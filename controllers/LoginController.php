<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            $usuario = new Usuario();
            if (empty($alertas)) {
                // Verificar que el usuario existe

                $usuario = Usuario::where('email', $auth->email);

                if ($usuario) {
                    // verificar el password
                    if ($usuario->comprobarPasswordAndVeridicado($auth->password)) {
                        // Autenticar el usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        
                        // Redireccionamiento
                        if ($usuario->admin === '1') {
                            $_SESSION['admin'] = $usuario->admin ?? null;

                            header('location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'El Usuario no Existe');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }

    public static function logout(Router $router)
    {
        $_SESSION = [];
        header('Location: /');
    }

    public static function olvide(Router $router)
    {
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);

            $alertas =  $auth->validarEmail();

            if (empty($alertas)) {
                // Buscar usuario
                $usuario = Usuario::where('email', $auth->email);

                if ($usuario && $usuario->confirmado === '1') {
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    
                    // Crear alerta 
                    Usuario::setAlerta('exito', 'Resvisa tu email');
                } else {
                    Usuario::setAlerta('error', 'El usuario no Existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    public static function recuperar(Router $router)
    {
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);
        
        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $error = true;
        }
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            
            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();

            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password = $password->password;

                $usuario->hashPassword();
                $usuario->token = null;
                $resultado = $usuario->guardar();

                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error'=>$error
        ]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario($_POST);

        // Alertas vacias 
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();


            // Revisa que alertas este vacio
            if (empty($alertas)) {
                $resultado = $usuario->existeUsuario();

                // Revisaer que el usuario no este registrado
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Generar un token unico
                    $usuario->crearToken();

                    // Enviar el emails
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }


    public static function mensaje(Router $router)
    {
        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router)
    {
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where('token', $token);

        if (empty($usuario) || $usuario->token === '') {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no valido');
        } else {
            // Mensaje de usuario confirmado
            $usuario->confirmado = '1';
            $usuario->token = null;
            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas,
        ]);
    }
}
