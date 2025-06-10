import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// ✅ Detectar base URL automáticamente
const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/');
console.log('🔧 Base URL detectada:', baseUrl);

// ✅ Elementos del DOM
const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const validarTelefono = document.getElementById('telefono');
const validarNit = document.getElementById('nit');

// ✅ VALIDACIONES
const validacionTelefono = () => {
    const cantidadDigitos = validarTelefono.value;

    if (cantidadDigitos.length < 1) {
        validarTelefono.classList.remove('is-valid', 'is-invalid');
    } else {
        if (cantidadDigitos.length < 8) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revise el número de teléfono",
                text: "La cantidad de dígitos debe ser de 8 dígitos",
                showConfirmButton: false,
                timer: 3000
            });
            validarTelefono.classList.remove('is-valid');
            validarTelefono.classList.add('is-invalid');
        } else {
            validarTelefono.classList.remove('is-invalid');
            validarTelefono.classList.add('is-valid');
        }
    }
}

function validandoNit() {
    const nit = document.getElementById('nit').value.trim();
    let nd, add = 0;

    if (nd = /^(\d+)-?([\dkK])$/.exec(nit)) {
        nd[2] = (nd[2].toLowerCase() === 'k') ? 10 : parseInt(nd[2], 10);
        for (let i = 0; i < nd[1].length; i++) {
            add += ((((i - nd[1].length) * -1) + 1) * parseInt(nd[1][i], 10));
        }
        return ((11 - (add % 11)) % 11) === nd[2];
    } else {
        return false;
    }
}

const EsValidoNit = () => {
    if (!validarNit.value.trim()) {
        validarNit.classList.remove('is-valid', 'is-invalid');
        return;
    }

    if (validandoNit()) {
        validarNit.classList.add('is-valid');
        validarNit.classList.remove('is-invalid');
    } else {
        validarNit.classList.remove('is-valid');
        validarNit.classList.add('is-invalid');
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "NIT INVÁLIDO",
            text: "El número de NIT ingresado es inválido",
            showConfirmButton: false,
            timer: 3000
        });
    }
}

// ✅ DATATABLE
const datatable = new DataTable('#TableClientes', {
    dom: `
        <"row mt-3 justify-content-between"
            <"col-md-6" l>
            <"col-md-6" f>
        >
        t
        <"row mt-3 justify-content-between"
            <"col-md-6" i> 
            <"col-md-6" p>
        >
    `,
    language: lenguaje,
    data: [],
    responsive: true,
    columns: [
        {
            title: 'No.',
            data: 'id_cliente',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombre', 
            data: 'nombre',
            render: (data, type, row) => `${data} ${row.apellido}`
        },
        { title: 'Cédula', data: 'cedula' },
        { title: 'NIT', data: 'nit' },
        { title: 'Email', data: 'email' },
        { title: 'Teléfono', data: 'telefono' },
        {
            title: 'Fecha Registro',
            data: 'fecha_creacion',
            render: (data) => {
                if (!data) return 'N/A';
                const fecha = new Date(data);
                return fecha.toLocaleDateString('es-GT');
            }
        },
        {
            title: 'Acciones',
            data: 'id_cliente',
            searchable: false,
            orderable: false,
            width: '15%',
            render: (data, type, row) => {
                return `
                <div class='d-flex justify-content-center gap-1'>
                     <button class='btn btn-warning btn-sm modificar' 
                         data-id="${data}" 
                         data-nombre="${row.nombre || ''}"  
                         data-apellido="${row.apellido || ''}"  
                         data-cedula="${row.cedula || ''}"
                         data-nit="${row.nit || ''}"  
                         data-telefono="${row.telefono || ''}"  
                         data-email="${row.email || ''}"
                         data-direccion="${row.direccion || ''}"
                         title="Modificar cliente">
                         <i class='fas fa-edit'></i>
                     </button>
                     <button class='btn btn-danger btn-sm eliminar' 
                         data-id="${data}"
                         title="Eliminar cliente">
                        <i class="fas fa-trash"></i>
                     </button>
                 </div>`;
            }
        }
    ],
})

// ✅ BUSCAR CLIENTES
const BuscarCliente = async () => {
    console.log('🔄 Iniciando búsqueda de clientes...');
    
    const url = `${baseUrl}/clientes/buscarAPI`;
    
    try {
        console.log(`🔄 Usando URL: ${url}`);
        
        const respuesta = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        console.log(`📡 Status: ${respuesta.status} ${respuesta.statusText}`);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}: ${respuesta.statusText}`);
        }
        
        const contentType = respuesta.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await respuesta.text();
            console.error('❌ Respuesta no es JSON:', text.substring(0, 500));
            throw new Error('El servidor no devolvió JSON válido');
        }
        
        const datos = await respuesta.json();
        console.log('✅ Datos recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            console.log(`✅ ${data.length} clientes cargados exitosamente`);
            datatable.clear().draw();
            datatable.rows.add(data || []).draw();
            
            // No mostrar mensaje si hay datos
            if (data.length === 0) {
                Swal.fire({
                    position: "center",
                    icon: "info",
                    title: "Sin registros",
                    text: "No hay clientes registrados",
                    showConfirmButton: false,
                    timer: 2000,
                });
            }
        } else {
            console.log('ℹ️ Respuesta del servidor:', mensaje);
            datatable.clear().draw();
            
            Swal.fire({
                position: "center",
                icon: "info",
                title: "Información",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    } catch (error) {
        console.error('❌ Error:', error);
        datatable.clear().draw();
        
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: `No se pudo conectar con el servidor: ${error.message}`,
            showConfirmButton: true,
        });
    }
}

// ✅ GUARDAR CLIENTE
const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    // Validar campos requeridos
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const telefono = document.getElementById('telefono').value.trim();

    if (!nombre || !apellido || !telefono) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campos obligatorios",
            text: "Nombre, apellido y teléfono son campos obligatorios",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const formData = new FormData(FormClientes);
    const url = `${baseUrl}/clientes/guardarAPI`;
    
    try {
        console.log(`🔄 Guardando cliente en: ${url}`);
        
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('✅ Respuesta:', datos);
        
        const { codigo, mensaje } = datos;
        
        if (codigo == 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Éxito!",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000,
            });
            limpiarTodo();
            BuscarCliente();
        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    } catch (error) {
        console.error('❌ Error al guardar:', error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: `Error al guardar: ${error.message}`,
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

// ✅ MODIFICAR CLIENTE
const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    // Validar campos requeridos
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const telefono = document.getElementById('telefono').value.trim();

    if (!nombre || !apellido || !telefono) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Campos obligatorios",
            text: "Nombre, apellido y teléfono son campos obligatorios",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnModificar.disabled = false;
        return;
    }

    const formData = new FormData(FormClientes);
    const url = `${baseUrl}/clientes/modificarAPI`;

    try {
        console.log(`🔄 Modificando cliente en: ${url}`);
        
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Éxito!",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000,
            });
            limpiarTodo();
            BuscarCliente();
        } else {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
            });
        }
    } catch (error) {
        console.error('❌ Error al modificar:', error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: `Error al modificar: ${error.message}`,
            showConfirmButton: true,
        });
    }
    BtnModificar.disabled = false;
}

// ✅ ELIMINAR CLIENTE - Necesitas cambiar tu controlador para usar GET
const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea ejecutar esta acción?",
        text: "Esta acción eliminará permanentemente el cliente",
        showConfirmButton: true,
        confirmButtonText: "Sí, eliminar",
        confirmButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    try {
        // ✅ OPCIÓN 1: Si cambias el controlador para usar GET
        const url = `${baseUrl}/clientes/eliminar?id_cliente=${idCliente}`;
        
        const respuesta = await fetch(url, {
            method: 'GET'
        });

        // ✅ OPCIÓN 2: Si mantienes POST (comentar la opción 1 y usar esta)
        /*
        const body = new URLSearchParams();
        body.append('id_cliente', idCliente);
        
        const respuesta = await fetch(`${baseUrl}/clientes/eliminarAPI`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });
        */

        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Eliminado!",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000
            });
            BuscarCliente();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000
            });
        }
    } catch (error) {
        console.error('❌ Error al eliminar:', error);
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: `Error al eliminar: ${error.message}`,
            showConfirmButton: true,
        });
    }
};

// ✅ LLENAR FORMULARIO PARA MODIFICAR
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('id_cliente').value = datos.id;
    document.getElementById('nombre').value = datos.nombre;
    document.getElementById('apellido').value = datos.apellido;
    document.getElementById('cedula').value = datos.cedula;
    document.getElementById('nit').value = datos.nit;
    document.getElementById('telefono').value = datos.telefono;
    document.getElementById('email').value = datos.email;
    document.getElementById('direccion').value = datos.direccion;

    // Cambiar botones
    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    // Scroll al formulario
    document.getElementById('FormClientes').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// ✅ LIMPIAR FORMULARIO
const limpiarTodo = () => {
    FormClientes.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Limpiar clases de validación
    const inputs = FormClientes.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });

    // Limpiar el ID oculto
    document.getElementById('id_cliente').value = '';
}

// ✅ INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Inicializando módulo de clientes...');
    
    // Verificar elementos críticos
    const elementos = {
        FormClientes,
        BtnGuardar,
        BtnModificar,
        BtnLimpiar,
        validarTelefono,
        validarNit
    };
    
    Object.entries(elementos).forEach(([nombre, elemento]) => {
        if (elemento) {
            console.log(`✅ ${nombre} encontrado`);
        } else {
            console.error(`❌ ${nombre} NO encontrado`);
        }
    });
    
    // Event listeners
    if (FormClientes) {
        FormClientes.addEventListener('submit', GuardarCliente);
        console.log('✅ Evento submit agregado al formulario');
    }
    
    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Evento click agregado a BtnLimpiar');
    }
    
    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarCliente);
        console.log('✅ Evento click agregado a BtnModificar');
    }
    
    if (validarTelefono) {
        validarTelefono.addEventListener('blur', validacionTelefono);
        console.log('✅ Evento blur agregado a teléfono');
    }
    
    if (validarNit) {
        validarNit.addEventListener('blur', EsValidoNit);
        console.log('✅ Evento blur agregado a NIT');
    }
    
    // Eventos del datatable
    datatable.on('click', '.eliminar', EliminarCliente);
    datatable.on('click', '.modificar', llenarFormulario);
    console.log('✅ Eventos del datatable agregados');
    
    // Cargar clientes al iniciar
    console.log('🔄 Cargando clientes iniciales...');
    BuscarCliente();
});