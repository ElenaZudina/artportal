<?php

class ExhibitionService {
    public static function createExhibition($data) {
        $title = trim($data['title'] ?? '');
        $description = trim($data['description'] ?? '');
        $collectionId = (int)($data['collection_id'] ?? 0);
        $startDate = trim($data['start_date'] ?? '');
        $endDate = trim($data['end_date'] ?? '');

        if ($title === '') {
            return ['success' => false, 'errorMessage' => 'Exhibition title is required'];
        }

        if ($collectionId <= 0) {
            return ['success' => false, 'errorMessage' => 'Please select a collection'];
        }

        if (!Collection::getCollectionById($collectionId)) {
            return ['success' => false, 'errorMessage' => 'Selected collection does not exist'];
        }

        if ($startDate === '' || $endDate === '') {
            return ['success' => false, 'errorMessage' => 'Start date and end date are required'];
        }

        if (strtotime($startDate) === false || strtotime($endDate) === false) {
            return ['success' => false, 'errorMessage' => 'Please provide valid dates'];
        }

        if (strtotime($startDate) > strtotime($endDate)) {
            return ['success' => false, 'errorMessage' => 'Start date cannot be later than end date'];
        }

        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        if (Exhibitions::existsByTitle($title)) {
            return ['success' => false, 'errorMessage' => 'Exhibition already exists'];
        }

        if (!Exhibitions::create($title, $description, $collectionId, $startDate, $endDate)) {
            return ['success' => false, 'errorMessage' => 'Database error while adding exhibition'];
        }

        return ['success' => true, 'errorMessage' => null];
    }
}