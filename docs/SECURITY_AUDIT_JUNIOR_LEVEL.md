# ArtPortal - Анализ безопасности (Junior уровень)

## Резюме
Проект имеет **серьезные уязвимости безопасности**, которые НЕ соответствуют требованиям к production-приложению. Однако, содержит хорошие примеры правильного использования защиты в некоторых местах.

---

## 🔴 КРИТИЧЕСКИЕ УЯЗВИМОСТИ

### 1. SQL Injection в models/Auth.php
**Файл:** [models/Auth.php](models/Auth.php) (строка 5)
**Статус:** ⚠️ КРИТИЧНАЯ УЯЗВИМОСТЬ

```php
// УЯЗВИМЫЙ КОД:
public static function findUserByEmail($email) {
    $sql='SELECT * FROM `users` WHERE `email` ="'.$email.'"';  // ❌ Конкатенация!
    $db = new Database();
    return $db->getOne($sql);
}
```

**Проблема:**
- Переменная `$email` напрямую подставляется в SQL-запрос
- Любой злоумышленник может ввести: `admin"--` и получить доступ
- Пример атаки: вход с email `" OR "1"="1` вернет первого пользователя

**Пример атаки:**
```
Email: admin@example.com" OR "1"="1
Password: anything

SQL запрос станет:
SELECT * FROM `users` WHERE `email` ="admin@example.com" OR "1"="1"

Вернет ВСЕХ пользователей!
```

**Правильный пример в том же файле:**
```php
// ПРАВИЛЬНО:
public static function getUserByID($id) {
    $sql = 'SELECT * FROM `users` WHERE `id` = ?';  // ✅ Prepared statement
    $db = new Database();
    return $db->getOne($sql, [$id]);
}
```

**Риск:** 
- Несанкционированный доступ к аккаунтам
- Утечка всех данных пользователей
- Возможность изменения или удаления данных
- **CVSS Score: 9.8 CRITICAL**

**Решение:** Заменить на prepared statement с параметром `?`

---

### 2. Отсутствие CSRF защиты
**Файл:** Все формы в `views/` и контроллеры
**Статус:** ⚠️ ВЫСОКИЙ РИСК

**Проблема:**
- Ни в одной форме нет CSRF токена
- Защищенные операции могут быть выполнены с других сайтов

**Пример уязвимости:**
```html
<!-- Форма в views/formLogin.php - БЕЗ защиты -->
<form method="POST" action="/artportal/login">
    <input type="email" name="email">
    <input type="password" name="password">
    <button type="submit">Login</button>
</form>

<!-- Злоумышленник создает на своем сайте: -->
<form action="https://yoursite.com/artportal/dashboard/updateAccount" method="POST">
    <input type="hidden" name="email" value="attacker@evil.com">
    <input type="hidden" name="username" value="hacked">
</form>
<!-- И показывает ее жертве - автоматически отправится! -->
```

**Затронутые операции:**
- Вход/выход
- Редактирование профиля
- Добавление/редактирование картин
- Изменение пароля
- Любые POST операции

**Риск:**
- Несанкционированное изменение данных пользователя
- Выполнение действий от имени пользователя
- **CVSS Score: 6.5 MEDIUM**

**Решение:** 
```php
// В form:
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    ...
</form>

// В контроллере:
if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die('CSRF token validation failed');
}
```

---

### 3. Проблема с аутентификацией в dashboard
**Файл:** [dashboard/index.php](dashboard/index.php)
**Статус:** ⚠️ СРЕДНИЙ РИСК

**Проблема:**
В `dashboard/index.php` сессия проверяется, но в публичном `index.php` нет проверки.

```php
// dashboard/index.php - ХОРОШО
if ($_SESSION['status'] !== 'admin') {
    session_destroy();
    header('Location: /artportal/login');
    exit;
}

// Но public pages - НЕТ ПРОВЕРКИ ролей
// Например, artistProfileForm доступен в public routing.php
```

**Риск:**
- Потенциальная путаница в управлении доступом
- Неправильное разделение ролей может привести к утечкам

---

## 🟡 ВЫСОКИЙ РИСК

### 4. Недостаточная валидация GET параметров в некоторых местах
**Файл:** [routes/routing.php](routes/routing.php)
**Статус:** ⚠️ СРЕДНИЙ РИСК

```php
// ХОРОШО - правильное типирование:
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;  // ✅ Приведение к int

// Но в других местах может быть слабее:
$search = trim((string)($_GET['search'] ?? ''));  // ✅ Хорошо

// Используется в SQL с ?:
$row = $db->getOne($query, [$like, $like, $like]);  // ✅ Правильно
```

**Хорошее:** Использование типирования и prepared statements
**Плохое:** Передача search напрямую в view без htmlspecialchars()

---

### 5. Отсутствие защиты от Broken Access Control (BAC)
**Файл:** [dashboard/controllers/HomeController.php](dashboard/controllers/HomeController.php) и другие
**Статус:** ⚠️ СРЕДНИЙ РИСК

```php
// ХОРОШО - проверка аутентификации:
public static function startDashboard() {
    self::requireAuth();  // ✅ Есть проверка
    // ...
}

// НО НЕТ ПРОВЕРКИ ТОГО, ЧТО ПОЛЬЗОВАТЕЛЬ РЕДАКТИРУЕТ СВОИ ДАННЫЕ
public static function updateAccount() {
    self::requireAuth();
    
    // Но что если $userId в $_POST отличается от $_SESSION['userId']?
    // Контроль доступа на уровне данных может быть слабым
    
    $userId = (int)($_POST['userId'] ?? 0);  // ⚠️ Опасно!
    // Нужно использовать (int)$_SESSION['userId']
}
```

**Проверка для картин в services:**
```php
// ХОРОШО:
$artistId = PaintingService::getArtistIdForUser($userId);
// Убедиться, что пользователь - художник
```

**Риск:**
- Пользователь может отредактировать чужие картины
- Может изменить чужой профиль
- **CVSS Score: 7.5 HIGH**

---

### 6. Логирование ошибок выводит информацию в БД
**Файл:** [config/Database.php](config/Database.php)
**Статус:** ⚠️ СРЕДНИЙ РИСК

```php
// В Database.php:
catch (PDOException $e) {
    error_log('Database query error: ' . $e->getMessage());  // ✅ Логируется
    throw new Exception('Database operation failed');          // ✅ Скрывает детали
}
```

**Хорошо:**
- Детали ошибки логируются, но не показываются пользователю
- Пользователь видит общее сообщение

**Но плохо:**
- Нет проверки, что файл error_log записывается в защищенное место
- Могут быть утечки информации в других местах

---

## 🟢 ХОРОШО РЕАЛИЗОВАНО

### 7. Правильное хеширование паролей ✅
**Файл:** [services/AuthService.php](services/AuthService.php)
**Статус:** ✅ ПРАВИЛЬНО

```php
// При регистрации:
password_hash($cleanData['password'], PASSWORD_DEFAULT)  // ✅ Использует bcrypt

// При проверке:
password_verify($password, $user['password'])  // ✅ Правильное сравнение
```

**Плюсы:**
- Использует надежный алгоритм bcrypt
- Невозможно обратное преобразование
- Защита от rainbow table атак

---

### 8. Защита от XSS в view файлах ✅
**Файл:** [views/partials/paintings.php](views/partials/paintings.php)
**Статус:** ✅ ПРАВИЛЬНО

```php
// ПРАВИЛЬНО - используется htmlspecialchars:
echo '<h5 class="card-title">' . htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8') . '</h5>';
// ✅ Все специальные символы экранированы

// Правильно обрабатывается поиск:
value="<?php echo htmlspecialchars($searchQuery ?? '', ENT_QUOTES, 'UTF-8'); ?>"
```

**Хорошее:**
- Используется `htmlspecialchars()` с флагами `ENT_QUOTES`
- Кодировка явно указана `UTF-8`
- Защита от XSS атак

**Примеры защищенных полей:**
```php
htmlspecialchars($value['title'], ENT_QUOTES, 'UTF-8')      // ✅
htmlspecialchars($value['artist_name'], ENT_QUOTES, 'UTF-8') // ✅
htmlspecialchars($value['category'] ?? 'Unknown', ...)      // ✅
```

---

### 9. Защита от Arbitrary File Upload ✅
**Файл:** [services/PaintingService.php](services/PaintingService.php)
**Статус:** ✅ ХОРОШО

```php
// 1. Проверка is_uploaded_file:
if (!is_uploaded_file($tmpName)) {
    $errors[] = 'Uploaded image is invalid';  // ✅
    return $existingImage;
}

// 2. Проверка расширения файла:
$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
    $errors[] = 'Image must be a JPG, PNG, GIF, or WEBP file';  // ✅
}

// 3. Проверка размера файла:
if ($fileSize <= 0) {
    $errors[] = 'Uploaded image is empty';  // ✅
}

// 4. Создание уникального имени:
$fileName = uniqid('painting_', true) . '.' . $extension;  // ✅

// 5. Проверка наличия директории:
if (!is_dir($directory)) {
    $errors[] = 'Upload directory is missing';  // ✅
}

// 6. MD5 хеширование для защиты от дубликатов:
$fileHash = md5_file($tmpName);  // ✅ Новое!
```

**Хорошее:**
- Проверка is_uploaded_file()
- Белый список расширений
- Уникальное имя файла (защита от перезаписи)
- Проверка размера

**Было бы еще лучше:**
- Проверка MIME-type через fileinfo
- Максимальный размер файла
- Защита от path traversal (уже в place с uniqid)

---

### 10. Управление сессиями ✅
**Файл:** [index.php](index.php)
**Статус:** ✅ ХОРОШО

```php
session_start();

$timeout = 900; // 15 минут

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    unset($_SESSION['userId']);
    unset($_SESSION['status']);
    unset($_SESSION['name']);
    unset($_SESSION['last_activity']);
    // Выход при неактивности
}

$_SESSION['last_activity'] = time();  // Обновление времени активности
```

**Хорошее:**
- Таймаут неактивности (15 минут)
- Правильное очищение данных сессии
- Обновление last_activity при каждом запросе

**Но:**
- Редирект закомментирован (должен быть)
- Нет защиты от session fixation

---

### 11. Правильное использование prepared statements в большинстве мест ✅
**Файл:** Большинство models/ и services/
**Статус:** ✅ ХОРОШО

```php
// ПРАВИЛЬНО используется везде кроме Auth::findUserByEmail():
$query = "SELECT * FROM `users` WHERE `id` = ?";
$db = new Database();
$db->getOne($sql, [$id]);  // ✅

// Правильно с типированием:
$db->getOne($query, [
    $cleanData['title'],
    $cleanData['description'],
    $cleanData['image'],
    (int)$id,
    (int)$_SESSION['userId'],
]);  // ✅
```

---

### 12. Валидация входных данных ✅
**Файл:** [services/AuthService.php](services/AuthService.php), [services/ArtistProfileService.php](services/ArtistProfileService.php)
**Статус:** ✅ ХОРОШО

```php
// Валидация email:
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';  // ✅
}

// Валидация пароля:
if (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters long';  // ✅
}

// Валидация названия (regex):
if (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
    $errors[] = 'Name can only contain letters, numbers, and underscores';  // ✅
}

// Валидация года:
if (!preg_match('/^\d{4}$/', $year_created)) {
    $errors[] = 'Year must be a 4-digit value';  // ✅
}

// Проверка длины данных:
if (mb_strlen($normalized['title']) > 255) {
    $errors[] = 'Title is too long';  // ✅
}
```

---

### 13. Правильное использование .env для конфигурации ✅
**Файл:** [config/Database.php](config/Database.php)
**Статус:** ✅ ХОРОШО

```php
require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Использование переменных окружения:
$this->host = $_ENV['DB_HOST'] ?? 'localhost';  // ✅
$this->user = $_ENV['DB_USER'] ?? 'root';       // ✅
$this->password = $_ENV['DB_PASSWORD'] ?? '';   // ✅
```

**Хорошее:**
- Чувствительные данные в .env
- Значения по умолчанию для разработки
- Не в исходном коде

---

## 📊 МАТРИЦА РИСКОВ

| Категория | Статус | Уровень | CVSS | Описание |
|-----------|--------|--------|------|----------|
| SQL Injection | ❌ УЯЗВИМ | CRITICAL | 9.8 | findUserByEmail в Auth.php |
| XSS | ✅ ЗАЩИЩЕН | - | - | htmlspecialchars везде |
| CSRF | ❌ НЕ ЗАЩИЩЕНО | HIGH | 6.5 | Все формы без токенов |
| Broken Access Control | ⚠️ СЛАБО | HIGH | 7.5 | Недостаточная проверка доступа |
| Arbitrary File Upload | ✅ ЗАЩИЩЕНО | - | - | Белый список, уникальные имена |
| Аутентификация | ✅ ХОРОШО | - | - | bcrypt, правильные проверки |
| Session Management | ✅ ХОРОШО | - | - | Таймаут, очистка |
| Валидация данных | ✅ ХОРОШО | - | - | filter_var, regex, length checks |
| Обработка ошибок | ✅ ХОРОШО | - | - | Логирование, скрытие деталей |

---

## 🔧 БЫСТРЫЕ ИСПРАВЛЕНИЯ (Priority)

### Priority 1 - СЕЙЧАС (CRITICAL)
1. **Исправить SQL Injection в Auth::findUserByEmail()**
```php
// Заменить:
$sql='SELECT * FROM `users` WHERE `email` ="'.$email.'"';

// На:
$sql = 'SELECT * FROM `users` WHERE `email` = ?';
$db = new Database();
return $db->getOne($sql, [$email]);
```

### Priority 2 - В БЛИЖАЙШЕЕ ВРЕМЯ (HIGH)
2. **Добавить CSRF защиту на все формы**
```php
// В session_start():
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// В каждую форму:
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// В каждый контроллер POST:
if ($_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    die('CSRF token invalid');
}
```

3. **Добавить проверку доступа для редактирования данных**
```php
// Вместо $userId из $_POST/GET:
$userId = (int)$_SESSION['userId'];  // ВСЕГДА из сессии!

// Для художников проверять artistId:
$artistId = PaintingService::getArtistIdForUser($userId);
if (!$artistId) {
    die('Not an artist');
}
```

### Priority 3 - УЛУЧШЕНИЯ (MEDIUM)
4. Добавить проверку MIME-type через fileinfo
5. Добавить максимальный размер файла (MAX_UPLOAD_SIZE)
6. Добавить логирование попыток доступа
7. Добавить rate limiting для попыток входа

---

## 📋 COMPLIANCE CHECKLIST (Junior Level)

| Требование | Статус | Примечание |
|-----------|--------|-----------|
| Защита от SQL Injection | ❌ | findUserByEmail уязвима |
| Защита от XSS | ✅ | htmlspecialchars везде |
| Защита от CSRF | ❌ | Отсутствует полностью |
| Безопасное хеширование паролей | ✅ | bcrypt используется |
| Управление сессиями | ✅ | Таймаут реализован |
| Контроль доступа | ⚠️ | Слабая проверка данных |
| Валидация входа | ✅ | Хорошо реализована |
| Безопасная загрузка файлов | ✅ | Белый список, уникальные имена |
| Защита от раскрытия информации | ✅ | Ошибки логируются, скрываются |
| Использование .env | ✅ | Чувствительные данные защищены |

---

## 📈 ОБЩАЯ ОЦЕНКА

**Уровень зрелости безопасности:** Junior + (с критическими пробелами)

**Что ПРАВИЛЬНО:**
- Использование PDO prepared statements (в большинстве мест)
- Хеширование паролей bcrypt
- Защита от XSS
- Валидация данных
- Управление сессиями
- Безопасная загрузка файлов

**Что НЕ ПРАВИЛЬНО:**
- SQL Injection в одном критическом месте
- Отсутствие CSRF защиты везде
- Слабая проверка доступа к данным
- Потенциальные проблемы с access control

**Для production нужно:**
1. Исправить критические уязвимости (SQL Injection)
2. Добавить CSRF защиту
3. Укрепить контроль доступа
4. Добавить логирование попыток доступа
5. Провести полный security audit

**Для Junior разработчика:**
Проект демонстрирует хорошее понимание базовых концепций безопасности, но есть опасные пробелы, которые нужно срочно закрыть перед публикацией.
