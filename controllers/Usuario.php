<?php

/**
 *    Controlador de lugares
 *
 *
 */
//use CentroNotificaciones;


//require_once __LUNA__ . '/luna/Helpers/Mail/Mail.php';

class Usuario extends Luna\Controller {

    public function front($req, $res) {
        echo $this->renderWiew([], $res);
    }

    public function loged($req, $res) {
        echo $this->renderWiew([], $res);
    }

    /**
     * Vista para el listado de usuario
     * @param type $req
     * @param type $res
     */
    public function usuarios($req, $res) {
        global $spot;
        $tpu = $spot->mapper("Entity\UsuarioDatos");
        $tpu->migrate();

        $limit = isset($req->data['numpp']) ? $req->data['numpp'] : 10;
        $page = isset($req->data['page']) ? ($req->data['page'] * $limit) - $limit : 0;
        $count = count($this->mapper->where(["estado" => EstadoUsuario::activo])->with("tipo")->toArray());
        $user = $this->mapper->where(["estado" => EstadoUsuario::activo])->with("tipo")->limit($limit)->offset($page)->toArray();
        $end = ($page + $limit >= $count) ? $count : $page + $limit;
        echo $this->renderWiew(["users" => $user, 'ini' => $page + 1, 'end' => $end, 'count' => $count], $res);
    }

    /**
     * Vista para crear nuevo usuario
     * @param type $req
     * @param type $res
     */
    public function nuevo($req, $res) {
        $tipos = $this->spot->mapper("Entity\TipoUsuario")->all()->order(['tipo' => 'ASC'])->toArray();
        $roles = $this->spot->mapper("Entity\Rol")->all()->order(['rol' => 'ASC'])->toArray();
        echo $this->renderWiew(["tipos" => $tipos, "roles" => $roles], $res);
    }

    /**
     * Vista para editar usuario
     * @param type $req
     * @param type $res
     */
    public function editar($req, $res) {

        $id = $req->params['id'];
        $usuario = $this->mapper->where(["id" => $id])->with("tipo")->first()->toArray();
        $tipos = $this->spot->mapper("Entity\TipoUsuario")->all()->order(['tipo' => 'ASC'])->toArray();

        foreach ($tipos as &$tipo) {
            if ($tipo["id"] == $usuario["tipo"]["id"]) {
                $tipo["selected"] = true;
            }
        }

        $roles = $this->spot->mapper("Entity\Rol")->all()->order(['rol' => 'ASC'])->toArray();
        foreach ($roles as &$role) {
            if (in_array($role["id"], $usuario["roles"])) {
                $role["selected"] = true;
            }
        }
        echo $this->renderWiew(['usuario' => $usuario, "tipos" => $tipos, "roles" => $roles], $res);
    }

    public function eliminar($req, $res) {
        $id = $req->params['id'];
        $user = $this->mapper->get($id);
        $user->estado = EstadoUsuario::inactivo;
        $this->mapper->update($user);
        $this->session->setFlash("alert", ["message" => "Usuario eliminado satisfactoriamente!", "status" => "Exitoso:", "class" => "alert-success"]);
        header('Location: /panel/usuarios');
    }

    public function guardareditarusuariopsv($req, $res) {
        unset($req->data["_RAW_HTTP_DATA"]);
        $idUsuario = $req->user['id'];
        $usuario = $this->mapper->first(['id' => $idUsuario]);
        $usuario->nombre = $req->data['nombre'];
        $usuario->papellido = $req->data['papellido'];
        $usuario->sapellido = $req->data['sapellido'];
        $this->mapper->update($usuario);

        $usuarioDatosMapper = $this->spot->mapper("Entity\UsuarioDatos");
        $usuario_datos = $usuarioDatosMapper->where(["id_usuario" => $idUsuario])->first()->toArray();
        $usuario_datos = $usuarioDatosMapper->get($usuario_datos['id']);
        $usuario_datos->telefono = $req->data['telefono'];
        $usuario_datos->fecha_nacimiento = $req->data['fecha_nacimiento'];
        $usuario_datos->duenno = $req->data['duenno'];
        $usuario_datos->sexo = $req->data['sexo'];
        $usuarioDatosMapper->update($usuario_datos);
        $this->session->setFlash("alert", ["message" => "Sus datos se han editado", "class" => "alert-success "]);
        header('Location: /perfil');
        exit();
    }

    public function cambiapass($req, $res) {
        unset($req->data["_RAW_HTTP_DATA"]);
        $idUsuario = $req->user['id'];
        $usuario = $this->mapper->get($idUsuario);

        $currentePass = $usuario->password;
        if ($currentePass == md5($req->data['passwordold'])) {
            if ($req->data['password'] == $req->data['password_again']) {
                $usuario->password = $req->data['password'];
                $this->mapper->update($usuario);

                echo $this->mailer( $res , ["usuario" => "julio@mail.com"] , "Mail/cambiopass");
                exit;


                $this->session->setFlash("alert", ["message" => "Su contraseÃ±a se ha cambiado", "class" => "alert-success "]);
                header('Location: /logout');
                exit();
            } else {
                $this->session->setFlash("alert", ["message" => "Verifique que su nueva contraseÃ±a y su confirmaciÃ³n coninciden", "class" => "alert-error"]);
            }
        } else {
            $this->session->setFlash("alert", ["message" => "Su vieja contraseÃ±a no es correcta", "class" => "alert-error"]);
        }
        header('Location: /perfil');
        exit();
    }

    /**
     *
     * @param type $req
     * @param type $res
     */
    public function guardarNuevo($req, $res) {
        unset($req->data["_RAW_HTTP_DATA"]);
        if (isset($req->params['id'])) {
            $user = $this->mapper->first(['id' => $req->params['id']]);
            $user->nombre = $req->data['nombre'];
            $user->papellido = $req->data['papellido'];
            $user->sapellido = $req->data['sapellido'];
            if ($req->data['password'] != '')
                $user->password = $req->data['password'];
            $user->correo = $req->data['correo'];
            $user->id_tipo_usuario = $req->data['id_tipo_usuario'];
            $user->roles = $req->data['roles'];
            $this->mapper->update($user);
            $this->session->setFlash("alert", ["message" => "Usuario editado satisfactoriamente!", "status" => "Exitoso:", "class" => "alert-success"]);
        } else {
            $result = $this->mapper->create([
                'nombre' => $req->data['nombre'],
                'papellido' => $req->data['papellido'],
                'sapellido' => $req->data['sapellido'],
                'id_tipo_usuario' => $req->data['id_tipo_usuario'],
                'correo' => $req->data['correo'],
                'password' => $req->data['password'],
                'estado' => EstadoUsuario::activo,
                'roles' => $req->data['roles']
            ]);
            $usuario = $result->toArray();

            $this->session->setFlash("alert", ["message" => "Usuario agregado satisfactoriamente!", "status" => "Exitoso:", "class" => "alert-success"]);

            $CentroNotificaciones = new CentroNotificaciones();
            $CentroNotificaciones->nuevaPorRol($req->user['id'], Roles::admin, $usuario['id'], TipoNotificacion::noti_reg_usuario, "Registro de Usuario", "Se ha registrado un nuevo usuario en el sistema");
        }
        header('Location: /panel/usuarios');
    }

    public function verificarNombreUsuario($req, $res) {
        $correo = $req->params['nombreUsuario'];
        echo json_encode(['result' => $this->existeUsuario($correo)]);
    }

    private function existeUsuario($correo) {
        $user = $this->mapper->where(["correo" => trim($correo)]);
        if (count($user) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function login($req, $res) {
        echo $this->renderWiew([], $res);
    }

    public function logout($req, $res) {
        header("Location: http://" . $_SERVER["SERVER_NAME"] . "/login");
    }

    public function iniciopsv($req, $res) {
        echo $this->renderWiew([], $res);
    }

    public function perfilpsv($req, $res) {
        $idUsuario = $req->user['id'];
        $usuario = $this->spot->mapper("Entity\UsuarioDatos")->where(["id_usuario" => $idUsuario])->with("usuario")->first()->toArray();
        $ptoVenta = $this->spot->mapper("Entity\PuntoVentaDireccion")->where(["id_punto_venta" => $usuario['id_punto_venta'], "activa" => 1])->with('puntoventa')->first()->toArray();

        $diff = $this->mapper->query("SELECT DATEDIFF(CURRENT_DATE, (select max(fecha_generada) as diff FROM d_centro_notificacion dcn where id_relacionado=" . $ptoVenta['id'] . " and tipo='NotiAprobarPuntoVenta' and estado =0) ) as dif")->first()->toArray();
        $diff = $diff['dif'] - 0;

        $disable = ($diff > 7 && $ptoVenta['puntoventa']['estado'] == 0) ? '' : 'disabled';

        $usuario['sexo'] = ($usuario['sexo'] == 'm') ? 'Masculino' : 'Femenino';
        $ptoVenta['id_colonia'] = 'Colonia';
        $ptoVenta['id_municipio'] = 'Municipio';
        $ptoVenta['id_estado'] = 'Estado';

        $horario = explode("|", $ptoVenta['puntoventa']['horario']);
        $hora_ini = $horario[0];
        $hora_fin = $horario[1];
        $ptoVenta['display'] = 'none';
        echo $this->renderWiew(["usuario" => $usuario, 'disable' => $disable, 'ptoventa' => $ptoVenta, 'hora_ini' => $hora_ini, 'hora_fin' => $hora_fin], $res);
    }

    public function editarusuariopsv($req, $res) {
        $idUsuario = $req->user['id'];
        $usuario = $this->spot->mapper("Entity\UsuarioDatos")->where(["id_usuario" => $idUsuario])->with("usuario")->first()->toArray();

        $masculino = ($usuario['sexo'] == 'm') ? 'checked' : '';
        $femenino = ($usuario['sexo'] == 'f') ? 'checked' : '';
        $siduenno = ($usuario['duenno'] == 1) ? 'checked' : '';
        $noduenno = ($usuario['duenno'] == 0) ? 'checked' : '';
        echo $this->renderWiew(["usuario" => $usuario, 'siduenno' => $siduenno, 'noduenno' => $noduenno, 'femenino' => $femenino, 'masculino' => $masculino], $res);
    }

    public function nuevousuariopsv($req, $res) {
        if (isset($req->params['id'])) {
            $id_publica = $req->params['id'];
            $id = $this->spot->mapper("Entity\PuntoVenta")->where(["id_publico" => $id_publica])->first();
            if ($id != null) {
                $id = $id->toArray()['id'];
                $ptoVenta = $this->spot->mapper("Entity\PuntoVentaDireccion")->where(["id_punto_venta" => $id, "activa" => 1])->with('puntoventa')->first()->toArray();
                $nombre_comercial = $ptoVenta['puntoventa']['nombre_comercial'];
                $direccion = $ptoVenta['calle'] . ', ' . $ptoVenta['numero_exterior'] . ', ' . $ptoVenta['numero_interior'];
                $telefono = $ptoVenta['puntoventa']['lada'] . ' ' . $ptoVenta['puntoventa']['telefono'];
                $horario = explode("|", $ptoVenta['puntoventa']['horario']);
                $hora_ini = $horario[0];
                $hora_fin = $horario[1];
            } else {
                header('Location: /puntosventa/nuevo/');
                exit();
            }
        } elseif ($this->session->get("pv_psv") != NULL) {
            $ptoVenta = $this->session->get("pv_psv");
            $nombre_comercial = $ptoVenta['nombre_comercial'];
            $direccion = $ptoVenta['calle'] . ', ' . $ptoVenta['numero_exterior'] . ', ' . $ptoVenta['numero_interior'];
            $telefono = $ptoVenta['lada'] . ' ' . $ptoVenta['telefono'];
            $hora_ini = $ptoVenta['hora_ini'];
            $hora_fin = $ptoVenta['hora_fin'];
        } else {
            header('Location: /puntosventa/nuevo/');
            exit();
        }
        echo $this->renderWiew([
            'nombre_comercial' => $nombre_comercial,
            'direccion' => $direccion,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'hora_ini' => $hora_ini,
            'hora_fin' => $hora_fin], $res);
    }

    public function guardarNuevoPsv($req, $res) {
        $id_publica = $req->params['id'];
        $id = $this->spot->mapper("Entity\PuntoVenta")->where(["id_publico" => $id_publica])->first();

        if ($id != null) {
            unset($req->data["_RAW_HTTP_DATA"]);
            $idpto = $id->toArray()['id'];
            if (!$this->existeUsuario($req->data['correopsv'])) {
                $resultUsuario = $this->mapper->create([
                    'nombre' => $req->data['nombre'],
                    'papellido' => $req->data['papellido'],
                    'sapellido' => $req->data['sapellido'],
                    'id_tipo_usuario' => TipoUsuario::psv,
                    'correo' => $req->data['correopsv'],
                    'password' => $req->data['password'],
                    'estado' => EstadoUsuario::activo,
                    'roles' => [0 => Roles::psv]
                ]);
                $usuario = $resultUsuario->toArray();
                $id_usuario = $usuario['id'];

                $usuario_datos = $this->spot->mapper("Entity\UsuarioDatos");
                $usuario_datos->create(['id_usuario' => $id_usuario,
                    'id_punto_venta' => $idpto,
                    'fecha_nacimiento' => $req->data['fecha_nacimiento'],
                    'telefono' => $req->data['telefono'],
                    'sexo' => $req->data['sexo'],
                    'recibir' => empty($req->data['recibir']) ? false : true,
                    'duenno' => $req->data['duenno']
                ]);
                $this->session->setFlash("showmodal", ["class" => "show"]);
                header('Location: /datospersonales/nuevopsv/' . $id_publica);
            } else {
                $this->session->setFlash("alert", ["message" => "Este correo ya esta registrado por otro usuario", "class" => "alert-error"]);
            }
        }
    }

    public function guardarNuevoPsvPsv($req, $res) {
        unset($req->data["_RAW_HTTP_DATA"]);
        //se crea el usuario

        if (!$this->existeUsuario($req->data['correopsv'])) {
            $resultUsuario = $this->mapper->create([
                'nombre' => $req->data['nombre'],
                'papellido' => $req->data['papellido'],
                'sapellido' => $req->data['sapellido'],
                'id_tipo_usuario' => TipoUsuario::psv,
                'correo' => $req->data['correopsv'],
                'password' => $req->data['password'],
                'estado' => EstadoUsuario::activo,
                'roles' => [0 => Roles::psv]
            ]);
            $usuario = $resultUsuario->toArray();
            $id_usuario = $usuario['id'];

            $pv_psv = $this->session->get("pv_psv");

            $this->session->set("pv_psv", null);

            //se crea el pto de venta que esta en memoria
            $ptoventa = $this->spot->mapper("Entity\PuntoVenta");
            $punto = $ptoventa->create([
                'nombre_comercial' => $pv_psv['nombre_comercial'],
                'razon_social' => $pv_psv['razon_social'],
                'numero_pv_essilor' => isset($pv_psv['numero_pv_essilor']) ? $pv_psv['numero_pv_essilor'] : '',
                'lada' => isset($pv_psv['lada']) ? $pv_psv['lada'] : '',
                'telefono' => $pv_psv['telefono'],
                'horario' => $pv_psv['hora_ini'] . '|' . $pv_psv['hora_fin'],
                'estado' => EstadoPuntoVenta::pendiente_aprobacion
            ]);
            $punto = $punto->toArray();

            $direccion = $this->spot->mapper("Entity\PuntoVentaDireccion");
            $direccion->create([
                'id_punto_venta' => $punto['id'],
                'codigo_postal' => $pv_psv['codigo_postal'],
                'id_estado' => $pv_psv['id_estado'],
                'id_municipio' => $pv_psv['id_municipio'],
                'id_colonia' => $pv_psv['id_colonia'],
                'calle' => $pv_psv['calle'],
                'numero_exterior' => $pv_psv['numero_exterior'],
                'numero_interior' => $pv_psv['numero_interior'],
                'referencia' => $pv_psv['referencia'],
                'id_usuario_propone' => $id_usuario,
                'fecha_propone' => new DateTime(),
                'activa' => 1,
            ]);

            //se guardan los otros datos del usuario
            $usuario_datos = $this->spot->mapper("Entity\UsuarioDatos");
            $usuario_datos->create(['id_usuario' => $id_usuario,
                'id_punto_venta' => $punto['id'],
                'fecha_nacimiento' => $req->data['fecha_nacimiento'],
                'telefono' => $req->data['telefono'],
                'sexo' => $req->data['sexo'],
                'recibir' => empty($req->data['recibir']) ? false : true,
                'duenno' => $req->data['duenno']
            ]);

            $CentroNotificaciones = new CentroNotificaciones();
            $CentroNotificaciones->nuevaPorRol($id_usuario, Roles::gerente_venta, $punto['id'], TipoNotificacion::noti_reg_punto_venta, "Registro Punto de Venta", "Se ha registrado un nuevo punto de venta en el sistema");
            $CentroNotificaciones->nuevaPorRol($id_usuario, Roles::gerente_venta, $punto['id'], TipoNotificacion::noti_aprobar_punto_venta, "Registro Punto de Venta para Aprobar", "Se ha registrado un nuevo punto de venta en el sistema y debe sera aprobado");
            $this->session->setFlash("showmodal", ["class" => "show"]);
            header('Location: /datospersonales/nuevopsv/' . $punto['id_publico']);
        } else {
            $this->session->setFlash("alert", ["message" => "Este correo ya esta registrado por otro usuario", "class" => "alert-error"]);
            header('Location: /datospersonales/nuevopsv');
        }
    }

}

?>