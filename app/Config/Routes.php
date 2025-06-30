<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('signin', 'Auth::signin');
$routes->get('signup', 'Auth::signup');
$routes->post('auth/login', 'Auth::login');
$routes->post('auth/register', 'Auth::register');
$routes->get('auth/logout', 'Auth::logout');
$routes->post('auth/forgot-password', 'Auth::forgotPassword');

// Dashboard routes
$routes->get('dashboard', 'Dashboard::index');
$routes->get('dashboard/main', 'Dashboard::main');
$routes->get('dashboard/silos', 'JsonSilo::index');
$routes->get('dashboard/silos/create', 'JsonSilo::create');
$routes->post('dashboard/silos/upload', 'JsonSilo::upload');
$routes->post('dashboard/silos/save', 'JsonSilo::save');
$routes->get('dashboard/silos/edit/([a-zA-Z0-9\-]+)', 'JsonSilo::edit/$1');
$routes->post('dashboard/silos/update/([a-zA-Z0-9\-]+)', 'JsonSilo::update/$1');
$routes->post('dashboard/silos/delete/([a-zA-Z0-9\-]+)', 'JsonSilo::delete/$1');
$routes->get('dashboard/silos/view/([a-zA-Z0-9\-]+)', 'JsonSilo::view/$1');
$routes->get('dashboard/silos/download/([a-zA-Z0-9\-]+)', 'JsonSilo::download/$1');

// API Management routes
$routes->get('dashboard/api', 'ApiManager::index');
$routes->get('dashboard/api/docs', 'ApiManager::docs');
$routes->post('dashboard/api/create', 'ApiManager::create');
$routes->post('dashboard/api/update-domain-lock', 'ApiManager::updateDomainLock');
$routes->post('dashboard/api/revoke/(:num)', 'ApiManager::revoke/$1');
$routes->get('dashboard/api/details/(:num)', 'ApiManager::details/$1');

// Settings routes
$routes->get('dashboard/settings', 'Settings::index');
$routes->post('dashboard/settings/change-password', 'Settings::changePassword');
$routes->post('dashboard/settings/change-email', 'Settings::changeEmail');
$routes->post('dashboard/settings/delete-account', 'Settings::deleteAccount');
$routes->get('dashboard/settings/stats', 'Settings::getStats');

// Admin routes (requires admin privileges)
$routes->get('admin', 'Admin::index');
$routes->get('admin/users', 'Admin::users');
$routes->get('admin/user/([a-zA-Z0-9]+)', 'Admin::userDetails/$1');
$routes->get('admin/live-requests', 'Admin::liveRequests');
$routes->get('admin/live-requests/data', 'Admin::getLiveRequestData');
$routes->post('admin/updateUserType', 'Admin::updateUserType');
$routes->post('admin/updateUserLimits', 'Admin::updateUserLimits');
$routes->post('admin/deleteUser', 'Admin::deleteUser');
$routes->post('admin/deleteUserFile', 'Admin::deleteUserFile');
$routes->post('admin/editUserFile', 'Admin::editUserFile');

// System Backup routes (Admin only)
$routes->get('backup', 'BackupController::index');
$routes->get('backup/download', 'BackupController::download');
$routes->post('backup/import', 'BackupController::import');
$routes->get('backup/stats', 'BackupController::stats');

// Public API v1 routes
$routes->group('api/v1', ['namespace' => 'App\Controllers'], function($routes) {
    // API info and health endpoints
    $routes->get('info', 'ApiController::info');
    $routes->get('health', 'ApiController::health');
    
    // CORS preflight support for all API endpoints
    $routes->options('info', 'ApiController::options');
    $routes->options('health', 'ApiController::options');
    $routes->options('([a-zA-Z0-9]+)', 'ApiController::options');
    $routes->options('([a-zA-Z0-9]+)/([a-zA-Z0-9\-]+)', 'ApiController::options');
    $routes->options('([a-zA-Z0-9]+)/([a-zA-Z0-9\-]+)/raw', 'ApiController::options');
    
    // User JSON files endpoints
    $routes->get('([a-zA-Z0-9]+)', 'ApiController::getUserFiles/$1');
    $routes->get('([a-zA-Z0-9]+)/([a-zA-Z0-9\-]+)', 'ApiController::getJsonFile/$1/$2');
    $routes->get('([a-zA-Z0-9]+)/([a-zA-Z0-9\-]+)/raw', 'ApiController::getRawJson/$1/$2');
});

// Legacy routes for compatibility
$routes->get('login', 'Auth::signin');
$routes->get('register', 'Auth::signup');
