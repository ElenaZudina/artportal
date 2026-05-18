<?php

/**
 * Admin Collection Controller - manages painting collections
 * Handles collection CRUD operations and artwork grouping
 */
class CollectionController {
    /**
     * List collections
     * Retrieves a list of all collections and renders the list view
     */
    public static function collectionsList() {
        $arr = Collections::getCollectionsList();
        include_once 'views/collections-list.php';
    }

    /**
     * Show create collection form
     * Renders form for adding a new collection
     */
    public static function create() {
        include_once 'views/collections-add-form.php';
    }

    /**
     * Handle collection creation
     * Processes POST data via CollectionService and re-renders form with result
     */
    public static function store() {
        $result = CollectionService::createCollection($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/collections-add-form.php';
    }

    /**
     * Show edit collection form
     * Loads collection data and renders edit form
     * @param int $id Collection identifier
     */
    public static function edit($id) {
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-edit-form.php';
    }

    /**
     * Update collection
     * Applies updates through CollectionService and re-renders edit form
     * @param int $id Collection identifier
     */
    public static function update($id) {
        $result = CollectionService::updateCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-edit-form.php';
    }

    /**
     * Show delete confirmation
     * Loads collection and renders a confirmation page
     * @param int $id Collection identifier
     */
    public static function delete($id) {
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-delete-form.php';
    }

    /**
     * Destroy collection
     * Deletes a collection via CollectionService and shows result
     * @param int $id Collection identifier
     */
    public static function destroy($id) {
        $result = CollectionService::deleteCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-delete-form.php';
    }

    /**
     * Handle AJAX request to create a collection
     * Expects POST data and returns JSON with creation result
     */
    public static function storeAjax() {
        // Ensure request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Create collection using the service layer
        $result = CollectionService::createCollection($_POST);

        header('Content-Type: application/json');

        if ($result['success']) {
            // Get collection ID returned by the service
            $collectionId = $result['id'] ?? null;

            if ($collectionId) {
                // Retrieve full collection data by ID
                $newCollection = Collections::getCollectionById($collectionId);

                if ($newCollection) {
                    echo json_encode([
                        'success' => true,
                        'id' => $newCollection['id'],
                        'title' => $newCollection['title'],
                        'message' => 'Collection created successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Collection created but could not retrieve data'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Collection created but ID not returned'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => $result['errorMessage'] ?? 'Unknown error'
            ]);
        }
        exit;
    }
}