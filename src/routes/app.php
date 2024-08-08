<?php
use App\Support\Route;

Route::get("/", "MonController@index");
Route::get("/register", "MonController@register");
Route::post("/create-user", "MonController@createUser");
Route::get("/login", "MonController@login");
Route::post("/connect", "MonController@connect");
Route::get("/logout", "MonController@logout");



