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
$routes->get('billing/dashboard', 'Billing::index', ['filter' => 'auth']);
$routes->get('billing/compute', 'Billing::computeTool', ['filter' => 'auth']);
$routes->get('billing/clients/create', 'Billing::createClient', ['filter' => 'auth']);
$routes->post('billing/clients', 'Billing::storeClient', ['filter' => 'auth']);
$routes->get('billing/compute/(:num)', 'Billing::compute/$1', ['filter' => 'auth']);
$routes->post('billing/compute/(:num)', 'Billing::storeCompute/$1', ['filter' => 'auth']);
$routes->post('billing/compute-preview', 'Billing::previewCompute', ['filter' => 'auth']);
$routes->get('billing/history', 'Billing::history', ['filter' => 'auth']);
$routes->get('billing/history/(:num)', 'Billing::billDetail/$1', ['filter' => 'auth']);
$routes->get('billing/action-trails', 'Billing::actionTrails', ['filter' => 'auth']);
