<?php

namespace Luna;
use OAuth2 ;
require "UserCredentials.php";
/**
 * Class OAuth on luna
 * @package Luna
 *
 * Usage:
 * 
 * $router->attach('\Luna\OAuth');
 *     
 * 
 */

class OAuth extends \Zaphpa\BaseMiddleware {


  function preprocess(&$router) {
    $router->addRoute(array(
      'path'     => '/oauth/v2/token',
      'post'      => array('Api','token')
    ));
  }

  function preroute(&$req, &$res) {
      // if( self::$context["request_uri"] != '/oauth/v2/token'  ){
      //   $api = new \Api();
      //   if (!$api->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
      //     echo $api->server->getResponse()->send();
      //     die;
      //  }
      // }
  }
  
  function prerender( &$buffer ) {


  }


}
