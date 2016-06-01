<?php

// EJEMPLO
// $router->addRoute(array(
//   'path'     => '/place/view/{uri}',
//   'get'      => array('Place', 'view'),
//   'post'      => array('Place', 'view'),
// ));

global $ROUTES;
//Transaleted routes
$ROUTES = [
	"/" => [
		'es' => '/inicio',
		'en' => '/home',
		'allow' => true,
		'path' => '/',
		'get' => array('Plain', 'home'),
	],
	"/about-us" => [
		'es' => "/nosotros",
		'en' => "/about-us",
		'allow' => true,
		'path' => '/about-us',
		'get' => array('Plain', 'about'),
	],
	"/hotel-collection" => [
		'es' => "/hoteles",
		'en' => "/hotel-collection",
		'allow' => true,
		'path' => '/hotel-collection',
		'get' => array('Plain', 'hotel'),
	],
	"/hotel-collection/{hotel}" => [
		'es' => "/hoteles/{hotel}",
		'en' => "/hotel-collection/{hotel}",
		'allow' => true,
		'path' => '/hotel-collection/{hotel}',
		'get' => array('Plain', 'hotel'),
	],
	"/experience" => [
		'es' => "/experiencia",
		'en' => "/experience",
		'allow' => true,
		'path' => '/experience',
		'get' => array('Plain', 'experience'),
	],
	"/experience/{exper}" => [
		'es' => "/experiencia/{exper}",
		'en' => "/experience/{exper}",
		'allow' => true,
		'path' => '/experience/{exper}',
		'get' => array('Plain', 'experience'),
	],
	"/transfer" => [
		'es' => "/travelAgent",
		'en' => "/travelAgent",
		'path' => '/transfer',
		'rol'=>'common',
		'get' => array('Plain', 'transfer'),
	],
	"/hTransfer" => [
		'es' => "/travelAgent/{uid}",
		'en' => "/travelAgent/{uid}",
		'path' => '/transfer/{uid}',
		'rol'=>'common',
		'get' => array('Plain', 'hotelTransfer'),
	],
	"/contact-us" => [
		'es' => "/contactanos",
		'en' => "/contact-us",
		'allow' => true,
		'path' => '/contact-us',
		'get' => array('Plain', 'contact'),
		'post' => array('Plain', 'contact'),
	],
	"/forgot" => [
		'es' => "/olividada",
		'en' => "/forgot",
		'allow' => true,
		'path' => '/forgot',
		'get' => array('Plain', 'forgot'),
		'post' => array('Plain', 'forgot'),
	],
	"/forgot/{uid}" => [
		'es' => "/olvidada/{uid}",
		'en' => "/forgot/{uid}",
		'allow' => true,
		'path' => '/forgot/{uid}',
		'get' => array('Plain', 'change'),
	],
	"/register" => [
		'es' => "/registro",
		'en' => "/register",
		'allow' => true,
		'path' => '/register',
		'get' => array('Plain', 'register'),
		'post' => array('Plain', 'register'),
	],
	"/aviso" => [
		'path' => '/aviso',
		'allow' => true,
		'get' => array('Plain', 'aviso'),
	],
	"/panel/hotel/add" => [
		'path' => '/panel/hotel/add',
		'get' => array('Hotel', 'add'),
		'post' => array('Hotel', 'add'),
	],
	"/login" => [
		'es' => '/login',
		'en' => '/login',
		'path' => '/login',
		'get' => array('Usuario', 'login'),
		'post' => array('Usuario', 'login'),
	],
	"/request" => [
		'es' => '/request',
		'en' => '/request',
		'path' => '/request',
		'rol'=>'common',
		'get' => array('Plain', 'inquiere'),
		'post' => array('Plain', 'inquiere'),
	],
	"/transfers" => [
		'es' => '/traslados',
		'en' => '/transfers',
		'allow' => true,
		'path' => '/transfers',
		'get' => array('Plain', 'transfers'),
		'post' => array('Plain', 'transfers'),
	],
	"/404" => [
		'es' => '/404',
		'en' => '/404',
		'allow' => true,
		'path' => '/404',
		'get' => array('Plain', 'notFound'),
		'post' => array('Plain', 'notFound'),
	]
];

foreach ($ROUTES as $key => $route) {
	$router->addRoute($route);
}

//admin routes
$router->addRoute(array(
	'path' => '/panel/hotel/show',
	'get' => array('Hotel', 'show'),
));

$router->addRoute(array(
	'path' => '/panel/hotel/edit/{hotel}',
	'get' => array('Hotel', 'edit'),
	'post' => array('Hotel', 'edit'),
));

$router->addRoute(array(
	'path' => '/panel/hotel/delete/{hotel}',
	'get' => array('Hotel', 'delete'),
));

$router->addRoute(array(
	'path' => '/panel/hotel/show/{hotel}',
	'get' => array('Hotel', 'hide'),
));



///HotelImages Routes
$router->addRoute(array(
	'path' => '/panel/hotel/addImages',
	'get' => array('Hotel', 'addImages'),
	'post' => array('Hotel', 'addImages'),
));
$router->addRoute(array(
	'path' => '/panel/hotel/addVideo',
	'get' => array('Hotel', 'addVideo'),
	'post' => array('Hotel', 'addVideo'),
));
$router->addRoute(array(
	'path' => '/panel/hotel/editImages/{hotel}',
	'get' => array('Hotel', 'editImages'),
	'post' => array('Hotel', 'editImages'),
));
$router->addRoute(array(
	'path' => '/panel/hotel/editVideo/{hotel}',
	'get' => array('Hotel', 'editVideo'),
	'post' => array('Hotel', 'editVideo'),
));
$router->addRoute(array(
	'path' => '/panel/hotel/deleteImages/{hotel}',
	'get' => array('Hotel', 'deleteImages'),
));
$router->addRoute(array(
	'path' => '/panel/hotel/deleteVideo/{hotel}',
	'get' => array('Hotel', 'deleteVideo'),
));

///Tour Routes
$router->addRoute(array(
	'path' => '/panel/tour/add',
	'get' => array('Tour', 'add'),
	'post' => array('Tour', 'add'),
));
$router->addRoute(array(
	'path' => '/panel/tour/show',
	'get' => array('Tour', 'show'),
));
$router->addRoute(array(
	'path' => '/panel/tour/edit/{exper}',
	'get' => array('Tour', 'edit'),
	'post' => array('Tour', 'edit'),
));
$router->addRoute(array(
	'path' => '/panel/tour/delete/{exper}',
	'get' => array('Tour', 'delete'),
));
$router->addRoute(array(
	'path' => '/panel/tour/delete',
	'get' => array('Tour', 'show'),
));
$router->addRoute(array(
	'path' => '/panel/tour/show/{exper}',
	'get' => array('Tour', 'hide'),
));

///TourImages Routes
$router->addRoute(array(
	'path' => '/panel/tour/addImages',
	'get' => array('Tour', 'addImages'),
	'post' => array('Tour', 'addImages'),
));
$router->addRoute(array(
	'path' => '/panel/tour/editImages/{exper}',
	'get' => array('Tour', 'editImages'),
	'post' => array('Tour', 'editImages'),
));
$router->addRoute(array(
	'path' => '/panel/tour/deleteImages/{exper}',
	'get' => array('Tour', 'deleteImages'),
));

///User Routes
$router->addRoute(array(
	'path' => '/panel/user/add',
	'get' => array('Usuario', 'add'),
	'post' => array('Usuario', 'add'),
));
$router->addRoute(array(
	'path' => '/panel/user/show',
	'get' => array('Usuario', 'show'),
));
$router->addRoute(array(
	'path' => '/panel/user/edit/{exper}',
	'get' => array('Usuario', 'edit'),
	'post' => array('Usuario', 'edit'),
));
$router->addRoute(array(
	'path' => '/panel/user/active/{exper}',
	'get' => array('Usuario', 'active'),
));
$router->addRoute(array(
	'path' => '/panel/user/delete/{exper}',
	'get' => array('Usuario', 'delete'),
));
$router->addRoute(array(
	'path' => '/panel/user/zone',
	'get' => array('Usuario', 'addZone'),
	'post'=>array('Usuario','addZone')
));
$router->addRoute(array(
	'path' => '/panel/user/deleteZona/{zona}/{user}',
	'get' => array('Usuario', 'deleteZone'),
	'post'=>array('Usuario','deleteZone')
));
$router->addRoute(array(
	'path' => '/panel/profile',
	'get' => array('Usuario', 'profile'),
));
$router->addRoute(array(
	'path' => '/panel/profile/edit',
	'get' => array('Usuario', 'editProfile'),
	'post' => array('Usuario', 'editProfile'),
));
//contact routes
$router->addRoute(array(
	'path' => '/panel/contact/show',
	'get' => array('Contact', 'show'),
));
//request routes
$router->addRoute(array(
	'path' => '/panel/request/show',
	'get' => array('Request', 'show'),
));
$router->addRoute(array(
	'path' => '/panel/request/show/{req}',
	'get' => array('Request', 'showDet'),
));
//transfer Blocks routes
$router->addRoute(array(
	'path' => '/panel/transfer/addBlock',
	'get' => array('Transfer', 'addBlock'),
	'post' => array('Transfer', 'addBlock'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/addDetail',
	'get' => array('Transfer', 'addDetail'),
	'post' => array('Transfer', 'addDetail'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/listBlock',
	'get' => array('Transfer', 'listBlock'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/editBlock/{block}',
	'get' => array('Transfer', 'editBlock'),
	'post' => array('Transfer', 'editBlock'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/delete/{block}',
	'get' => array('Transfer', 'deleteBlock'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/editDetail/{detail}',
	'get' => array('Transfer', 'editDetail'),
	'post' => array('Transfer', 'editDetail'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/deleteDetail/{detail}',
	'get' => array('Transfer', 'deleteDetail'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/addValue',
	'get' => array('Transfer', 'addValue'),
	'post' => array('Transfer', 'addValue'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/addHotel',
	'get' => array('Transfer', 'addHotel'),
	'post' => array('Transfer', 'addHotel'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/showHotel',
	'get' => array('Transfer', 'showHotel')
));
$router->addRoute(array(
	'path' => '/panel/transfer/editHotel/{hTrans}',
	'get' => array('Transfer', 'editHotel'),
	'post' => array('Transfer', 'editHotel'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/editHotel_spa/{hTrans}',
	'get' => array('Transfer', 'editHotelSpa'),
	'post' => array('Transfer', 'editHotelSpa'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/hotelDelete/{hTrans}',
	'get' => array('Transfer', 'deleteHotel')
));

$router->addRoute(array(
	'path' => '/panel/transfer/editValue/{value}',
	'get' => array('Transfer', 'editValue'),
	'post' => array('Transfer', 'editValue'),
));
$router->addRoute(array(
	'path' => '/panel/transfer/deleteValue/{value}',
	'get' => array('Transfer', 'deleteValue'),
	'post' => array('Transfer', 'deleteValue')
));
$router->addRoute(array(
	'path' => '/panel/transfer/hide/{detail}',
	'get' => array('Transfer', 'hide'),
));
//routes for vehicle
$router->addRoute(array(
	'path' => '/panel/vehicles/show',
	'get' => array('Vehicle', 'show')
));
$router->addRoute(array(
	'path' => '/panel/vehicles/add',
	'get' => array('Vehicle', 'add'),
	'post' => array('Vehicle', 'add')
));
$router->addRoute(array(
	'path' => '/panel/vehicles/edit/{car}',
	'get' => array('Vehicle', 'edit'),
	'post' => array('Vehicle', 'edit'),
));
$router->addRoute(array(
	'path' => '/panel/vehicles/delete/{car}',
	'get' => array('Vehicle', 'delete')
));
$router->addRoute(array(
	'path' => '/panel/vehicles/addImages',
	'get' => array('Vehicle', 'addImages'),
	'post' => array('Vehicle', 'addImages')
));
$router->addRoute(array(
	'path' => '/panel/vehicles/deleteImages/{car}',
	'get' => array('Vehicle', 'deleteImages')
));
$router->addRoute(array(
	'path' => '/panel/vehicles/editImages/{car}',
	'get' => array('Vehicle', 'editImages'),
	'post' => array('Vehicle', 'editImages')
));




?>