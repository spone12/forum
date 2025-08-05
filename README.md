### Описание

>Основные возможности сайта:

+ **Профиль**:
    + Создание профиля, аутентификация
    + Внесение информации о пользователе (описание, аватар итд)
    + API взаимодейтсвия

+ **Новости**:
    + Добавление, удаление, изменение
    + Вывод графика просмотров новостей
    + Голосование (рейтинг)
    + Добавление изображений
    + Комментарии (Скоро)
    + Пагинация
  
+ **Чат**:
    + Обмен сообщениями с пользователями в реальном времени (websockets)
    + Уведомления

+ **Мультиязычность (доступные языки на данный момент)**:
    + Русский
    + English

+ **Поиск по сайту**
+ **Разграничение прав доступа**

## <blockquote>Установка</blockquote>
<ul>
    <li><b>PHP: "^8.2"</b></li>
    <li><b>Laravel: "^10.48"</b></li>
    <li><b>MySQL: "^5.6"</b></li>
    <li><b>Apache: "^2.2"</b></li>
</ul>
<div>
    <div><blockquote>Необходимые программы:</blockquote></div>
    <ul>
        <li>Любой локальный веб-сервер, например, <b>Open Server</b></li>
        <li><b>Git</b></li>
        <li><b>Node.js</b></li>
        <li><b>Composer</b></li>
    </ul>
</div>

><i>Начало установки:</i>

1. Клонировать репозиторий: <code>git clone https://github.com/spone12/forum.git</code>
2. Перейти в папку с репозиторием: `cd forum/`
3. Выполнить установку необходимых зависимостей: `composer install`
4. Установить пакеты NodeJs: `npm install`
5. Создать базу данных с предпочитаемой кодировкой, например, `utf8_general_ci`
6. Создать файл конфигурации в каталоге репозитория `.env` на основе файла `.env.example`
   + Заполнить конфигурацию файла:
         `DB_DATABASE, DB_USERNAME, DB_PASSWORD` и другие при необходимости
7. Выполнить накатку миграции БД с сидерами: `php artisan migrate:refresh --seed`
8. Создать символичную ссылку на хранилище `php artisan storage:link`
9. Сгенерировать ключ приложения `php artisan key:generate`

<div>
<b>Очистка кэша:</b>

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

</div>

>SASS компиляция: <code>npm run watch (dev)</code>

>PHP Unit запуск тестов: <code>vendor\bin\phpunit</code>

>Websockets:

**Local (по умолчанию)**

1. Константы:
   + Файл `.env.example` полностью настроен для локальной работы сокетов, заполнить по подобию `Websockets константы`, указанные ниже в файле `.env` 
2. Пересобрать локальное окружение `npm run dev`
3. Очистить кэш приложения `php artisan config:cache`
4. Запустить локальный сервер `php artisan websockets:serve`

**Pusher**

1. Создать канал на сайте [Pusher](https://pusher.com/ "Pusher") и сгенерировать API ключи
2. Добавить сгенерированные ключи в файл конфигурации `.env`
3. Зайти в файл конфигурации `resources/js/bootstrap.js`
   + Закомментировать  // For local websockets
   + Раскомментировать // For another service websockets
4. Зайти в файл конфигурации `config/broadcasting.php`
    + Закомментировать  // For local websockets
    + Раскомментировать // For another service websockets
5. Пересобрать локальное окружение `npm run dev`
6. Очистить кэш приложения `php artisan config:cache`

**Websockets константы**

   + BROADCAST_DRIVER=pusher
   + APP_URL - доменное имя
   + PUSHER_APP_HOST
   + PUSHER_APP_PORT
   + PUSHER_APP_ID
   + PUSHER_APP_KEY
   + PUSHER_APP_SECRET

>Swagger API документация: `{host}/api/documentation`
