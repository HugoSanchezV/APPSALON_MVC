<h1 class="nombre-pagina">Olvide Password</h1>
<p class="descripcion-pagina">Reestablece tu password escribiendo tu email a continuación</p>

<?php 
    include_once __DIR__ . '/../templates/alertas.php';
?>

<form class="formulario" method="POST" action="/olvide-password">
    <div class="campo">
        <label for="email">Tu Email</label>
        <input type="email" id="email" name="email" placeholder="Tu Email">

    </div>
    <input type="submit" value="Enviar Instrucciones" class="boton">

</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aun no tienes una cuenta? Crear una</a>
</div>