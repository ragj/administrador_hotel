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

$router->addRoute(array(
    'path' => '/panel/tour/add',
    'get' => array('Tour', 'add'),
    'post'=>array('Tour','add')
));

$router->addRoute(array(
    'path' => '/panel/tour/show',
    'get' => array('Tour', 'show')
));

$router->addRoute(array(
    'path' => '/panel/tour/edit/{exper}',
    'get' => array('Tour', 'edit'),
    'post' => array('Tour', 'edit')
));

$router->addRoute(array(
    'path' => '/panel/tour/delete/{exper}',
    'get' => array('Tour', 'delete')
));

$router->addRoute(array(
    'path' => '/panel/tour/delete',
    'get' => array('Tour', 'show')
));

$router->addRoute(array(
    'path' => '/panel/tour/addImages',
    'get' => array('Tour', 'addImages'),
    'post' => array('Tour', 'addImages')
));

$router->addRoute(array(
    'path' => '/panel/tour/showImages',
    'get' => array('Tour', 'showImages')
));

$router->addRoute(array(
    'path' => '/panel/tour/editImages/{exper}',
    'get' => array('Tour', 'editImages'),
    'post' => array('Tour', 'editImages')
));

$router->addRoute(array(
    'path' => '/panel/tour/deleteImages/{exper}',
    'get' => array('Tour', 'deleteImages')
));








?>