<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Core pages
$routes->get('/login', 'Pages::login');
$routes->get('/dashboard', 'Pages::dashboard');
$routes->get('/utilisateurs', 'Pages::utilisateurs');
$routes->get('/formulaire', 'Pages::formulaire');

// Notes and students
$routes->get('/etudiants', 'Pages::etudiants');
$routes->get('/notes/ajout', 'Notes::ajout');
$routes->get('/notes/semestres', 'Notes::semestres');
$routes->get('/notes/releve', 'Notes::releve');
