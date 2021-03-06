<?php

namespace Luna;

/**
 * Class CORS
 * @package Luna
 *
 * Usage:
 * 
 * $router->attach('\Luna\Mustache', '*');
 *     
 * 
 */
class Mustache extends \Zaphpa\BaseMiddleware {

  function preroute(&$req, &$res) {
    $mustache = new \Mustache_Engine(array(
        'template_class_prefix' => '__MyTemplates_',
        'cache' => __LUNA__.'/tmp/cache/mustache',
        'loader' => new \Mustache_Loader_FilesystemLoader(__LUNA__.'/views/'),
        'partials_loader' => new \Mustache_Loader_FilesystemLoader(__LUNA__.'/views/partials'),
        // 'helpers' => array('i18n' => function($text) {
        //     // do something translatey here...
        // }),
        'strict_callables' => true,
        'charset' => 'UTF-8',
        'pragmas' => [\Mustache_Engine::PRAGMA_FILTERS ,\Mustache_Engine::PRAGMA_BLOCKS],
    ));

    //  Requires value to be a time stamp string ( .getTimeStamp )
    $mustache->addHelper('date', [
        'format' => function($value) { return strtolower((string) date("d - F - Y" , $value )); },
        'myformato' => function($value) { return strtolower((string) date("d - F - Y, H:i:s" , $value )); },
        'fecha_hora' => function($value) { return ucfirst((string) date("F j, Y, g:i a" , $value )); }
    ]);

    $mustache->addHelper('url' , function($value) {

        return \Luna\Translate::url( $value );

    } );

    $mustache->addHelper('magic' , function($value) {
        explode("|",$value);
        return \Luna\Translate::url( $value );

    } );
    $mustache->addHelper('lan' , function($value) {
        return \Luna\Translate::to( $value );
    } );

    $mustache->addHelper('upper' , function($value) {
        return strtoupper( $value);
    } );

    $res->mustache = $mustache;
    
    if( is_file( __LUNA__.'/views/'.implode( "/" , self::$context["callback"] ).".mustache" ) ){
      $res->m = $res->mustache->loadTemplate( implode( "/" , self::$context["callback"] ) ) ;
    }

  }
  
  
  function prerender( &$buffer ) {



  }


}
