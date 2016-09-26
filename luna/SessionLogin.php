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

    private $urlPermitidas = ['/login', '/logout', '/','/about-us','/hotel-collection','/hotel-collection/{hotel}','/experience','/experience/{exper}','/contact-us','/home','/forgot','/forgot/{uid}','/register','/aviso','/transfers','' ,"/migrate/up"];
    private $common=['/transfer','/request','/transfer/{uid}'];

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
                $route["rol"]=isset($route["rol"])?$route["rol"]:'';
                if(self::$context["pattern"]==$route["es"]&&$route["rol"]=='common'){
                    array_push($this->common, self::$context["pattern"]);
                }
            }
            if(isset($route["en"])){
                $route["allow"]=isset($route["allow"])?$route["allow"]:false;
                if(self::$context["pattern"]==$route["en"]&&$route["allow"]==true){
                    array_push($this->urlPermitidas,self::$context["pattern"]);
                }
                $route["rol"]=isset($route["rol"])?$route["rol"]:'';
                if(self::$context["pattern"]==$route["en"]&&$route["rol"]=='common'){
                    array_push($this->common, self::$context["pattern"]);
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

        if($session->get("user",false)){
            $id=$session->get("user")["id"];
            //buscamos el pattern en los routes
            $aux="";
            foreach ($ROUTES as $key => $route) {
                if(isset($route["es"])){
                    if(self::$context["pattern"]==$route["es"]){
                        $aux=explode("/",$route["path"]);
                    }
                }
                if(isset($route["en"])){
                    if(self::$context["pattern"]==$route["en"]){
                        $aux=explode("/",$route["path"]);
                    }
                }
            }
            //obtenemos path con uri y los combinamos
            $slug="";
            if($aux==""){
                $slug=self::$context["request_uri"];
                if($slug=="/"){
                    $slug="/home";
                }
            }
            else{
                $aux2=explode("/",self::$context["request_uri"]);
                if(sizeof($aux)==3){
                    $slug="/".$aux[1]."/".$aux2[2]."/";
                }
                else if(sizeof($aux)==2){
                    $slug="/".$aux[1];
                }
            } 
            //registramos
            if($slug!="/404"){
                $visitMapper=$spot->mapper("Entity\Visits");
                $visit=$visitMapper->build([
                    'userid' => $id,
                    'slug' => $slug,
                ]);
                $visitMapper->insert($visit);
            } 
        }
        //si pattern no esta en las url permitidas, verificamos el usuario este logueado y tenga permisos para acceder al lugar
        if (!in_array(self::$context["pattern"], $this->urlPermitidas)) {
            //si el usuario no esta logueado, lo mandamos a loguear
            if (!$session->get("user", false)) {
                header("Location: http://" . $_SERVER["SERVER_NAME"] . "/bali/login?redirect=/bali" . self::$context["request_uri"]);
                die();
            } else {
                $req->user = $session->get("user");
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
                                $session->set("user", $req->user);
                                $session->set("zonas", $permisoZone);
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
                            default:
                                $session->set("user", $req->user);
                                header("Location: http://" . $_SERVER["SERVER_NAME"] . $redirect_after_login);
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

                $user = $usersMapper->where(["usuario" => $username])->first();
                if ($user){
                    if ($user->password === md5($password)||$user->activo=== true) {
                        /// LOGIN SUCCESS
                        $user = $user->toArray();
                        $session->set("user", $user);
                        $id=$session->get("user")["id"];
                        $visitMapper=$spot->mapper("Entity\Visits");
                        $visit=$visitMapper->build([
                            'userid' => $id,
                            'slug' => self::$context["request_uri"],
                        ]);
                        $visitMapper->insert($visit);
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