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

## Установка
#<b>Версия PHP-7.2 +, Apache-2.2 +, MySQL-5.6 +</b>

<p>Для установки для начала выполните клонирование репозитория: <code>git clone https://github.com/spone12/forum.git</code></p>

<div>
    <p>Далее установите <b>Node.js</b></p>
    <p>В терминале войдите в папку с проектом и выполните команду <code>npm install</code></p>
    <p>Выполните глобальную установку SASS<code>npm install -g node-sass</code></p>
    <p>Для компиляции SASS используется команда <code>npm run watch (dev)</code></p>
</div>

<p>Запустите любой локальный веб-сервер, например, Open Server</p>

<p>Создайте базу данных с кодировкой utf8_general_ci</p>

<p>Перейдите в корень проекта и в файле .env заполните поля DB_DATABASE=**,DB_USERNAME=**,DB_PASSWORD=** (Имя базы данных, логин и пароль)</p>

<p>Далее в консоли выполните команду <code>php artisan migrate:refresh --seed</code> для создания миграций с сидерами</p>

<div>
    <div><strong>Ngrok</strong></div>
    <p><b> Path: </b> App\Http\Integrations\ngrok.exe
    <p><b>Description: </b> делает ваш локальный сервер доступным всему интернету по специальному HTTPS адресу</p>
    <p><b>In console: </b><code>ngrok http 80</code></p>
</div>