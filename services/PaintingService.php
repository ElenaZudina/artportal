<?php

class PaintingService {
    private static function getArtistIdForUser(int $userId) {
        $artist = Artists::getArtistByUserId($userId);
        return $artist['id'] ?? null;
    }

    private static function deleteImageFile(?string $fileName) {
        if ($fileName === null || $fileName === '') {
            return;
        }
        $filePath = __DIR__ . '/../images/paintings/' . $fileName;
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    private static function resolveImageValue(array $data, array $files, ?string $existingImage = null, array &$errors = []) {
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

    public static function createPainting(array $data, array $files, int $userId) {
        $errors = [];
        $normalized = [];

        $artistId = PaintingService::getArtistIdForUser($userId);
        if (!$artistId) {
            return ['success' => false, 'errors' => ['Artist profile not found'], 'data' => []];
        }

        PaintingService::validateCommonData($data, $normalized, $errors);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $image = PaintingService::resolveImageValue($data, $files, null, $errors);
        if ($image === null || $image === '') {
            $errors[] = 'Image is required';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
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

        if (!Paintings::insertPainting($cleanData)) {
            return ['success' => false, 'errors' => ['Database error while adding painting'], 'data' => $normalized];
        }

        return ['success' => true, 'errors' => [], 'data' => $cleanData];
    }

    public static function updatePainting(int $id, array $data, array $files, int $userId) {
        $errors = [];
        $normalized = [];

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

        $image = PaintingService::resolveImageValue($data, $files, $painting['image'] ?? null, $errors);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
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

        if (!Paintings::updatePainting($id, $cleanData)) {
            return ['success' => false, 'errors' => ['Database error while updating painting'], 'data' => $normalized];
        }

        // Delete old image file if a new one was uploaded
        if ($image !== $painting['image'] && $painting['image'] !== null) {
            PaintingService::deleteImageFile($painting['image']);
        }

        return ['success' => true, 'errors' => [], 'data' => $cleanData];
    }

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