<h1 class="nombre-pagin">Actualizar Servicio</h1>
<p class="descripcion-pagina">Modifica los datos de un servicio existente</p>

<?php include_once __DIR__ . '/../templates/barra.php'; ?>
<?php include_once __DIR__ . '/../templates/alertas.php'; ?>

<form method="POST" class="formulario">
    <?php include_once __DIR__ . '/formulario.php'; ?>
    <input type="submit" class="boton" value="Guardar Actualizar">
</form>