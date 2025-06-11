<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dasbor::index');
$routes->get('duit/pengeluaran', 'Duit::daftarPengeluaran');
$routes->get('duit/pemasukan', 'Duit::daftarPemasukan');
$routes->get('duit/hapus/(:segment)/(:num)', 'Duit::hapus/$1/$2');
$routes->get('duit/edit/(:segment)/(:num)', 'Duit::edit/$1/$2');
$routes->post('duit/update', 'Duit::update');