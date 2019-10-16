<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


/** @var TYPE_NAME $router */
$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('users', 'UsersController@index');
    /**
     * This block if for users route
     */
    $router->post('users', 'UsersController@index');
    $router->post('users/signin', 'UsersController@signin');
    $router->post('users/signup', 'UsersController@signup');
    $router->post('users/profile', 'UsersController@showDetails');
    $router->post('users/profile/setpersonnal', 'UsersController@setPersonal');
    $router->post('users/profile/setjob', 'UsersController@setJobInformation');
    $router->post('users/profile/newpass', 'UsersController@changePassword');


    $router->post('users/newpass', 'UsersController@changePassword');
    $router->post('users/usersnear', 'UsersController@showUsersNear');
    $router->post('users/usersprofile', 'UsersController@showProfile');
});
/*
$router->group(['prefix' => 'api'], function($router) {
    /**
     * This block if for users route
     *
    $router->get('users', 'UsersController@index');
    $router->get('users/signup', 'UsersController@signup');
    $router->get('users/signin', 'UsersController@signin');
    $router->get('users/profile', 'UsersController@showDetails');
    $router->get('users/usersnear', 'UsersController@showUsersNear');
    $router->get('users/usersprofile', 'UsersController@showProfile');
});*/
