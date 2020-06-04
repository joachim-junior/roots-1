<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'pages/view';
$route['posts/index'] = 'posts/index';
$route['posts/create'] = 'posts/create';
$route['posts/update'] = 'posts/update';
$route['posts/(:any)'] = 'posts/view/$1';
$route['users/profile/(:any)'] = 'users/profile/$1';
$route['users/upload/(:any)'] = 'users/upload/$1';
$route['users/fetch'] = 'users/fetch';
$route['users/avatar'] = 'users/avatar';
$route['messages/send_message'] = 'messages/send_message';
$route['posts'] = 'posts/index';
$route['countries'] = 'countries/index';
$route['countries/create'] = 'countries/create';
$route['countries/posts/(:any)'] = 'countries/posts/$1';
$route['(:any)'] = 'pages/view/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
