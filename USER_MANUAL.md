Руководство пользователя — ArtPortal

Общие примечания

URL: сайт доступен по базовому пути /artportal.
Навигация: используйте верхнее меню и ссылки в карточках; большинство действий — клики по кнопкам/ссылкам или формы с полями.
Вход/сессия: войти можно через  /artportal/login; после входа система перенаправит в админку (/artportal/admin/startAdmin) для админа или в дашборд (/artportal/dashboard/startDashboard) для обычного пользователя/художника.
Статусы: аккаунт имеет status (active или blocked). Заблокированный пользователь не может войти.
1. Публичная часть (для всех посетителей)

Главная страница: откройте /artportal — видна лента выставок и подборки.
Просмотр всех картин: кликните «All» или перейдите на /artportal/all — список работ.
Фильтр по категории: нажмите на категорию на странице или откройте /artportal/category?id={id}.
Просмотр карточки картины: кликните на изображение/заголовок картины — откроется /artportal/paintings?id={id}.
Список художников: откройте /artportal/artists. Для просмотра профиля художника нажмите «View» у нужного художника (/artportal/artist?id={id}).
Выставки: список выставок /artportal/exhibitions. Детали текущей выставки — /artportal/current-exhibition?id={id}.
Регистрация: откройте /artportal/registerForm, заполните поля и нажмите «Register» (отправит на /artportal/registerAnswer).
Вход: /artportal/login — введите email и пароль, нажмите «Log in» (отправка на /artportal/auth).
Заявка на покупку: на странице картины заполните форму покупки и нажмите «Submit» — форма POST в /artportal/purchase-request.
Избранное: на карточке картины нажмите «Add to favorite»/«Toggle favorite» — действие отправляет запросы к /artportal/add-to-favorite, /artportal/remove-from-favorite, /artportal/toggle-favorite.
2. Дашборд пользователя (обычный пользователь)

Доступ: после входа пользователь попадает на /artportal/dashboard/startDashboard.
Профиль: нажмите «Profile» или /artportal/dashboard/profile — просмотрите личные данные.
Аккаунт (данные): «Account» → /artportal/dashboard/account — кнопка «Edit account» ведёт на /artportal/dashboard/edit-account; заполните поля и нажмите «Update» (отправляется на /artportal/dashboard/update-account).
Смена пароля: откройте /artportal/dashboard/change-password, заполните текущий и новый пароли, нажмите «Update password» (/artportal/dashboard/update-password).
Мои картины (если пользователь — художник): /artportal/dashboard/my-paintings — кнопки «Add painting» → /artportal/dashboard/add-painting; заполните форму и нажмите «Save» (/artportal/dashboard/store-painting). Редактирование/удаление через соответствующие ссылки с параметром id.
Избранное: /artportal/dashboard/my-favorites — список ваших избранных.
Мои запросы / покупки: /artportal/dashboard/my-requests и /artportal/dashboard/purchase-requests.
Интерактивные шаги (пример добавления картины):

Нажмите «Add painting» → заполняете поля: Title, Description, Image, Category, Price → нажмите «Save» → при успехе вы вернётесь к списку ваших картин.
3. Дашборд художника (роль artist)

Роль автоматически выставляется при наличии профиля художника, после входа перенаправление — /artportal/dashboard/startDashboard.
Профиль художника: заполните Artist profile через /artportal/artistProfileForm, загрузите изображение, биографию и т.д., затем сохраните (/artportal/artistProfileSave).
Публикация картин: как в разделе «Дашборд пользователя» — добавляете картины, указываете medium, dimensions, price. После добавления запись появляется в публичной части (если автор подтверждён модератором).
Управление выставками/коллекциями: (частично доступно через админку или через коллекции при поддержке) — художник может предлагать материалы, но основное управление — в админке.
4. Админка (роль admin)

Доступ: /artportal/admin/startAdmin после входа админа. Админский интерфейс защищён Auth::requireSession('admin').
Панель администратора: на стартовой странице видны KPI: Artists, Collections, Exhibitions, Users, Categories.
Пользователи: откройте /artportal/admin/users — список пользователей. Чтобы заблокировать/разблокировать:
Найдите пользователя в списке — используйте поле поиска.
В строке пользователя выберите Block/Unblock (или форму статуса) и нажмите «Save» — POST отправляется на /artportal/admin/user-status с id и status.
Ограничение: вы не можете изменить статус своего аккаунта; админские аккаунты защищены.
Категории: /artportal/admin/categories — добавление (/create-category → /store-category), редактирование (/edit-category?id= → /result-edit-category?id=), удаление (/delete-category?id= → /result-delete-category?id=).
Коллекции: /artportal/admin/collections — создание через форму или модальное окно; есть AJAX-эндпоинт /artportal/admin/store-collection-ajax (возвращает JSON) для быстрого создания из модального окна.
Выставки: /artportal/admin/exhibitions — добавление (/create-exhibition → /store-exhibition), редактирование, удаление (POST на /result-edit-exhibition?id= и /result-delete-exhibition?id=).
Модерация художников: /artportal/admin/moderation-artists — просмотр заявок на профиль художника, кнопки Approve (/approve-artist?id=) / Reject (/reject-artist?id=).
Управление картинами: добавление/редактирование/удаление через админские формы (add, paintingAddResult, paintingEdit?id=, paintingEditResult, paintingDel?id=, paintingDelResult?id=).
Интерактивный пример — блокировка пользователя:

В админке откройте Users → найдите пользователя → в столбце статуса выберите blocked → нажмите «Save» → система выполнит POST в /artportal/admin/user-status. После этого пользователь больше не сможет войти.
5. Восстановление пароля (ручной процесс)

В публичной форме /artportal/forgot-password введите email и нажмите «Send request» → форма POST в /artportal/forgot-password-request.
Система отправит письмо админу (ADMIN_EMAIL из .env) через EmailService::sendPasswordResetRequestToAdmin. Админ вручную свяжется с пользователем и, при необходимости, сбросит пароль через админский инструмент или обновит в БД.
6. Частые действия и подсказки

Проверить статус аккаунта: если не можете войти, админ мог изменить status на blocked. Обратитесь к администратору.
Mailtrap / тест почты: локально письма проходят через Mailtrap (см. .env — MAILTRAP_*). Для тестов используйте почтовый ящик Mailtrap.
Работа с изображениями: при загрузке изображений убедитесь, что поле image заполнено; при больших файлах проверяйте настройки PHP (upload_max_filesize и post_max_size).
AJAX-операции: некоторые быстрые формы (например, создание коллекции в модальном окне) используют AJAX и возвращают JSON; в случае ошибки откройте DevTools → вкладку Network чтобы увидеть ответ.
7. Безопасность и ограничения

Не редактируйте руками роль admin.
Бэкап базы: перед массовыми действиями делайте бэкап БД; для локальной разработки используйте дамп art_portal.sql.
Хеширование паролей: пароли хранятся в хеше (bcrypt). При ручном изменении пароля используйте password_hash().