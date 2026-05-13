# File Hash Configuration - Система защиты от дубликатов картин

## Описание
Система использует MD5 хэширование файлов для предотвращения загрузки одинаковых изображений разными художниками.

## Компоненты системы

### 1. База данных - paintings table
**Колонка которая нужна:**
```sql
ALTER TABLE `paintings` ADD COLUMN `file_hash` char(32) DEFAULT NULL COMMENT 'MD5 hash of uploaded image file' AFTER `image`;

-- Опционально для оптимизации запросов:
ALTER TABLE `paintings` ADD INDEX `idx_file_hash` (`file_hash`);
```

**Поле:**
- `file_hash` (char(32), nullable) - MD5 хеш файла изображения

---

### 2. Model - models/Paintings.php

#### Метод: getPaintingByFileHash()
```php
public static function getPaintingByFileHash($fileHash) {
    if (empty($fileHash)) {
        return null;
    }
    $query = "SELECT id, title, image FROM paintings WHERE file_hash = ? LIMIT 1";
    $db = new Database();
    return $db->getOne($query, [$fileHash]);
}
```
**Назначение:** Поиск существующей картины по MD5 хешу файла
**Возвращает:** массив с id, title, image или null

#### Метод: insertPainting()
**Изменения:** 
- Проверяет наличие `file_hash` в $cleanData
- При наличии хеша включает его в INSERT запрос
- При отсутствии хеша (NULL) пропускает колонку

#### Метод: updatePainting()
**Изменения:**
- Проверяет наличие `file_hash` в $cleanData
- При наличии нового хеша обновляет его в БД
- При отсутствии хеша не трогает существующее значение

---

### 3. Service - services/PaintingService.php

#### Метод: resolveImageValue()
**Параметр:** `&$fileHash` (передается по ссылке)
**Логика:**
```php
$fileHash = md5_file($tmpName);  // Вычисляем хеш перед загрузкой
move_uploaded_file($tmpName, $destination);
```

#### Метод: createPainting()
**Проверка дубликатов:**
```php
$existingPainting = Paintings::getPaintingByFileHash($fileHash);
if ($existingPainting && $fileHash) {
    // Удаляем загруженный файл
    $this->deleteImageFile($cleanData['image']);
    return [
        'success' => false,
        'error' => 'This image has already been uploaded. Please use a different image.'
    ];
}
```
**Добавление хеша в сохранение:**
```php
if ($fileHash) {
    $cleanData['file_hash'] = $fileHash;
}
```

#### Метод: updatePainting()
**Проверка дубликатов (только для нового изображения):**
```php
$painting = Paintings::getPaintingByID($id);
$image = $painting['image'] ?? '';

if ($fileHash && $image !== $painting['image']) {
    // Проверяем только если выбрано новое изображение
    $existingPainting = Paintings::getPaintingByFileHash($fileHash);
    if ($existingPainting) {
        $this->deleteImageFile($cleanData['image']);
        return [
            'success' => false,
            'error' => 'This image has already been uploaded. Please use a different image.'
        ];
    }
}
```

---

## Workflow: Загрузка картины

```
1. Художник выбирает изображение
   ↓
2. resolveImageValue() вычисляет MD5 хеш
   md5 = md5_file($tmpName)
   ↓
3. createPainting() получает хеш
   ↓
4. Проверка дубликата: getPaintingByFileHash(md5)
   ↓
   ├─ Найден дубликат → Ошибка "This image has already been uploaded"
   │  + Удаляем загруженный файл
   │  + Возвращаем сообщение об ошибке
   │
   └─ Дубликат не найден → Продолжаем
      ↓
5. Вставляем запись с file_hash в БД
   ↓
6. Картина успешно загружена
```

---

## Workflow: Редактирование картины

```
1. Художник редактирует картину
   ↓
2. Если выбрано новое изображение:
   - Вычисляем MD5 новой картины
   ↓
3. updatePainting() получает новый хеш
   ↓
4. Проверка: это новое изображение или старое?
   │
   ├─ Старое изображение (не изменилось)
   │  └─ Не проверяем на дубликаты
   │
   └─ Новое изображение
      ↓
      Проверка дубликата: getPaintingByFileHash(new_hash)
      ↓
      ├─ Найден дубликат → Ошибка
      │  + Удаляем загруженный файл
      │  + Возвращаем сообщение об ошибке
      │
      └─ Дубликат не найден → Продолжаем
         ↓
      5. Обновляем запись с новым file_hash
         ↓
      6. Картина успешно обновлена
```

---

## Сообщения об ошибках

**При обнаружении дубликата:**
```
"This image has already been uploaded. Please use a different image."
```

**Где отображается:**
- В PaintingService методы createPainting() и updatePainting()
- Возвращается в массиве: `['success' => false, 'error' => '...']`

---

## Что нужно сделать:

1. ✅ Код PHP полностью готов (Paintings.php + PaintingService.php)
2. ⚠️ **ТРЕБУЕТСЯ:** Добавить колонку `file_hash` в БД (вручную в phpMyAdmin)
3. ✅ Система автоматически начнет работать после добавления колонки

---

## SQL команды для добавления в БД:

```sql
-- Добавляем колонку
ALTER TABLE `paintings` ADD COLUMN `file_hash` char(32) DEFAULT NULL COMMENT 'MD5 hash of uploaded image file' AFTER `image`;

-- Опционально: индекс для быстрого поиска
ALTER TABLE `paintings` ADD INDEX `idx_file_hash` (`file_hash`);
```

---

## Примечания

- **Хеш вычисляется:** перед загрузкой файла через `md5_file($tmpName)`
- **Хеш хранится:** в виде строки из 32 символов (шестнадцатеричные цифры)
- **Проверка:** происходит ДО сохранения файла в папку `/images/paintings/`
- **Удаление:** если найден дубликат, загруженный файл удаляется, чтобы не оставлять мусор
- **Безопасность:** работает корректно с файлами разного размера и имени, проверяет только содержимое
