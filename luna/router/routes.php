<?php

// EJEMPLO
// $router->addRoute(array(
//   'path'     => '/place/view/{uri}',
//   'get'      => array('Place', 'view'),
//   'post'      => array('Place', 'view'),
// ));

$router->addRoute(array(
    'path' => '/panel/usuarios',
    'get' => array('Usuario', 'usuarios')
));

$router->addRoute(array(
    'path' => '/panel/usuarios/nuevo',
    'get' => array('Usuario', 'nuevo'),
    'post' => array('Usuario', 'guardarNuevo')
));

$router->addRoute(array(
    'path' => '/datospersonales/nuevopsv',
    'get' => array('Usuario', 'nuevousuariopsv'),
    'post' => array('Usuario', 'guardarNuevoPsvPsv')
));

$router->addRoute(array(
    'path' => '/datospersonales/nuevopsv/{id}',
    'get' => array('Usuario', 'nuevousuariopsv'),
    'post' => array('Usuario', 'guardarNuevoPsv')
));



$router->addRoute(array(
    'path' => '/inicio',
    'get' => array('Usuario', 'iniciopsv')
));

$router->addRoute(array(
    'path' => '/perfil',
    'get' => array('Usuario', 'perfilpsv'),
    'post' => array('Usuario', 'cambiapass')
));

$router->addRoute(array(
    'path' => '/perfil/editar',
    'get' => array('Usuario', 'editarusuariopsv'),
    'post' => array('Usuario', 'guardareditarusuariopsv')
));




$router->addRoute(array(
    'path' => '/panel/usuarios/editar/{id}',
    'get' => array('Usuario', 'editar'),
    'post' => array('Usuario', 'guardarNuevo')
));
$router->addRoute(array(
    'path' => '/panel/usuarios/eliminar/{id}',
    'get' => array('Usuario', 'eliminar')
));

$router->addRoute(array(
    'path' => '/panel/usuarios/verificarNombreUsuario/{nombreUsuario}',
    'get' => array('Usuario', 'verificarNombreUsuario')
));

$router->addRoute(array(
    'path' => '/centronotificaciones/draw/{idNotificacion}',
    'get' => array('CentroNotificaciones', 'draw')
));

$router->addRoute(array(
    'path' => '/centronotificaciones/forward/{idNotificacion}',
    'get' => array('CentroNotificaciones', 'forward')
));
$router->addRoute(array(
    'path' => '/centronotificaciones/dismiss/{idNotificacion}',
    'get' => array('CentroNotificaciones', 'dismiss')
));

$router->addRoute(array(
    'path' => '/centronotificaciones/notificaciones',
    'get' => array('CentroNotificaciones', 'notificaciones')
));


/* puntos de venta */
$router->addRoute(array(
    'path' => '/panel/puntosventa',
    'get' => array('PuntoVenta', 'puntosventa')
));

$router->addRoute(array(
    'path' => '/panel/gestpuntosventa',
    'get' => array('PuntoVenta', 'gestpuntosventa')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/eliminar/{id}',
    'get' => array('PuntoVenta', 'eliminar')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/solicitareliminar/{id}',
    'get' => array('PuntoVenta', 'solicitarEliminar')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/activar/{id}',
    'get' => array('PuntoVenta', 'activar')
));
$router->addRoute(array(
    'path' => '/panel/puntosventa/nuevo',
    'get' => array('PuntoVenta', 'nuevo'),
    'post' => array('PuntoVenta', 'guardarNuevo')
));
$router->addRoute(array(
    'path' => '/puntosventa/nuevo',
    'get' => array('PuntoVenta', 'nuevoptoventapsv'),
    'post' => array('PuntoVenta', 'guardarNuevoPsv')
));
$router->addRoute(array(
    'path' => '/puntosventa/buscar/{codigo_postal}/{id_colonia}/{nombre}',
    'get' => array('PuntoVenta', 'buscarptoventapsv')
));


$router->addRoute(array(
    'path' => '/panel/puntosventa/editar/{id}',
    'get' => array('PuntoVenta', 'editar'),
    'post' => array('PuntoVenta', 'guardarNuevo')
));
$router->addRoute(array(
    'path' => '/panel/puntosventa/consultor/{id}',
    'get' => array('PuntoVenta', 'consultor'),
    'post' => array('PuntoVenta', 'guardarConsultor')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/detalles/{id}',
    'get' => array('PuntoVenta', 'detalles')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/gesteditar/{id}',
    'get' => array('PuntoVenta', 'gesteditar'),
    'post' => array('PuntoVenta', 'guardarGestEditar')
));

$router->addRoute(array(
    'path' => '/panel/puntosventa/consultorvarilux/{id}',
    'get' => array('PuntoVenta', 'consultorvarilux'),
    'post' => array('PuntoVenta', 'guardarConsultorVarilux')
));

$router->addRoute(array(
    'path' => '/puntosventa/crear',
    'get' => array('PuntoVenta', 'crear'),
    'post' => array('PuntoVenta', 'guardarNuevo')
));

$router->addRoute(array(
    'path' => '/puntosventa/solicitarverificacion',
    'get' => array('PuntoVenta', 'solicitarverificacion')
));


$router->addRoute(array(
    'path' => '/usuario/front',
    'get' => array('Usuario', 'front')
));

$router->addRoute(array(
    'path' => '/usuario/loged',
    'get' => array('Usuario', 'loged')
));
?>