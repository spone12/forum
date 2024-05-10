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
    <li><b>PHP: "^7.3"</b></li>
    <li><b>Laravel: "^8.83"</b></li>
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

<blockquote><i>Начало установки:</i></blockquote>

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

<div>
    <blockquote>SASS компиляция: <code>npm run watch (dev)</code></blockquote>
</div>

<div>
    <blockquote>PHP Unit запуск тестов: <code>vendor\bin\phpunit</code> </blockquote>
</div>

<div>
    <blockquote>Websocket Pusher:</blockquote>
</div>

1. Создать канал на сайте [Pusher](https://pusher.com/ "Pusher") и сгенерировать API ключи
2. Добавить сгенерированные ключи в файл `.env`
