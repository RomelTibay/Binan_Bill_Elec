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
$routes->get('admin/users/create', 'AdminUsers::create', ['filter' => 'auth']);
$routes->post('admin/users', 'AdminUsers::store', ['filter' => 'auth']);
$routes->get('admin/users/edit/(:num)', 'AdminUsers::edit/$1', ['filter' => 'auth']);
$routes->post('admin/users/update/(:num)', 'AdminUsers::update/$1', ['filter' => 'auth']);
$routes->get('admin/users/delete/(:num)', 'AdminUsers::confirmDelete/$1', ['filter' => 'auth']);
$routes->post('admin/users/delete/(:num)', 'AdminUsers::destroy/$1', ['filter' => 'auth']);
$routes->get('admin/audit-logs', 'AdminUsers::auditLogs', ['filter' => 'auth']);

$routes->get('billing', 'Home::billing', ['filter' => 'auth']);
