<p align="center">
    <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel Logo">\
    <img class="hidden ml-5 sm:block" src="https://laravel.com/img/logotype.min.svg" alt="Laravel">
</p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## Установка ( Installation )
#<b>Version PHP-7.3 +, Apache-2.2 +, MySQL-5.6 +</b>

<div>
    <div> <i><blockquote>Required programms:</blockquote></i> </div>
    <ul>
        <li>Любой локальный веб-сервер, например, <b>Open Server</b></li>
        <li><b>Git</b></li>
        <li><b>Node.js</b></li>
    </ul>
</div>

<p>Для установки для начала выполните клонирование репозитория: <code>git clone https://github.com/spone12/forum.git</code>
</p>

<div>
    <p>Перейдите в папку с репозиторием <code>cd forum</code> и выполните установку необходимых зависимостей <code>composer install</code></p>
    <p>Далее установите зависимости NodeJs, выполнив команду <code>npm install</code></p>
    <p>Выполните глобальную установку SASS <code>npm install -g node-sass</code></p>
    <p>Создайте базу данных с кодировкой utf8_general_ci</p>
    <p>Перейдите в корень проекта и в файле .env заполните поля DB_DATABASE=**,DB_USERNAME=**,DB_PASSWORD=** (Имя базы данных, логин и пароль)</p>
    <p>Далее в консоли выполните команду <code>php artisan migrate:refresh --seed</code> для создания миграций с сидерами</p>
    <p>Прописать alias на /folder_site/public </p>
    <p>
        <ul><b>При необходимости выполнить команды:</b>
            <li>Перегенерировать ключ <code>php artisan key:generate</code></li>
            <li><code>php artisan route:clear</code></li>
            <li><code>php artisan config:clear</code></li>
            <li><code>php artisan cache:clear</code></li>
        </ul>
    </p>
</div>

<div>
    <div><strong>Ngrok</strong></div>
    <p><b> Path: </b> App\Http\Integrations\ngrok.exe</p>
    <p><b>Description: </b> делает ваш локальный сервер доступным всему интернету по специальному HTTPS адресу</p>
    <p><b>In console: </b>
        <div><code>ngrok http 80</code></div>
        <div><code>ngrok http -host-header=rewrite localhost:80</code></div>
    </p>
</div>

<div>
    <div><strong>SASS</strong></div>
    <p><b>In console: </b>
        <div>sass compilation: <code>npm run watch (dev)</code></div>
    </p>
</div>

<div>
    <div>Запуск <b>tests PhpUnit</b>:</div>
    <code> vendor\bin\phpunit </code>
</div>
