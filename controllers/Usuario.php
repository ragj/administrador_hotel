<?php

/**
 *    Controlador de lugares
 *
 *
 */
//use CentroNotificaciones;




class Usuario extends Luna\Controller {

    public function login($req, $res) {
        echo $this->renderWiew([], $res);
    }

    public function logout($req, $res) {
        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/login");
    }

}

?>