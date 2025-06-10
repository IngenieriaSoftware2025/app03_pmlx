import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

// ✅ CORREGIDO: IDs que coinciden con el HTML
const FormClientes = document.getElementById('FormClientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnEliminar = document.getElementById('BtnEliminar');
const validarTelefono = document.getElementById('telefono'); // ✅ Corregido
const validarNit = document.getElementById('nit'); // ✅ Corregido

const validacionTelefono = () => {
    const cantidadDigitos = validarTelefono.value;

    if (cantidadDigitos.length < 1) {
        validarTelefono.classList.remove('is-valid', 'is-invalid');
    } else {
        // ✅ CORREGIDO: La lógica estaba mal
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
    // ✅ CORREGIDO: usar el ID correcto
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
    // ✅ CORREGIDO: usar el elemento correcto
    const nitElement = document.getElementById('nit');
    
    if (validandoNit()) {
        nitElement.classList.add('is-valid');
        nitElement.classList.remove('is-invalid');
    } else {
        nitElement.classList.remove('is-valid');
        nitElement.classList.add('is-invalid');

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

const GuardarCliente = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    const body = new FormData(FormClientes);
    const url = '/app03_jemg/clientes/guardarCliente';
    
    try {
        const respuesta = await fetch(url, {
            method: 'POST',
            body
        });
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;
        
        if (codigo == 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
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
        console.log("Error:", error);
    }
    BtnGuardar.disabled = false;
}

const datatable = new DataTable('#TableClientes', {
    dom: `
        <"row mt-3 justify-content-between"
            <"col" l>
            <"col" B>
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between"
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'id_cliente', // ✅ CORREGIDO: nombre de campo
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre', data: 'nombres' }, // ✅ CORREGIDO
        { title: 'Apellidos', data: 'apellidos' }, // ✅ CORREGIDO
        { title: 'Correo', data: 'correo' }, // ✅ CORREGIDO
        { title: 'Teléfono', data: 'telefono' }, // ✅ CORREGIDO
        { title: 'NIT', data: 'nit' }, // ✅ CORREGIDO
        {
            title: 'Acciones',
            data: 'id_cliente', // ✅ CORREGIDO
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombres="${row.nombres}"  
                         data-apellidos="${row.apellidos}"  
                         data-nit="${row.nit}"  
                         data-telefono="${row.telefono}"  
                         data-correo="${row.correo}">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ],
})

const BuscarCliente = async () => {
    console.log('🔄 Iniciando búsqueda de clientes...');
    
    // ✅ USAR LA RUTA DEL FRAMEWORK QUE YA TIENES CONFIGURADA
    const url = '/app03_jemg/clientes/buscarCliente';
    
    try {
        console.log(`🔄 Usando URL: ${url}`);
        
        const respuesta = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        console.log(`📡 Respuesta:`, respuesta.status, respuesta.statusText);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }
        
        const datos = await respuesta.json();
        console.log('✅ Datos recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            console.log('✅ Clientes cargados exitosamente');
            datatable.clear().draw();
            datatable.rows.add(data || []).draw();
            
            Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Éxito!",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000,
            });
        } else {
            console.log('ℹ️ Respuesta del servidor:', mensaje);
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
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            showConfirmButton: true,
        });
    }
}

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    // ✅ CORREGIDO: IDs correctos
    document.getElementById('id_cliente').value = datos.id;
    document.getElementById('nombres').value = datos.nombres;
    document.getElementById('apellidos').value = datos.apellidos;
    document.getElementById('nit').value = datos.nit;
    document.getElementById('telefono').value = datos.telefono;
    document.getElementById('correo').value = datos.correo;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });
}

const limpiarTodo = () => {
    FormClientes.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Limpiar clases de validación
    const inputs = FormClientes.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarCliente = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormClientes, [''])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "Formulario incompleto",
            text: "Debe validar todos los campos",
            showConfirmButton: false,
            timer: 3000,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormClientes);
    const url = '/app03_jemg/clientes/modificarCliente';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 3000,
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
        console.log("Error:", error);
    }
    BtnModificar.disabled = false;
}

const EliminarCliente = async (e) => {
    const idCliente = e.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea ejecutar esta acción?",
        text: "Usted eliminará un cliente",
        showConfirmButton: true,
        confirmButtonText: "Sí",
        confirmButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    const body = new URLSearchParams();
    body.append('id_cliente', idCliente); // ✅ CORREGIDO: nombre del campo

    try {
        const respuesta = await fetch('/app03_jemg/clientes/eliminarCliente', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
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
        console.log("Error:", error);
    }
};

// ✅ VERIFICACIÓN DE ELEMENTOS Y EVENTOS
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado - Verificando elementos...');
    
    // Verificar elementos críticos
    const elementos = {
        FormClientes: document.getElementById('FormClientes'),
        BtnGuardar: document.getElementById('BtnGuardar'),
        BtnModificar: document.getElementById('BtnModificar'),
        BtnLimpiar: document.getElementById('BtnLimpiar'),
        validarTelefono: document.getElementById('telefono'),
        validarNit: document.getElementById('nit'),
        TableClientes: document.getElementById('TableClientes')
    };
    
    // Log de elementos encontrados/no encontrados
    Object.entries(elementos).forEach(([nombre, elemento]) => {
        if (elemento) {
            console.log(`✅ ${nombre} encontrado`);
        } else {
            console.error(`❌ ${nombre} NO encontrado`);
        }
    });
    
    // Solo agregar eventos si los elementos existen
    if (elementos.FormClientes) {
        elementos.FormClientes.addEventListener('submit', GuardarCliente);
        console.log('✅ Evento submit agregado al formulario');
    }
    
    if (elementos.BtnLimpiar) {
        elementos.BtnLimpiar.addEventListener('click', limpiarTodo);
        console.log('✅ Evento click agregado a BtnLimpiar');
    }
    
    if (elementos.BtnModificar) {
        elementos.BtnModificar.addEventListener('click', ModificarCliente);
        console.log('✅ Evento click agregado a BtnModificar');
    }
    
    if (elementos.validarTelefono) {
        elementos.validarTelefono.addEventListener('blur', validacionTelefono);
        console.log('✅ Evento blur agregado a teléfono');
    }
    
    if (elementos.validarNit) {
        elementos.validarNit.addEventListener('blur', EsValidoNit);
        console.log('✅ Evento blur agregado a NIT');
    }
    
    // Eventos del datatable
    datatable.on('click', '.eliminar', EliminarCliente);
    datatable.on('click', '.modificar', llenarFormulario);
    console.log('✅ Eventos del datatable agregados');
    
    // Intentar cargar clientes
    console.log('🔄 Intentando cargar clientes...');
    BuscarCliente();
});