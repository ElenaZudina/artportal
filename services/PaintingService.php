<?php

require_once __DIR__ . '/../services/VisionAIService.php';
require_once __DIR__ . '/../models/PaintingTags.php';
require_once __DIR__ . '/../models/Tags.php';

/**
 * Painting Service - handles painting operations
 * Manages painting creation, updates, deletion, and vision AI tagging
 */
class PaintingService {
    /**
     * Get the artist profile ID for a user account.
     * @param int $userId User ID
     * @return int|null Artist ID or null when no profile exists
     */
    private static function getArtistIdForUser(int $userId) {
        $artist = Artists::getArtistByUserId($userId);
        return $artist['id'] ?? null;
    }

    /**
     * Delete a painting image file from disk when it exists.
     * @param string|null $fileName Image filename
     * @return void
     */
    private static function deleteImageFile(?string $fileName) {
        if ($fileName === null || $fileName === '') {
            return;
        }
        $filePath = __DIR__ . '/../images/paintings/' . $fileName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    /**
     * Resolve the painting image value from an upload, existing image, or legacy field.
     * Validates image upload metadata and stores the uploaded file.
     * @param array $data Form data with legacy image field
     * @param array $files Uploaded files from $_FILES
     * @param string|null $existingImage Existing image filename if updating
     * @param array $errors Error messages array passed by reference
     * @param string|null $fileHash Uploaded file hash passed by reference
     * @return string|null Saved filename, existing filename, or null
     */
    private static function resolveImageValue(array $data, array $files, ?string $existingImage = null, array &$errors = [], ?string &$fileHash = null) {
        $upload = $files['image_file'] ?? null;

        if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)($upload['tmp_name'] ?? '');
            $originalName = (string)($upload['name'] ?? '');
            $fileSize = (int)($upload['size'] ?? 0);

            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                $errors[] = 'Uploaded image is invalid';
                return $existingImage;
            }

            if ($fileSize <= 0) {
                $errors[] = 'Uploaded image is empty';
                return $existingImage;
            }

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
                $errors[] = 'Image must be a JPG, PNG, GIF, or WEBP file';
                return $existingImage;
            }

            $directory = __DIR__ . '/../images/paintings';
            if (!is_dir($directory)) {
                $errors[] = 'Upload directory is missing';
                return $existingImage;
            }

            // Calculate the MD5 file hash before saving.
            $fileHash = md5_file($tmpName);

            $fileName = uniqid('painting_', true) . '.' . $extension;
            $targetPath = $directory . '/' . $fileName;
            if (!move_uploaded_file($tmpName, $targetPath)) {
                $errors[] = 'Failed to save uploaded image';
                return $existingImage;
            }

            return $fileName;
        }

        if ($existingImage !== null) {
            return $existingImage;
        }

        $legacyImage = trim((string)($data['image'] ?? ''));
        return $legacyImage !== '' ? $legacyImage : null;
    }

    /**
     * Validate common painting form fields and normalize them.
     * @param array $data Painting form data
     * @param array $normalized Normalized form data passed by reference
     * @param array $errors Validation errors passed by reference
     * @return void
     */
    private static function validateCommonData(array $data, array &$normalized, array &$errors) {
        $normalized = [
            'title' => trim((string)($data['title'] ?? '')),
            'description' => trim((string)($data['description'] ?? '')),
            'year_created' => trim((string)($data['year_created'] ?? '')),
            'category_id' => (int)($data['category_id'] ?? 0),
            'medium' => trim((string)($data['medium'] ?? '')),
            'dimensions' => trim((string)($data['dimensions'] ?? '')),
            'price' => trim((string)($data['price'] ?? '')),
        ];

        if ($normalized['title'] === '') {
            $errors[] = 'Title is required';
        } elseif (mb_strlen($normalized['title']) > 255) {
            $errors[] = 'Title is too long';
        }

        if ($normalized['description'] === '') {
            $errors[] = 'Description is required';
        }

        if ($normalized['year_created'] === '' || !preg_match('/^\d{4}$/', $normalized['year_created'])) {
            $errors[] = 'Year must be a 4-digit value';
        }

        if ($normalized['category_id'] <= 0 || !Categories::getCategoryByID($normalized['category_id'])) {
            $errors[] = 'Category is required';
        }

        if ($normalized['medium'] === '') {
            $errors[] = 'Medium is required';
        } elseif (mb_strlen($normalized['medium']) > 255) {
            $errors[] = 'Medium is too long';
        }

        if ($normalized['dimensions'] === '') {
            $errors[] = 'Dimensions are required';
        } elseif (mb_strlen($normalized['dimensions']) > 100) {
            $errors[] = 'Dimensions are too long';
        }

        if ($normalized['price'] === '' || !is_numeric($normalized['price']) || (float)$normalized['price'] < 0) {
            $errors[] = 'Price must be a valid positive number';
        }
    }

    /**
     * Rebuild AI-generated tag links for a painting image.
     * @param int $paintingId Painting ID
     * @param string $imagePath Absolute image path
     * @return void
     */
    private static function rebuildTagsForPainting(int $paintingId, string $imagePath): void {
        $visionService = new VisionAIService();
        $response = $visionService->detectLabels($imagePath);
        $tags = $visionService->buildTags($response);

        PaintingTags::detachByPaintingId($paintingId);

        if (empty($tags)) {
            return;
        }

        foreach ($tags as $tagName) {
            $tagId = Tags::getOrCreateTag($tagName);
            PaintingTags::attach($paintingId, $tagId);
        }
    }

    /**
     * Create a painting for the current artist and generate AI tags.
     * @param array $data Painting form data
     * @param array $files Uploaded files
     * @param int $userId Current user ID
     * @return array Success status with saved data or validation errors
     */
    public static function createPainting(array $data, array $files, int $userId) {
        $errors = [];
        $normalized = [];
        $fileHash = null;

        $artistId = PaintingService::getArtistIdForUser($userId);
        if (!$artistId) {
            return ['success' => false, 'errors' => ['Artist profile not found'], 'data' => []];
        }

        PaintingService::validateCommonData($data, $normalized, $errors);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $image = PaintingService::resolveImageValue($data, $files, null, $errors, $fileHash);
        if ($image === null || $image === '') {
            $errors[] = 'Image is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        // Check whether another painting already uses the same uploaded file.
        if ($fileHash) {
            $existingPainting = Paintings::getPaintingByFileHash($fileHash);
            if ($existingPainting) {
                // Delete the uploaded duplicate file.
                PaintingService::deleteImageFile($image);
                return [
                    'success' => false, 
                    'errors' => ['This image has already been uploaded. Please use a different image.'], 
                    'data' => $normalized
                ];
            }
        }

        $cleanData = [
            'title' => $normalized['title'],
            'description' => $normalized['description'],
            'image' => $image,
            'year_created' => (int)$normalized['year_created'],
            'category_id' => $normalized['category_id'],
            'artist_id' => (int)$artistId,
            'medium' => $normalized['medium'],
            'dimensions' => $normalized['dimensions'],
            'price' => (float)$normalized['price'],
        ];

        if ($fileHash) {
            $cleanData['file_hash'] = $fileHash;
        }

        $paintingId = Paintings::insertPainting($cleanData);
        
        if (!$paintingId) {
            return ['success' => false, 'errors' => ['Database error while adding painting'], 'data' => $normalized];
        }

        $visionService = new VisionAIService();

        $response = $visionService->detectLabels(
        __DIR__ . '/../images/paintings/' . $cleanData['image']
        );

        $tags = $visionService->buildTags($response);
        if (empty($tags)) {
            return ['success' => true, 'errors' => [], 'data' => $cleanData];
        }
        foreach ($tags as $tagName) {
           $tagId = Tags::getOrCreateTag($tagName);
           PaintingTags::attach($paintingId, $tagId);
        }

        return ['success' => true, 'errors' => [], 'data' => $cleanData];

    }

    /**
     * Update an artist-owned painting and refresh AI tags when the image changes.
     * @param int $id Painting ID
     * @param array $data Painting form data
     * @param array $files Uploaded files
     * @param int $userId Current user ID
     * @return array Success status with saved data or validation errors
     */
    public static function updatePainting(int $id, array $data, array $files, int $userId) {
        $errors = [];
        $normalized = [];
        $fileHash = null;

        $artistId = PaintingService::getArtistIdForUser($userId);
        if (!$artistId) {
            return ['success' => false, 'errors' => ['Artist profile not found'], 'data' => []];
        }

        $painting = Paintings::getPaintingByID($id);
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artistId) {
            return ['success' => false, 'errors' => ['Painting not found'], 'data' => []];
        }

        PaintingService::validateCommonData($data, $normalized, $errors);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $image = PaintingService::resolveImageValue($data, $files, $painting['image'] ?? null, $errors, $fileHash);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        // If a new file was uploaded, check for duplicates.
        if ($fileHash && $image !== $painting['image']) {
            $existingPainting = Paintings::getPaintingByFileHash($fileHash);
            if ($existingPainting && (int)$existingPainting['id'] !== $id) {
                // Delete the uploaded duplicate file.
                PaintingService::deleteImageFile($image);
                return [
                    'success' => false, 
                    'errors' => ['This image has already been uploaded. Please use a different image.'], 
                    'data' => $normalized
                ];
            }
        }

        $cleanData = [
            'title' => $normalized['title'],
            'description' => $normalized['description'],
            'image' => $image,
            'year_created' => (int)$normalized['year_created'],
            'category_id' => $normalized['category_id'],
            'artist_id' => (int)$artistId,
            'medium' => $normalized['medium'],
            'dimensions' => $normalized['dimensions'],
            'price' => (float)$normalized['price'],
        ];

        if ($fileHash && $image !== $painting['image']) {
            $cleanData['file_hash'] = $fileHash;
        }

        if (!Paintings::updatePainting($id, $cleanData)) {
            return ['success' => false, 'errors' => ['Database error while updating painting'], 'data' => $normalized];
        }

        if ($image !== $painting['image']) {
            PaintingService::rebuildTagsForPainting($id, __DIR__ . '/../images/paintings/' . $cleanData['image']);
        }

        // Delete old image file if a new one was uploaded
        if ($image !== $painting['image'] && $painting['image'] !== null) {
            PaintingService::deleteImageFile($painting['image']);
        }

        return ['success' => true, 'errors' => [], 'data' => $cleanData];
    }

    /**
     * Delete an artist-owned painting and its image file.
     * @param int $id Painting ID
     * @param int $userId Current user ID
     * @return array Success status with errors if deletion fails
     */
    public static function deletePainting(int $id, int $userId) {
        $artistId = PaintingService::getArtistIdForUser($userId);
        if (!$artistId) {
            return ['success' => false, 'errors' => ['Artist profile not found']];
        }

        $painting = Paintings::getPaintingByID($id);
        if (!$painting || (int)($painting['artist_id'] ?? 0) !== (int)$artistId) {
            return ['success' => false, 'errors' => ['Painting not found']];
        }

        if (!Paintings::deletePainting($id, $artistId)) {
            return ['success' => false, 'errors' => ['Database error while deleting painting']];
        }

        // Delete image file when painting is deleted
        if ($painting['image'] !== null) {
            PaintingService::deleteImageFile($painting['image']);
        }

        return ['success' => true, 'errors' => []];
    }
}
