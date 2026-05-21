# PHPUnit — краткая шпаргалка

Файл: `phpunit.xml` в корне проекта управляет наборами тестов (suites).

## Установка
- Через Composer (в проекте):
```bash
composer require --dev phpunit/phpunit
```

## Запуск тестов
- Запустить все тесты:
```bash
./vendor/bin/phpunit
```
- Запустить конкретный набор (testsuite из phpunit.xml):
```bash
./vendor/bin/phpunit --testsuite=unit
./vendor/bin/phpunit --testsuite=integration
```
- Запустить один файл тестов:
```bash
./vendor/bin/phpunit tests/Unit/PriceCalculatorUnitTest.php
```
- Запустить конкретный тест-метод в файле:
```bash
./vendor/bin/phpunit --filter testCalculateFromPriceBasic tests/Unit/PriceCalculatorUnitTest.php
```
- Показать список доступных тестов (PHPUnit >= 10):
```bash
./vendor/bin/phpunit --list-tests
```

## Форматы вывода
- Читаемый вывод тестов (testdox):
```bash
./vendor/bin/phpunit --testdox
```
- JUnit XML (под CI): установить в `phpunit.xml` или:
```bash
./vendor/bin/phpunit --log-junit build/logs/junit.xml
```

## Покрытие кода (coverage)
- Требуется Xdebug или PCOV. Если видите "No code coverage driver available" — установите Xdebug.
- Текстовый отчёт покрытия (через phpdbg, не требует Xdebug для запуска):
```bash
phpdbg -qrr ./vendor/bin/phpunit --testsuite=unit --coverage-text
```
- HTML-отчёт покрытия (нужен Xdebug/PCOV для сбора):
```bash
./vendor/bin/phpunit --testsuite=unit --coverage-html coverage
# или с phpdbg
phpdbg -qrr ./vendor/bin/phpunit --testsuite=unit --coverage-html coverage
```

## Отладка и рекомендации
- Если PHPUnit ругается на отсутствие драйвера покрытия: установите Xdebug (рекомендуется) или PCOV.
- На Windows (XAMPP): скачайте DLL с https://xdebug.org/download под вашу версию PHP и добавьте в `php.ini`:
```
zend_extension = "C:\\xampp\\php\\ext\\php_xdebug.dll"
xdebug.mode = coverage
xdebug.start_with_request = yes
```
и перезапустите Apache/CLI.
- Для CLI-окружения убедитесь, что `php` в PATH — используйте `php -v` и `php -m | findstr xdebug`.

## Полезные флаги
- `--filter <pattern>` — запуск тестов, соответствующих шаблону
- `--stop-on-failure` — остановить при первом провале
- `--repeat <n>` — повторить тесты n раз (тестирование флексируемости)
- `--colors=always` — цветной вывод в CI/терминале

## Организация тестов
- Поместите unit-тесты в `tests/Unit`, integration — в `tests/Integration`.
- Используйте `phpunit.xml` для описания `testsuites`, bootstrap-файла и логов.
- В bootstrap подключайте минимально необходимые зависимости (`vendor/autoload.php`, конфиги).

## Быстрые команды для разработчика
- Запустить unit-тесты (локально):
```bash
./vendor/bin/phpunit --testsuite=unit
```
- Запустить integration-тесты:
```bash
phpdbg -qrr ./vendor/bin/phpunit --testsuite=integration
```
- Собрать HTML coverage (после включения Xdebug):
```bash
./vendor/bin/phpunit --coverage-html coverage
```

---
Сохрани файл рядом с проектом: `PHPUNIT-CHEATSHEET.md`. Коротко и по делу — если хочешь, добавлю примеры конфигурации `phpunit.xml` и шаблон `bootstrap.php`.