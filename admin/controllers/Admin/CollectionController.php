<?php

class CollectionController {
    public static function collectionsList() {
        $arr = Collections::getCollectionsList();
        include_once 'views/collections-list.php';
    }

    public static function create() {
        include_once 'views/collections-add-form.php';
    }

    public static function store() {
        $result = CollectionService::createCollection($_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        include_once 'views/collections-add-form.php';
    }

    public static function edit($id) {
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-edit-form.php';
    }

    public static function update($id) {
        $result = CollectionService::updateCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-edit-form.php';
    }

    public static function delete($id) {
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-delete-form.php';
    }

    public static function destroy($id) {
        $result = CollectionService::deleteCollection($id, $_POST);
        $test = $result['success'];
        $errorMessage = $result['errorMessage'];
        $collection = Collections::getCollectionByID($id);
        include_once 'views/collections-delete-form.php';
    }

    // Обработка AJAX-запроса для создания коллекции
    public static function storeAjax() {
        // Проверяем, что это POST-запрос
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Создаем коллекцию через service
        $result = CollectionService::createCollection($_POST);

        header('Content-Type: application/json');

        if ($result['success']) {
            // Получаем ID из результата Service
            $collectionId = $result['id'] ?? null;

            if ($collectionId) {
                // Получаем полные данные коллекции по ID
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