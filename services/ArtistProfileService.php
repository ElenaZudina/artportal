<?php

class ArtistProfileService {
    public static function createProfile($data, $userId) {
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

        if ($normalized['picture'] !== '' && mb_strlen($normalized['picture']) > 255) {
            $errors[] = 'Picture filename is too long';
        }

        $existing = Artists::getArtistByUserId((int)$userId);
        if ($existing) {
            $errors[] = 'Artist profile already exists for this user';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors, 'data' => $normalized];
        }

        $cleanData = [
            'name' => $normalized['name'],
            'location' => $normalized['location'],
            'birth_date' => $normalized['birth_date'] !== '' ? $normalized['birth_date'] : null,
            'bio' => $normalized['bio'] !== '' ? $normalized['bio'] : null,
            'picture' => $normalized['picture'] !== '' ? $normalized['picture'] : null,
            'status' => 'pending',
            'user_id' => (int)$userId,
        ];

        $saved = Artists::insertArtistProfile($cleanData);
        if (!$saved) {
            return ['success' => false, 'errors' => ['Database error: Unable to create artist profile'], 'data' => $normalized];
        }

        return ['success' => true, 'data' => $cleanData];
    }
}
