<?php
/**
 * UnifiedNest ERP - Bootstrap File
 */

// Load configuration
require_once __DIR__ . '/app/config/config.php';

// Load required libraries
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Router.php';

// Initialize router
$router = Router::getInstance();

// Define routes

// Auth routes
$router->get('/', 'AuthController@login');
$router->get('/login', 'AuthController@login');
$router->post('/login/process', 'AuthController@processLogin');
$router->get('/register', 'AuthController@register');
$router->post('/register/process', 'AuthController@processRegister');
$router->get('/logout', 'AuthController@logout');
$router->get('/onboarding', 'AuthController@onboarding');
$router->post('/onboarding/process', 'AuthController@processOnboarding');

// Dashboard routes
$router->get('/dashboard', 'DashboardController@index');
$router->get('/profile', 'DashboardController@profile');
$router->post('/dashboard/update-profile', 'DashboardController@updateProfile');

// Organization routes (super-admin)
$router->get('/organizations', 'OrganizationController@index');
$router->get('/organizations/create', 'OrganizationController@create');
$router->post('/organizations/store', 'OrganizationController@store');
$router->get('/organizations/edit/([0-9]+)', 'OrganizationController@edit');
$router->post('/organizations/update/([0-9]+)', 'OrganizationController@update');
$router->get('/organizations/delete/([0-9]+)', 'OrganizationController@delete');

// Department routes (organization-owner, super-admin)
$router->get('/departments', 'DepartmentController@index');
$router->get('/departments/create', 'DepartmentController@create');
$router->post('/departments/store', 'DepartmentController@store');
$router->get('/departments/edit/([0-9]+)', 'DepartmentController@edit');
$router->post('/departments/update/([0-9]+)', 'DepartmentController@update');
$router->get('/departments/delete/([0-9]+)', 'DepartmentController@delete');

// Project routes
$router->get('/projects', 'ProjectController@index');
$router->get('/projects/create', 'ProjectController@create');
$router->post('/projects/store', 'ProjectController@store');
$router->get('/projects/viewProject/([0-9]+)', 'ProjectController@viewProject');
$router->get('/projects/edit/([0-9]+)', 'ProjectController@edit');
$router->post('/projects/update/([0-9]+)', 'ProjectController@update');
$router->get('/projects/delete/([0-9]+)', 'ProjectController@delete');
$router->post('/projects/addTask/([0-9]+)', 'ProjectController@addTask');
$router->post('/projects/updateTaskStatus/([0-9]+)', 'ProjectController@updateTaskStatus');
$router->post('/projects/uploadDocument/([0-9]+)', 'ProjectController@uploadDocument');
$router->get('/projects/deleteDocument/([0-9]+)', 'ProjectController@deleteDocument');

// Dispatch the router
$router->dispatch(); 