# Архитектурный паттерн: разделение SQL и бизнес-логики

## Проблема

При смешивании слоёв модель становится сложной для тестирования и неясно, что её обязанность — данные или решения.

```php
// ❌ НЕПРАВИЛЬНО: смешано SQL + логика
public static function saveUser($cleanData) {
    $db = new Database();
    
    // SQL (низкий уровень)
    $user = $db->getOne("SELECT * FROM users WHERE email = ?", [$cleanData['email']]);
    
    // Логика/решение (высокий уровень)
    if ($user) {
        return ['success' => false, 'errors' => ['Email exists already']];
    }
    
    // Ещё SQL
    $db->executeRun($query, $params);
    return [...];
}
```

## Решение: правильное разделение

### Уровень 1: Модель (Repository) — только SQL и простая семантика данных

Методы модели отвечают на вопрос: "Дай мне данные" или "Сохрани это".

```php
<?php
class Register {
    /**
     * Проверяет, существует ли пользователь с таким email
     * @param string $email
     * @param Database|null $db для тестов (мокирование)
     * @return bool
     */
    public static function existsByEmail($email, $db = null) {
        $db = $db ?? new Database();
        $user = $db->getOne('SELECT id FROM users WHERE email = ?', [$email]);
        return (bool)$user;
    }

    /**
     * Проверяет, существует ли пользователь с таким username
     */
    public static function existsByUsername($username, $db = null) {
        $db = $db ?? new Database();
        $user = $db->getOne('SELECT id FROM users WHERE username = ?', [$username]);
        return (bool)$user;
    }

    /**
     * Сохраняет новое пользователя в БД
     * @param array $cleanData {name, email, password_hash}
     * @param Database|null $db для тестов
     * @return int|false ID нового пользователя или false при ошибке
     */
    public static function insertUser(array $cleanData, $db = null) {
        $db = $db ?? new Database();
        
        $query = "INSERT INTO `users` (`username`, `email`, `password`, `role`, `created_at`) 
                  VALUES (?, ?, ?, 'user', NOW())";
        
        $params = [
            $cleanData['name'],
            $cleanData['email'],
            $cleanData['password'],
        ];
        
        try {
            $db->executeRun($query, $params);
            return (int)$db->getLastInsertId();
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
```

### Уровень 2: Сервис — бизнес-логика и решения

Сервис использует методы модели и решает: "Что делать, если...".

```php
<?php
class RegisterService {
    /**
     * Регистрация пользователя: валидация + проверка уникальности + сохранение
     */
    public static function register($data) {
        $errors = [];

        // Валидация формата (входные данные)
        if (!is_array($data) || empty($data)) {
            return ['success' => false, 'errors' => ['No data provided']];
        }

        $name = trim($data['name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';
        $confirm = $data['confirm'] ?? '';

        // Валидация полей
        if ($name === '') {
            $errors[] = 'Name is required';
        } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $name)) {
            $errors[] = 'Name can only contain letters, numbers, and underscores';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            $errors[] = 'Password must be at least 8 characters and contain at least one letter and one number';
        }
        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // ===== БИЗНЕС-ЛОГИКА: проверка уникальности =====
        
        // Используем МЕТОДЫ МОДЕЛИ для проверки (не прямой SQL!)
        if (Register::existsByEmail($email)) {
            return ['success' => false, 'errors' => ['Email exists already']];
        }

        if (Register::existsByUsername($name)) {
            return ['success' => false, 'errors' => ['Username exists already']];
        }

        // ===== БИЗНЕС-ЛОГИКА: сохранение =====
        
        $cleanData = [
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
        ];

        $userId = Register::insertUser($cleanData);

        if (!$userId) {
            return ['success' => false, 'errors' => ['Database error: Unable to save user']];
        }

        return ['success' => true, 'user' => ['id' => $userId, 'username' => $name, 'email' => $email]];
    }
}
?>
```

## Правило (золотое правило архитектуры)

```
┌─────────────────────────────────────────────────────────────┐
│                      СЕРВИС (Service)                       │
│  - Бизнес-логика (IF...THEN...ELSE)                         │
│  - Оркестровка (вызов нескольких методов модели)            │
│  - Формирование ответов и ошибок                            │
│  - Транзакции (если нужны)                                  │
└─────────────────────────────────────────────────────────────┘
                             ↓
┌─────────────────────────────────────────────────────────────┐
│                     МОДЕЛЬ (Repository)                     │
│  - Только SQL: SELECT, INSERT, UPDATE, DELETE               │
│  - Простые проверки наличия (existsBy...)                   │
│  - Методы возвращают данные или boolean                     │
│  - Optional Database $db = null для тестов                  │
└─────────────────────────────────────────────────────────────┘
                             ↓
┌─────────────────────────────────────────────────────────────┐
│                  DATABASE (DB Wrapper)                      │
│  - PDO обёртка: getOne(), getAll(), executeRun()           │
│  - Управление подключением                                  │
└─────────────────────────────────────────────────────────────┘
```

## Преимущества правильного разделения

1. **Тестируемость**: сервис можно тестировать, мокируя методы модели
   ```php
   $dbMock = $this->createMock(Database::class);
   $userId = Register::insertUser($data, $dbMock);
   ```

2. **Переиспользование**: один метод модели может использовать несколько сервисов
   ```php
   // Сервис A: регистрация пользователя
   if (Register::existsByEmail($email)) { ... }
   
   // Сервис B: проверка уникальности при изменении профиля
   if (Register::existsByEmail($newEmail)) { ... }
   ```

3. **Ясность**: видно, где данные, где правила
   ```php
   // Модель: "Вот метод для SELECT"
   existsByEmail($email)
   
   // Сервис: "Если email существует, вернуть ошибку"
   if (Register::existsByEmail($email)) { return error; }
   ```

4. **Легче менять бизнес-логику** без изменения SQL
   ```php
   // Если нужно: "Email существует, но если статус='pending', разрешить"
   if (Register::existsByEmail($email) && Register::getStatusByEmail($email) !== 'pending') {
       return error;
   }
   ```

## Чек-лист при рефакторинге модели

- [ ] Все SELECT → методы модели (existsBy..., getBy..., get...)
- [ ] Все INSERT/UPDATE/DELETE → методы модели (insert, update, delete)
- [ ] Все IF/THEN/ELSE → сервис
- [ ] Все CHECK данных перед сохранением (уникальность, формат) → сервис через методы модели
- [ ] Все формирования ответов/ошибок → сервис
- [ ] Все методы модели имеют optional `$db = null` для тестов
- [ ] Модель не знает о сервисе (зависимость идёт вверх, не вниз)

## Пример применения в твоём проекте

### Файлы, которые нужно рефакторить:

1. `models/Register.php` → split на `existsByEmail()`, `existsByUsername()`, `insertUser()`
2. `models/PurchaseRequest.php` → split на `insert()`, `getById()`, `getAllForArtist()` и вынести проверки в сервис
3. `models/Paintings.php` → split на `insert()`, `update()`, `delete()` и вынести коллекцию-логику в сервис
4. `models/Artists.php` → метод `approveArtist()` → вынести в сервис (там 2 UPDATE, нужна транзакция)

### Сервисы, которые нужно обогатить:

1. `services/RegisterService.php` → добавить проверки через Register::exists...()
2. `services/PaintingService.php` → добавить коллекцию-логику через Paintings::selectBy...()
3. `services/ArtistProfileService.php` → добавить approveArtist() с транзакцией
4. `services/PurchaseRequestService.php` → создать, добавить throttling/проверки

