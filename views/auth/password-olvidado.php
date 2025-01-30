<h1 class="nombre-pagina">Contraseña Olvidada</h1>
<p class="descripcion-pagina">Reestablece tu contraseña escribiendo tu email a continuación</p>

<?php include_once __DIR__ . '/../templates/alertas.php' ?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Tu Email">
    </div>

    <input class="boton" type="submit" value="Enviar">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una</a>
</div>
