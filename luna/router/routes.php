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

?>