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
class SessionLogin extends \Zaphpa\BaseMiddleware {

    private $urlPermitidas = ['/login', '/logout', '/','/about-us','/hotel-collection','/hotel-collection/{hotel}','/experience','/experience/{exper}','/contact-us','/home','/forgot','/forgot/{uid}',''];

    function preprocess(&$router) {

        $router->addRoute(array(
            'path' => '/login',
            'get' => array('Usuario', 'login'),
            'post' => array('Usuario', 'login')
        ));

        $router->addRoute(array(
            'path' => '/logout',
            'get' => array('Usuario', 'logout')
        ));
    }

    public function preroute(&$req, &$res) {

        $redirect_after_login = "/bali/";

        global $spot;
        $usersMapper = $spot->mapper("Entity\Usuario");

        global $session_handle;
        $session = $session_handle->getSegment('Luna\Session');


        if (!in_array(self::$context["pattern"], $this->urlPermitidas)) {

            if (!$session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . "/bali/login?redirect=/bali" . self::$context["request_uri"]);
                die();
            } else {
                //  IF USUER IS LOGGED IN
                $req->user = $session->get("user");
                $session->set("user", $req->user);
                
            }
        }

        if (self::$context["request_uri"] == '/login') {
            if ($session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . $redirect_after_login);
            }
            if (isset($req->data["usuario"])) {
                $username = $req->data["usuario"];
                $password = $req->data["password"];

                $user = $usersMapper->where(["usuario" => $username]);
                if ($user->first()) {
                    if ($user->first()->password === md5($password)) {

                        /// LOGIN SUCCESS

                        $user = $user->first()->toArray();
                        $session->set("user", $user);

                        if (isset($req->data["redirect"])) {
                            header("Location: http://" . $_SERVER["SERVER_NAME"] . $req->data["redirect"]);
                        } else {
                            header("Location: http://" . $_SERVER["SERVER_NAME"] . $redirect_after_login);
                        }

                    } else {
                        $session_controller = $session_handle->getSegment('Luna\Controllers');
                        $session_controller->setFlash("alert", ["message" => "El password y el usuario no coinciden!", "status" => "Error:", "class" => "alert-danger"]);
                    }
                } else {
                    $session_controller = $session_handle->getSegment('Luna\Controllers');
                    $session_controller->setFlash("alert", ["message" => "El password y el usuario no coinciden!", "status" => "Error:", "class" => "alert-danger"]);
                }
            }
        }


        if (self::$context["request_uri"] == '/logout') {
            $session_handle->destroy();
            header("Location: /bali/login");
            exit;
        }
    }

    function tienePermiso($permisos, $ruta) {
        foreach ($permisos as $permiso) {
            if ($permiso['ruta'] == $ruta && $permiso['allow'] == 'allow') {
                return true;
            }
        }
        return false;
    }

    function prerender(&$buffer) {
        
    }

}

?>