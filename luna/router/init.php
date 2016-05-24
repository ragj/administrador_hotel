<?php
	//  BaseMiddleware by Luna for CORS see usage on class
	require_once (__DIR__."/../mustache.php");
	require_once (__DIR__."/../oauth.php");
	require_once (__DIR__."/../documentor.php");
	require_once (__DIR__."/../SessionLogin.php");
	require_once (__DIR__."/../migrator.php");
	require_once (__DIR__ ."/../Translate.php");

	
	//  REQUIRE MODELS
	foreach( scandir( __LUNA__.'/model' ) as $model ){
		$bffmodel = explode("." , $model);
		if( end( $bffmodel ) == "php" ){
			require_once( __LUNA__.'/model/'.$model );
		}
	}
	//  REQUIRE CONTROLLERS
	require_once( __LUNA__.'/controllers/Controller.php' );

	//  START ROUTER
	$router = new \Zaphpa\Router();
	
	//  ATTACHS
	$router->attach('\Luna\Translate');
	$router->attach('\Luna\OAuth' );
	$router->attach('\Luna\Migrator');
	$router->attach('\Luna\Mustache');
	$router->attach('\Luna\AutoDocumentator', '/apidocs' , $details = true);
	//$router->attach('\Luna\SessionLogin');


	//  ROUTES
	global $BASE;
	$BASE = "/bali";

	require_once( __LUNA__.'/luna/router/routes.php' );
	try {

	  $tokens = parse_url('http://lozano.travel' . str_replace($BASE , "" , $_SERVER["REQUEST_URI"] ));
      $uri = rawurldecode( isset($tokens['path'])?$tokens['path']:"/");
      $router->route( \Luna\Translate :: uri( $uri ) );

      
	} catch ( \Zaphpa\Exceptions\InvalidPathException $ex) {
	  header("Content-Type: application/json;", TRUE, 404);
	  $out = array("error" => "not found");
	  die(json_encode($out));
	}     


?>