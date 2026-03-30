<?php
require_once '../config/Database.php';
echo "<h2>Тестирование класса Database</h2>";
$db = new Database();

//1. Тестирование соединения с БД
echo "<h3>Подключение к базе данных</h3>";
try {
    $conn = $db->connect();
    if ($conn) {
        echo "Connected successfully.</br>";
    } else {
        echo "Connection failed";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}

//2. Тест создания таблицы и вставки данных
echo "<h3>Создание тестовой таблицы</h3>";
try {
    $sql_create_test_table = "CREATE TABLE IF NOT EXISTS test_table (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(30) NOT NULL
    )";
    $db->executeRun($sql_create_test_table);
    echo "Table 'test_table' created successfully.</br>";
    } catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage();
}
echo "<h3>Вставка данных в тестовую таблицу</h3>";
try {
    $sql_insert_test_data = "INSERT INTO test_table (name) VALUES ('Test Name')";
    $db->executeRun($sql_insert_test_data);
    echo "Data inserted successfully into 'test_table'.</br>";
} catch (Exception $e) {
    echo "Error inserting data: " . $e->getMessage();
}

//3. Тест получения данных
echo "<h3>Получение данных из тестовой таблицы</h3>";
try {

    $sql_select = "SELECT * FROM test_table";
    $rows = $db->getAll($sql_select);
    if ($rows) {
        echo "Data retrieved successfully from 'test_table':</br>";
        foreach ($rows as $row) {
            echo "ID: " . $row['id'] . " - Name: " . $row['name'] . "</br>";
        }
    } else {
        echo "No data found in 'test_table'.</br>";
    }

} catch (Exception $e) {
    echo "Error retrieving data: " . $e->getMessage();
}

//4. Тест удаления тестовой таблицы
echo "<h3>Удаление тестовой таблицы</h3>";
try {
    $sql_drop_table = "DROP TABLE IF EXISTS test_table";
    $db->executeRun($sql_drop_table);
    echo "Table 'test_table' dropped successfully.</br>";
} catch (Exception $e) {
    echo "Error dropping table: " . $e->getMessage();
}
