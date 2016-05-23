<?php 
	// // TRACER
	$whoops = new \Whoops\Run;
	$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
	$whoops->register();

	//  ROOT DIRECTORY
	define('__LUNA__',  __DIR__."/.." );

	$cfg = new \Spot\Config();
	$cfg->addConnection('mysql', [
	    'dbname' => 'ess_spe',
	    'user' => 'root',
	    'password' => 'root',
	    'host' => 'localhost',
	    'driver' => 'pdo_mysql',
	]);
	
	global $spot;
	$spot = new \Spot\Locator($cfg);

	if( !isset($_SESSION)){session_start();}
	$aura_session = new \Aura\Session\SessionFactory;
	global $session_handle;
    $session_handle = $aura_session->newInstance($_SESSION);

     global $cmail;
    //$cmail="bali@lozano.com";
    $cmail="gescalona@denumeris.com";
    global $ccmail;
    //$ccmail="alex.mendiola@lozano.com";
    $ccmail="geoshada@gmail.com";

    include __DIR__."/mail.config.php";

	// LANG  MANNAGER
	// TODO: Pass to middleware
	// $lang = isset($_GET["lang"])?$_GET["lang"]:"es";
	// if( str_replace( "/".$lang ,"",$_SERVER["REQUEST_URI"] ) != $_SERVER["REQUEST_URI"] ){
	// 	$_SERVER["REQUEST_URI"] = substr( $_SERVER["REQUEST_URI"], 3 );	
	// }

?>