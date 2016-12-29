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

	public static $lang = "";
	public $default_languaje = 'en';
	public $languajes = ['es', 'en', 'jp', 'ch'];
	public static $uri = "";
	public static $pattern = "";

	static function removeLang($uri) {

	}

	static public function uri($uri = "") {
		$uri = $uri == "" ? $_SERVER["REQUEST_URI"] : $uri;
		$lang = isset($_GET["lang"]) ? $_GET["lang"] : explode('/', $uri)[1];
		if (strlen($lang) == 2 && str_replace("/" . $lang, "/", $uri) != $uri) {
			$uri_out = substr($uri, 3);
		} else {
			$uri_out = $uri;
		}
		$tokens = parse_url('http://foo.com' . $uri_out);
		$uri_out = rawurldecode($tokens['path']);
		self::$uri = $uri_out;
		return $uri_out;
	}

	//  Regresa la URL actual en el idioma especificado
	// $uriHandler => array ( uri , mapper => "{param}" )
	static function to($lang_to, $uriHandler = false) {

		global $ROUTES;
		global $BASE;
		$url = "";
		//si existe la uri en las rutas
		if (isset($ROUTES[self::$uri])) {
			$final = $ROUTES[self::$uri];
			if (isset($final[$lang_to])) {
				$url = $BASE . "/" . $lang_to . $final[$lang_to];
				return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
			} else {
				$url = $BASE . "/" . $lang_to . $final["path"];
				return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
			}
		}
		//si existe el pattern en las rutas
		else if (isset($ROUTES[self::$context["pattern"]])) {
			$final = $ROUTES[self::$context["pattern"]];
			if (isset($final)) {
				if (isset($final[$lang_to])) {
					$url = $BASE . "/" . $lang_to . $final[$lang_to];
					return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
				} else {
					$url = $BASE . "/" . $lang_to . $final["path"];
					return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
				}
			}
		} else {
			//sino buscamos dentro de cada elementos del arreglo ROUTES
			foreach ($ROUTES as $key => $route) {
				//si existe alguna ruta en el lenguaje actual y si el valor de la ruta es igual al uri recibido
				if (isset($route[self::$lang]) && $route[self::$lang] == self::$uri) {
					//si existe ruta en el lenguaje actual
					if (isset($route[$lang_to])) {
						$url = $BASE . "/" . $lang_to . $route[$lang_to];
						return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
						break;
					} else {
						$url = $BASE . "/" . $lang_to . $route["path"];
						return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
						break;
					}
				} else if (isset($route[self::$lang]) && $route[self::$lang] == self::$context["pattern"]) {
					//si existe ruta en el lenguaje actual
					if (isset($route[$lang_to])) {
						$url = $BASE . "/" . $lang_to . $route[$lang_to];
						return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
						break;
					} else {
						$url = $BASE . "/" . $lang_to . $route["path"];
						return !$uriHandler ? $url : str_replace($uriHandler["mapper"], $uriHandler["uri"], $url);
						break;
					}
				} else {
					$url = "";
				}
			}

		}

		return $url;

	}

	public function preprocess(&$router) {

		//self::$pattern = self::$context["pattern"];
		$lang = isset($_GET["lang"]) ? $_GET["lang"] : explode('/', $_SERVER["REQUEST_URI"])[2];
		self::$lang = (in_array($lang, $this->languajes)) ? $lang : $this->default_languaje;
		global $ROUTES;
		foreach ($ROUTES as $key => $route) {
			if (isset($route[self::$lang])) {
				$route["path"] = $route[self::$lang];
				$router->addRoute($route);
			}
		}

	}

	public function preroute(&$req, &$res) {
		$req->lang = self::$lang;
	}

	static function url($uri) {
		global $BASE;
		global $ROUTES;
		$lang = self::$lang ? "/" . self::$lang : "";
		if (isset($ROUTES[$uri])) {
			$final = $ROUTES[$uri];
			if (isset($final[self::$lang])) {
				$url = $BASE . $lang . $final[self::$lang];
			} else {
				$url = $BASE . $lang . $final["path"];
			}
		}
		return $url;
	}

}

?>