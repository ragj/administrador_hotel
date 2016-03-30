<?php

// EJEMPLO
// $router->addRoute(array(
//   'path'     => '/place/view/{uri}',
//   'get'      => array('Place', 'view'),
//   'post'      => array('Place', 'view'),
// ));

$router->addRoute(array(
    'path' => '/',
    'get' => array('Plain', 'home')
));

$router->addRoute(array(
    'path' => '/about-us',
    'get' => array('Plain', 'about')
));

$router->addRoute(array(
    'path' => '/hotel-collection',
    'post' => array('Plain', 'hotel')
));

$router->addRoute(array(
    'path' => '/hotel-collection/{hotel}',
    'get' => array('Plain', 'hotel')
));

$router->addRoute(array(
    'path' => '/experience',
    'get' => array('Plain', 'experience')
));

$router->addRoute(array(
    'path' => '/experience/{exper}',
    'get' => array('Plain', 'experience')
));

$router->addRoute(array(
    'path' => '/transfer',
    'get' => array('Plain', 'transfer')
));

$router->addRoute(array(
    'path' => '/contact-us',
    'get' => array('Plain', 'contact')
));

$router->addRoute(array(
    'path' => '/panel/hotel/add',
    'get' => array('Hotel', 'add')
));





?>