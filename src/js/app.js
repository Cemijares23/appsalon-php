let paso = 1; // Designa el paso default

// Designar limites del paginador
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre : '',
    fecha : '',
    hora : '',
    servicios : []
}

document.addEventListener('DOMContentLoaded', function() {

    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();
    botonesPaginador();
    tabs();
    paginaSiguiente();
    paginaAnterior();

    consultarAPI(); // Traernos datos de la api

    getIdCliente() // Obtener id del cliente
    getNombreCliente(); // Obtener nombre del cliente
    getFechaCita(); // Obtener fecha de la cita
    getHoraCita(); // Obtener hora de la cita

    mostrarResumen(); // Mostrar resumen de los datos de la cita
}

function mostrarSeccion() {
    // Remover 'mostrar' a la seccion anterior (si existe)
    const seccionAnterior = document.querySelector('.mostrar');
    if(seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }
    // Mostrar la seccion con el paso actual
    const seccion = document.querySelector(`#paso-${paso}`);
    seccion.classList.add('mostrar');


    // Remover estilos al tab anterior (si existe)
    const tabAnterior = document.querySelector('.actual')
    if(tabAnterior) {
        tabAnterior.classList.remove('actual')
    }
    // Dar estilos al tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`)
    tab.classList.add('actual')
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach( boton => {
        boton.addEventListener('click', function(e) {
            // e: evento
            // target: elemento al cual se le ha hecho click
            // dataset: atributos que nosotros hayamos designado
            // paso: valor del atributo paso
            paso = parseInt(e.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();
        });
    });
}

function botonesPaginador() {
    const botonAnterior = document.querySelector('#anterior')
    const botonSiguiente = document.querySelector('#siguiente')

    if(paso === 1) {
        botonAnterior.classList.add('ocultar');
        botonSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        botonAnterior.classList.remove('ocultar');
        botonSiguiente.classList.add('ocultar');

        mostrarResumen(); // Muestra resumen cuando se este en esa pestaña (paso 3)
    } else {
        botonAnterior.classList.remove('ocultar');
        botonSiguiente.classList.remove('ocultar');
    }
}

function paginaAnterior() {
    const botonAnterior = document.querySelector('#anterior');
    botonAnterior.addEventListener('click', function() {
        if(paso <= pasoInicial) return;
        paso--;

        botonesPaginador();
        mostrarSeccion();
    });
}

function paginaSiguiente() {
    const botonSiguiente = document.querySelector('#siguiente');
    botonSiguiente.addEventListener('click', function() {
        if(paso >= pasoFinal) return;
        paso++;
        
        botonesPaginador();
        mostrarSeccion();
    });
}

async function consultarAPI() {

    try {
        const resultado = await fetch('http://localhost:3000/api/servicios');
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const {id, nombre, precio} = servicio;
        
        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `$${precio}`;

        const divServicio = document.createElement('DIV');
        divServicio.classList.add('servicio');
        divServicio.dataset.idServicio = id;
        divServicio.onclick = function () {
            seleccionarServicio(servicio);
        }

        divServicio.appendChild(nombreServicio);
        divServicio.appendChild(precioServicio);
        
        document.querySelector('#servicios').appendChild(divServicio);
    });
}


function seleccionarServicio(servicio) {
    const {id} = servicio;
    const {servicios} = cita;
    
    // Agrega o quita estilos segun se seleccione un servicio
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);
    divServicio.classList.toggle('seleccionado');
    
    // .some() recorre un array y verifica que el callback se cumpla en al menos una de los elementos
    // Verificamos que el servicio se haya agregado: (en este caso, comparamos que el id del elemento en el array coincida con el id del servicio al que se le dio click)
    if(servicios.some(elemento => elemento.id === id)) {
        // Coincide (ya esta agregado al array de servicios)
        cita.servicios = servicios.filter(elemento => elemento.id !== id);
    } else {
        // No coincide (no se ha agregado al array de servicios)
        cita.servicios = [...servicios, servicio];
    }
}

// Obtenemos datos del cliente por medio de HTML y lo asignamos al objeto de cita
function getIdCliente() {
    cita.id = document.querySelector('#id').value;
}

function getNombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function getFechaCita() {
    const inputFecha = document.querySelector('#fecha');
    
    inputFecha.addEventListener('input', function(e) {
        const dia = new Date(e.target.value).getUTCDay();
        
        if([0, 6].includes(dia)) {
            e.target.value = '';
            cita.fecha = '';
            mostrarAlerta('error', 'Sábados y Domingos no trabajamos', '#alertas-formulario');
            
        } else {
            cita.fecha = e.target.value;
        }
    })
}

function getHoraCita() {
    const inputHora = document.querySelector('#hora');

    inputHora.addEventListener('input', function(e) {
        const hora = e.target.value;
        const horaSplit = hora.split(':')[0];
        if(horaSplit < 8 || horaSplit > 19) {
            e.target.value = '';
            cita.hora = '';
            mostrarAlerta('error', 'Nuestro horario es de 8:00 AM a 8:00 PM', '#alertas-formulario');
        } else {
            cita.hora = hora;
        }
    });
}

function mostrarAlerta(tipo, mensaje, elemento, tiempoDisplay = 3000) {
    // Eliminar alerta previa
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('DIV');
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    alerta.textContent = mensaje;

    const divAlertas = document.querySelector(elemento);
    divAlertas.appendChild(alerta);

    // Borra la alerta en el tiempo dado, si es false no se borra
    if(tiempoDisplay) {
        setTimeout(() => {
            alerta.remove();
        }, tiempoDisplay);
    }
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // Mientras existan elementos hijo de 'resumen' los eliminara. Es una buena forma de iterar y eliminar elemento por elemento
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0) {
        mostrarAlerta('error', 'Ups! Faltan campos por llenar', '#alertas-resumen', false)

        return;
    } 

    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    // Datos del objeto cita:
    const {nombre, fecha, hora, servicios} = cita;

    // const headingServicios = document.createElement('H3');
    // headingServicios.textContent = 'Servicios';
    // resumen.appendChild(headingServicios);

    servicios.forEach( servicio => {
        const {nombre, precio} = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');
        
        const nombreServicio = document.createElement('P');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio: </span>$${precio}`;

        contenedorServicio.appendChild(nombreServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

    const headingCita = document.createElement('H3');
    headingCita.textContent = 'Datos de Cita';
    resumen.appendChild(headingCita);

    const nombreResumen = document.createElement('P');
    nombreResumen.innerHTML = `<span>Nombre: </span>${nombre}`;

    // FORMATEANDO LA FECHA
    
    // Instanciar el string de fecha pasado por el usuario a un objeto de tipo fecha
    const fechaObj = new Date(fecha); 

    // Convertir fecha a UTC
    // Extraer los datos individuales para pasarlos como parametro al metodo Date.UTC()
    const day = fechaObj.getDate() + 2;
    const month = fechaObj.getMonth();
    const year = fechaObj.getFullYear();

    // El metodo Date.UTC() convierte los parametros dados a un tipo de fecha UTC, esto se le pasa como parametro a una nueva instancia de fecha
    const fechaUTC = new Date( Date.UTC(year, month, day));
    
    // Por cuestiones de simplicidad, guardamos las opciones en una variable a parte
    const opciones = {weekday: 'long', month: 'long', year: 'numeric', day: 'numeric'}

    // Ahora si, con el metodo toLocaleDateString() damos formato a la fecha UTC obtenida
    const fechaFormateada = fechaUTC.toLocaleDateString('es-CO', opciones);
    
    const fechaResumen = document.createElement('P');
    fechaResumen.innerHTML = `<span>Fecha: </span>${fechaFormateada}`;

    const horaResumen = document.createElement('P');
    horaResumen.innerHTML = `<span>Hora: </span>${hora} horas`;

    resumen.appendChild(nombreResumen);
    resumen.appendChild(fechaResumen);
    resumen.appendChild(horaResumen);

    // Boton de reservar cita
    const botonReservar = document.createElement('BUTTON');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.classList.add('boton');
    botonReservar.onclick = reservarCita;

    resumen.appendChild(botonReservar);
}

async function reservarCita() {

    // Destructuring los datos de cita
    const {nombre, fecha, hora, servicios, id} = cita;

    // Extraemos el id de los servicios usando .map()
    const idServicios = servicios.map(servicio => servicio.id);

    try {
        // Ingresamos los datos al FormData() (se envian por medio de POST)
        const datos = new FormData();
        datos.append('fecha', fecha);
        datos.append('hora', hora);
        datos.append('usuario_id', id);
        datos.append('servicios', idServicios);

        const url = 'http://localhost:3000/api/cita';
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire({
                icon: "success",
                title: "Cita creada!",
                text: "Tu cita ha sido creada exitosamente"
            }).then(() => {
                window.location.reload();
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Hubo un error al crear tu cita"
        }).then(() => {
            window.location.reload();
        });
    }
}