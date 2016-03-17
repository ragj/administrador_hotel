<?php

namespace Luna;

include_once 'Enums.php';

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

    private $urlPermitidas = ['/login',
        '/logout',
        '/puntosventa/nuevo',
        '/usuario/front',
        '/datospersonales/nuevopsv',
        '/datospersonales/nuevopsv/{id}',
        '/puntosventa/buscar/{codigo_postal}/{id_colonia}/{nombre}',
        '/puntosventa/buscar',
        '/usuario/loged'];

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

        $redirect_after_login = "/centronotificaciones/notificaciones";

        global $spot;
        $usersMapper = $spot->mapper("Entity\Usuario");

        global $session_handle;
        $session = $session_handle->getSegment('Luna\Session');


        if (!in_array(self::$context["pattern"], $this->urlPermitidas)) {

            if (!$session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . "/login?redirect=" . self::$context["request_uri"]);
                die();
            } else {

                //  IF USUER IS LOGGED IN

                $req->user = $session->get("user");
                $allow = false;
                foreach ($req->user["permisos"] as $permiso) {
                    if ($permiso["ruta"] == self::$context["pattern"] && $permiso["allow"] == "allow") {
                        $allow = true;
                        continue;
                    }
                }

                //add notificaciones a usuario
                $notificaciones = $spot->mapper("Entity\CentroNotificaciones")->query("SELECT * FROM r_usuario_centro_notificacion as uc JOIN d_centro_notificacion as cn "
                                . "ON cn.id = uc.id_centro_notificacion where uc.id_usuario=" . $req->user['id'] . " and "
                                . "cn.estado=" . \EstadoNotificacion::generada . " ORDER BY fecha_generada DESC")->toArray();
                $req->user['notificaciones'] = $notificaciones;
                $req->user['count_notificaciones'] = (count($notificaciones) > 0) ? count($notificaciones) : '';
                $req->user['mostra_notificaciones'] = (count($notificaciones) > 0) ? 'block' : 'none';

                $session->set("user", $req->user);


                if (!$allow) {
                    header("Content-Type: application/json;", TRUE, 404);
                    $out = array("error" => "not allow");
                    die(json_encode($out));
                }
            }
        }

        if (self::$context["request_uri"] == '/login') {
            if ($session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . $redirect_after_login);
            }
            if (isset($req->data["correo"])) {
                $username = $req->data["correo"];
                $password = $req->data["password"];

                $user = $usersMapper->where(["correo" => $username]);
                if ($user->first()) {
                    if ($user->first()->password === md5($password)) {

                        /// LOGIN SUCCESS

                        $user = $user->first()->toArray();


                        $rolMapper = $spot->mapper("Entity\Rol");
                        $user["roles"] = $rolMapper->where(["id" => $user["roles"]])->with("permisos")->toArray();

                        $user["permisos"] = [];
                        foreach ($user["roles"] as $rol) {
                            $user["permisos"] = array_merge($user["permisos"], $rol["permisos"]);
                        }


                        /* eliminar permisos dobles */
                        $temp;
                        foreach ($user["permisos"] as $per) {
                            $temp[] = ['ruta' => $per['ruta'], 'allow' => $per['allow']];
                        }
                        $user["permisos"] = array_map("unserialize", array_unique(array_map("serialize", $temp)));


                        /* menu */
                        $menuMapper = $spot->mapper("Entity\MenuAcl");
                        $pMenu = $menuMapper->where(['parent' => NULL])->with("acl")->order(['order' => 'ASC'])->toArray();

                        $menu = [];
                        foreach ($pMenu as $p) {
                            if ($this->tienePermiso($user["permisos"], $p['acl']['ruta'])) {
                                $menu[] = ['id' => $p['id'], 'class' => $p['class'], 'label' => $p['label'], 'ruta' => $p['acl']['ruta']];
                            }
                        }

                        foreach ($menu as &$m) {
                            $subm = $menuMapper->where(['parent' => $m['id']])->with("acl")->order(['order' => 'ASC'])->toArray();
                            foreach ($subm as $sm) {
                                if ($this->tienePermiso($user["permisos"], $sm['acl']['ruta'])) {
                                    $m['sub'][] = ['id' => $sm['id'], 'class' => $sm['class'], 'label' => $sm['label'], 'ruta' => $sm['acl']['ruta']];
                                }
                            }
                        }

                        $user['menu'] = $menu;
                        $session->set("user", $user);

                        $redirect_after_login = ($user['id_tipo_usuario'] == \TipoUsuario::psv) ? '/inicio' : $redirect_after_login;


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