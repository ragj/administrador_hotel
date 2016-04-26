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

    private $urlPermitidas = ['/login', '/logout', '/','/about-us','/hotel-collection','/hotel-collection/{hotel}','/experience','/experience/{exper}','/contact-us','/home','/forgot','/forgot/{uid}','/register','/aviso','' ,"/migrate/up"];
    private $common=['/transfer'];
    private $admin=['/panel/hotel/add','/panel/hotel/show','/panel/hotel/edit/{hotel}','/panel/hotel/delete/{hotel}','/panel/hotel/addImages','/panel/hotel/editImages/{hotel}','/panel/hotel/deleteImages/{hotel}','/panel/tour/add','/panel/tour/show','/panel/tour/edit/{exper}','/panel/tour/delete/{exper}','/panel/tour/delete','/panel/tour/addImages','/panel/tour/editImages/{exper}','/panel/tour/deleteImages/{exper}','/panel/user/add','/panel/user/show','/panel/user/edit/{exper}','/panel/user/active/{exper}','/panel/user/delete/{exper}','/panel/contact/show','/panel/transfer/addBlock','/panel/transfer/addDetail','/panel/transfer/listBlock','/panel/transfer/editBlock/{block}','/panel/transfer/delete/{block}','/panel/transfer/editDetail/{detail}','/panel/transfer/deleteDetail/{detail}','/panel/user/zone','/panel/user/deleteZona/{zona}/{user}','/panel/hotel/addVideo','/panel/hotel/editVideo/{hotel}','/bali/panel/hotel/deleteVideo/{hotel}','/panel/transfer/addValue','/panel/transfer/editValue/{value}','/panel/transfer/deleteValue/{value}'];

    function preprocess(&$router) {
        $router->addRoute(array(
            'path' => '/logout',
            'get' => array('Usuario', 'logout')
        ));
    }

    public function preroute(&$req, &$res) {

        /*Searching for the context pattern in the global ROUTS and adding that route to the router if exists and it has the allow value else do nothing*/
        global $ROUTES;
        foreach ($ROUTES as $key => $route) {
            if(isset($route["es"])){
                $route["allow"]=isset($route["allow"])?$route["allow"]:false;
                if(self::$context["pattern"]==$route["es"]&&$route["allow"]==true){
                    array_push($this->urlPermitidas,self::$context["pattern"]);
                }
            }
            if(isset($route["en"])){
                $route["allow"]=isset($route["allow"])?$route["allow"]:false;
                if(self::$context["pattern"]==$route["en"]&&$route["allow"]==true){
                    array_push($this->urlPermitidas,self::$context["pattern"]);
                }
            }
        }
        //path succes
        $redirect_after_login = "/bali/";
        //spot users
        global $spot;
        $usersMapper = $spot->mapper("Entity\Users");
        //sesion handeler
        global $session_handle;
        $session = $session_handle->getSegment('Luna\Session');
        //base
        global $BASE;
        //si pattern no esta en las url permitidas, verificamos el usuario este logueado y tenga permisos para acceder al lugar
        if (!in_array(self::$context["pattern"], $this->urlPermitidas)) {
            //si el usuario no esta logueado, lo mandamos a loguear
            if (!$session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . "/bali/login?redirect=/bali" . self::$context["request_uri"]);
                die();
            } else {
                //  si el usuario esta logueado, obtenemos el usuario actual.
                $req->user = $session->get("user");
                // obtenemos su rol
                $rol=$session->get("user")["rols_idrols"];
                //obtenemos las zonas que tiene disponibles
                $aux=$usersMapper->where(["usuario"=>"admin"])->with("zonas")->first()->toArray();
                $zonas=$aux['zonas'];
                $activo=$aux['activo'];
                //si el usuario esta activo, procedemos a verificar permiso de zona y rol
                if($activo==true){
                    $permiso=false;
                    $permisoZone=array();
                    //verificamos que tenga permiso en la zona actual, es decir el $BASE
                    foreach ($zonas as $zona) {
                        //generamos un array con los id's de las zona
                        array_push($permisoZone,$zona['idzona']);
                        //si el valor de base es igual a lo que esta en zona, damos permiso
                        if(strcmp($BASE,strtolower("/".$zona["zona"]))==0){
                            $permiso=true;
                        }
                    }
                    //Si permiso de zona es true, vemos el rol y vemos si tiene habilitada la url
                    if($permiso){
                        switch($rol){
                        //si rol es uno, entonces tiene acceso al panel admin y a lo privado
                            case 1:
                                if(in_array(self::$context["pattern"], $this->admin)){
                                        $session->set("user", $req->user);
                                        $session->set("zonas", $permisoZone); 
                                }
                            break;
                            //solo tiene acceso a lo privado
                            case 2:
                                if(in_array(self::$context["pattern"], $this->common)){
                                    $session->set("user", $req->user);
                                }else{
                                    //pero no tiene acceso al administrador
                                    $session->set("user", $req->user);
                                    header("Location: http://" . $_SERVER["SERVER_NAME"] . $redirect_after_login);
                                }
                            break;
                        }
                    }else{
                        //no tiene permiso a esta zona pero si es un usuario
                        $session_handle->destroy();
                        header("Location: /bali/login");
                        exit;
                    } 
                }else{
                    //el usuario no esta activo por lo tanto no lo dejamos loguear
                    $session_handle->destroy();
                    header("Location: /bali/login");
                    exit;
                }
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
                    if ($user->first()->password === md5($password)||$user->first->activo=== true) {
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