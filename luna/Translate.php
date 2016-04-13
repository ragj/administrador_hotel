<?php
namespace Luna;

/**
 * Class Para manupular el login de usuarios
 * @package Luna
 *
 * Usage:
 * 
 * $router->attach('\Luna\SessionLogin');
 *     
 * 
 */
class Translate extends \Zaphpa\BaseMiddleware {

    public $lang;
    public $default_languaje = 'es';
    public $languajes = ['es', 'en', 'jp', 'ch'];

    static public function uri() {
        $lang = isset($_GET["lang"]) ? $_GET["lang"] : explode('/', $_SERVER["REQUEST_URI"])[1];
        if (strlen($lang) == 2 && str_replace("/" . $lang, "/", $_SERVER["REQUEST_URI"]) != $_SERVER["REQUEST_URI"]) {
            $uri = substr($_SERVER["REQUEST_URI"], 3);
        } else {
            $uri = $_SERVER["REQUEST_URI"];
        }                
        $tokens = parse_url('http://foo.com' . $uri);
        $uri = rawurldecode($tokens['path']);
        return $uri;
    }

    function preprocess(&$router) {
        $lang = isset($_GET["lang"]) ? $_GET["lang"] : explode('/', $_SERVER["REQUEST_URI"])[1];
        $this->lang = (in_array($lang, $this->languajes)) ? $lang : $this->default_languaje;
    }

    public function preroute(&$req, &$res) {
        $req->lang = $this->lang;
    }

}

?>