<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('login', 'Auth::loginForm');
$routes->post('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

$routes->get('admin', 'Home::admin', ['filter' => 'auth']);
$routes->get('admin/users', 'AdminUsers::index', ['filter' => 'auth']);
$routes->get('billing', 'Home::billing', ['filter' => 'auth']);
