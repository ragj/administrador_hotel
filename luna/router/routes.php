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
    'get' => array('Plain', 'hotel')
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

///Hotel Routes
$router->addRoute(array(
    'path' => '/panel/hotel/add',
    'get' => array('Hotel', 'add'),
    'post' => array('Hotel', 'add')
));

$router->addRoute(array(
    'path' => '/panel/hotel/show',
    'get' => array('Hotel', 'show'),
));

$router->addRoute(array(
    'path' => '/panel/hotel/edit/{hotel}',
    'get' => array('Hotel', 'edit'),
    'post' => array('Hotel', 'edit')
));

$router->addRoute(array(
    'path' => '/panel/hotel/delete/{hotel}',
    'get' => array('Hotel', 'delete')
));
///HotelImages Routes
$router->addRoute(array(
    'path' => '/panel/hotel/addImages',
    'get' => array('Hotel', 'addImages'),
    'post' => array('Hotel', 'addImages')
));

$router->addRoute(array(
    'path' => '/panel/hotel/showImages',
    'get' => array('Hotel', 'showImages')
));

$router->addRoute(array(
    'path' => '/panel/hotel/editImages/{hotel}',
    'get' => array('Hotel', 'editImages'),
    'post' => array('Hotel', 'editImages')
));

$router->addRoute(array(
    'path' => '/panel/hotel/deleteImages/{hotel}',
    'get' => array('Hotel', 'deleteImages')
));


///Tour Routes
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

///TourImages Routes
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