<?php

/**
 * Artist Profile Service - handles artist profile creation and validation
 * Manages profile data validation, file upload processing, and database operations
 */
class ArtistProfileService {
    private const MAX_PICTURE_SIZE = 5242880;
    
    /**
     * Helper method to process uploaded profile picture
     * Validates file type, size, and saves to artist images directory
     * Uses unique filename with uniqid to prevent collisions
     * @param array $data Form data with legacy picture field
     * @param array $files Uploaded files from $_FILES
     * @param string|null $existingPicture Existing picture filename if updating
     * @param array $errors Error messages array (passed by reference)
     * @return string|null Saved filename or null if no valid file
     */
    private static function resolvePictureValue(array $data, array $files, ?string $existingPicture = null, array &$errors = []) {
        $upload = $files['picture_file'] ?? null;

        if (is_array($upload) && ($upload['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)($upload['tmp_name'] ?? '');
            $originalName = (string)($upload['name'] ?? '');
            $fileSize = (int)($upload['size'] ?? 0);

            if ($fileSize <= 0) {
                $errors[] = 'Uploaded picture is empty';
                return $existingPicture;
            }

            if ($fileSize > self::MAX_PICTURE_SIZE) {
                $errors[] = 'Uploaded picture must not exceed 5 MB';
                return $existingPicture;
            }

            if ($tmpName === '' || !is_uploaded_file($tmpName)) {
                $errors[] = 'Uploaded picture is invalid';
                return $existingPicture;
            }

            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
                $errors[] = 'Picture must be a JPG, PNG, GIF, or WEBP file';
                return $existingPicture;
            }

            $directory = __DIR__ . '/../images/artists';
            if (!is_dir($directory)) {
                $errors[] = 'Upload directory is missing';
                return $existingPicture;
            }

            $fileName = uniqid('artist_', true) . '.' . $extension;
            $targetPath = $directory . '/' . $fileName;
            if (!move_uploaded_file($tmpName, $targetPath)) {
                $errors[] = 'Failed to save uploaded picture';
                return $existingPicture;
            }

            return $fileName;
        }

        if ($existingPicture !== null) {
            return $existingPicture;
        }

        $legacyPicture = trim((string)($data['picture'] ?? ''));
        return $legacyPicture !== '' ? $legacyPicture : null;
    }

    /**
     * Create or update artist profile
     * Validates all profile data, processes file uploads, saves to database
     * Returns success status and validation errors if any
     * @param array $data Profile form data
     * @param array $files Uploaded files
     * @param int $userId User ID for profile owner
     * @return array Success status with user message or errors
     */
    public static function createProfile($data, $files, $userId, $db = null) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $normalized = [
            'name' => trim((string)($data['name'] ?? '')),
            'location' => trim((string)($data['location'] ?? '')),
            'birth_date' => trim((string)($data['birth_date'] ?? '')),
            'bio' => trim((string)($data['bio'] ?? '')),
            'picture' => trim((string)($data['picture'] ?? '')),
        ];

        if ($normalized['name'] === '') {
            $errors[] = 'Name is required';
        } elseif (mb_strlen($normalized['name']) > 255) {
            $errors[] = 'Name is too long';
        }

        if ($normalized['location'] === '') {
            $errors[] = 'Location is required';
        } elseif (mb_strlen($normalized['location']) > 100) {
            $errors[] = 'Location is too long';
        }

        if ($normalized['birth_date'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $normalized['birth_date'])) {
            $errors[] = 'Birth date must be in YYYY-MM-DD format';
        }

        if ($normalized['bio'] !== '' && mb_strlen($normalized['bio']) > 65535) {
            $errors[] = 'Bio is too long';
        }

        $existing = Artists::getArtistByUserId((int)$userId, $db);
        if ($existing) {
            $errors[] = 'Artist profile already exists for this user';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $picture = self::resolvePictureValue($data, $files, null, $errors);
        if ($picture !== null && mb_strlen($picture) > 255) {
            $errors[] = 'Picture filename is too long';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $cleanData = [
            'name' => $normalized['name'],
            'location' => $normalized['location'],
            'birth_date' => $normalized['birth_date'] !== '' ? $normalized['birth_date'] : null,
            'bio' => $normalized['bio'] !== '' ? $normalized['bio'] : null,
            'picture' => $picture,
            'status' => 'pending',
            'user_id' => (int)$userId,
        ];

        $saved = Artists::insertArtistProfile($cleanData, $db);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error: Unable to create artist profile'], 'data' => $normalized];
        }

        return ['success' => true, 'data' => $cleanData];
    }

    /**
     * Update an existing artist profile.
     * Validates profile data, processes optional file uploads, and saves changes.
     * @param array $data Profile form data
     * @param array $files Uploaded files
     * @param int $userId User ID for profile owner
     * @return array Success status with updated data or validation errors
     */
    public static function updateProfile($data, $files, $userId, $db = null) {
        $errors = [];

        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $existing = Artists::getArtistByUserId((int)$userId, $db);
        if (!$existing) {
            return ['success' => false, 'errors' => ['Artist profile not found']];
        }

        $normalized = [
            'name' => trim((string)($data['name'] ?? '')),
            'location' => trim((string)($data['location'] ?? '')),
            'birth_date' => trim((string)($data['birth_date'] ?? '')),
            'bio' => trim((string)($data['bio'] ?? '')),
            'picture' => trim((string)($data['picture'] ?? '')),
        ];

        if ($normalized['name'] === '') {
            $errors[] = 'Name is required';
        } elseif (mb_strlen($normalized['name']) > 255) {
            $errors[] = 'Name is too long';
        }

        if ($normalized['location'] === '') {
            $errors[] = 'Location is required';
        } elseif (mb_strlen($normalized['location']) > 100) {
            $errors[] = 'Location is too long';
        }

        if ($normalized['birth_date'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $normalized['birth_date'])) {
            $errors[] = 'Birth date must be in YYYY-MM-DD format';
        }

        if ($normalized['bio'] !== '' && mb_strlen($normalized['bio']) > 65535) {
            $errors[] = 'Bio is too long';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $picture = self::resolvePictureValue($data, $files, $existing['picture'] ?? null, $errors);
        if ($picture !== null && mb_strlen($picture) > 255) {
            $errors[] = 'Picture filename is too long';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $cleanData = [
            'name' => $normalized['name'],
            'location' => $normalized['location'],
            'birth_date' => $normalized['birth_date'] !== '' ? $normalized['birth_date'] : null,
            'bio' => $normalized['bio'] !== '' ? $normalized['bio'] : null,
            'picture' => $picture,
            'status' => $existing['status'] ?? 'pending',
            'user_id' => (int)$userId,
        ];

        $saved = Artists::updateArtistProfile($cleanData, (int)$userId, $db);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error: Unable to update artist profile'], 'data' => $normalized];
        }

        return ['success' => true, 'data' => $cleanData];
    }
}
