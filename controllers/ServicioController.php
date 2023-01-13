<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController
{

    public static function index(Router $router)
    {
        session_start();

        isAdmin();

        $servicios = Servicio::all();

        $router->render('servicio/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios
        ]);
    }

    public static function crear(Router $router)
    {
        session_start();

        isAdmin();

        $servicio = new Servicio($_POST);

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)) {
                $servicio->guardar();
                header('location: /servicios');
            }
        }

        $alertas = Servicio::getAlertas();

        $router->render('servicio/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas 
        ]);
    }
    public static function actualizar(Router $router)
    {
        session_start();

        isAdmin();

        if(!is_numeric($_GET['id'])) return;
        
        
        $id = $_GET['id'];
        $resultado = $servicio = Servicio::find($id);
        
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if(empty($alertas)){
                $servicio->guardar();
                header('location: /servicios');
            }
        }

        $router->render('servicio/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar()
    {
        session_start();
        isAdmin();
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $id = $_POST['id'];
            $servicio = Servicio::find($id);
            $servicio->eliminar();

            header('location: /servicios');
        }
    }
}
