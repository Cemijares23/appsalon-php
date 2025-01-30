
<h1 class="nombre-pagina">Panel de Aministración</h1>

<?php include_once __DIR__ . '/../templates/barra.php' ?>

<h2>Bucar Cita</h2>
<div class="busqueda">
    <form class="formulario">
        <div class="campo">
            <label for="fecha">Fecha</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $fecha; ?>">
        </div>
    </form>
</div>

<?php if(count($citas) === 0) { ?>
        <div class="alerta-fecha">
            <h3>Ups!</h3>
            <h3>No hay citas para esta fecha</h3>
        </div>
<?php } ?>

<!-- Aca la condicion evalua si el id de la cita es = a el id de la cita anterior. Solo ingresara cada id una vez, debido que al encontrar coincidencias, no se ejecutara -->
<div id="citas-admin">
    <ul class="citas">
        <?php 
            $idCita = 0;
            foreach($citas as $key => $cita) { 
                ?>
            <?php if($idCita !== $cita->id) { 
                $total = 0; ?>
                <div class="cita-info">
                    
                    <li>
                        <h3>Datos</h3>
                        <p>ID: <span><?php echo $cita->id; ?></span></p>
                        <p>Hora: <span><?php echo $cita->hora; ?></span></p>
                        <p>cliente: <span><?php echo $cita->cliente; ?></span></p>
                        <p>Email: <span><?php echo $cita->email; ?></span></p>
                        <p>Teléfono: <span><?php echo $cita->telefono; ?></span></p>
                    </li>
        
                    <h3>Servicios</h3>  
                </div>
            <?php } //if 
                $idCita = $cita->id;
            ?>
    
            <p class="servicio"><?php echo $cita->servicio . ' - $' . $cita->precio; ?></p>

            <?php 
                $actual = $cita->id;
                $proximo = $citas[$key + 1]->id ?? 0;
                $total+= $cita->precio;
            ?>

            <!-- Al final de cada servicio -->
            <?php if($proximo !== $actual) { ?>
                <p class="total">Total: <span>$<?php echo $total; ?></span></p>

                <form action="/api/eliminar" method="POST" onsubmit="confirmEliminar(event)">
                    <input type="hidden" name="id" value="<?php echo $cita->id ?>">
                    <input type="submit" class="boton-eliminar" value="Eliminar"">
                </form>
            <?php } ?>

            <?php } //foreach ?> 
    </ul>
</div>

<?php 
    $script = "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script src='build/js/admin.js'></script>
    " 
?>