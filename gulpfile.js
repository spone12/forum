const { src, dest, parallel, series, watch} = require('gulp');
var elixir = require('laravel-elixir');
var shell = require("gulp-shell");

elixir(function(mix) {
    mix.sass("resources/sass/app.scss");
});