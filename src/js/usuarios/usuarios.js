import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

console.log('🚀 Iniciando usuarios.js');

// Detectar base URL automáticamente
const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/');
console.log('🔧 Base URL detectada:', baseUrl);

// Elementos del DOM
const FormUsuarios = document.getElementById('FormUsuarios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');

// DataTable
let datatable;
try {
    datatable = new DataTable('#TableUsuarios', {
        language: lenguaje,
        data: [],
        responsive: true,
        columns: [
            {
                title: 'No.',
                data: 'id_usuario',
                width: '5%',
                render: (data, type, row, meta) => meta.row + 1
            },
            { 
                title: 'Nombre', 
                data: 'nombre',
                render: (data, type, row) => `${data} ${row.apellido}`
            },
            { title: 'Email', data: 'email' },
            { 
                title: 'Rol', 
                data: 'id_rol',
                render: (data) => {
                    // Mapear IDs de rol a nombres (temporal)
                    const roles = {
                        1: 'Administrador',
                        2: 'Usuario',
                        3: 'Supervisor'
                    };
                    return roles[data] || `Rol ${data}`;
                }
            },
            { 
                title: 'Estado', 
                data: 'activo',
                render: (data) => data === 'S' ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-danger">Inactivo</span>'
            },
            {
                title: 'Acciones',
                data: 'id_usuario',
                searchable: false,
                orderable: false,
                render: (data, type, row) => {
                    return `
                    <div class='d-flex justify-content-center gap-1'>
                         <button class='btn btn-warning btn-sm modificar' 
                             data-id="${data}" 
                             data-nombre="${row.nombre || ''}"  
                             data-apellido="${row.apellido || ''}"  
                             data-email="${row.email || ''}"
                             data-id_rol="${row.id_rol || ''}"
                             data-activo="${row.activo || ''}"
                             title="Modificar usuario">
                             <i class='fas fa-edit'></i> Editar
                         </button>
                         <button class='btn btn-danger btn-sm eliminar' 
                             data-id="${data}"
                             title="Eliminar usuario">
                            <i class="fas fa-trash"></i> Eliminar
                         </button>
                         <button class='btn btn-info btn-sm cambiar-estado' 
                             data-id="${data}"
                             title="Cambiar estado">
                            <i class="fas fa-power-off"></i> Estado
                         </button>
                     </div>`;
                }
            }
        ]
    });
    console.log('✅ DataTable inicializado');
} catch (error) {
    console.error('❌ Error al inicializar DataTable:', error);
}

// Buscar usuarios
const BuscarUsuarios = async () => {
    console.log('🔄 Buscando usuarios...');
    
    const url = `${baseUrl}/usuarios/buscarAPI`;
    console.log('🔗 URL:', url);
    
    try {
        const respuesta = await fetch(url, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        
        console.log('📡 Status:', respuesta.status);
        
        if (!respuesta.ok) {
            throw new Error(`HTTP ${respuesta.status}`);
        }
        
        const contentType = respuesta.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await respuesta.text();
            console.error('❌ Respuesta no es JSON:', text.substring(0, 300));
            throw new Error('Respuesta inválida del servidor');
        }
        
        const datos = await respuesta.json();
        console.log('✅ Datos recibidos:', datos);
        
        const { codigo, mensaje, data } = datos;
        
        if (datatable) {
            datatable.clear().draw();
            
            if (codigo === 1 && data && data.length > 0) {
                console.log(`✅ Cargando ${data.length} usuarios`);
                datatable.rows.add(data).draw();
                
                // Actualizar contador
                const contador = document.getElementById('contador-usuarios');
                if (contador) {
                    contador.innerHTML = `<i class="fas fa-user-friends me-1"></i>${data.length} usuario${data.length !== 1 ? 's' : ''}`;
                }
            } else {
                console.log('ℹ️ No hay usuarios');
                
                // Actualizar contador a 0
                const contador = document.getElementById('contador-usuarios');
                if (contador) {
                    contador.innerHTML = `<i class="fas fa-user-friends me-1"></i>0 usuarios`;
                }
                
                Swal.fire({
                    icon: "info",
                    title: "Sin usuarios",
                    text: mensaje || "No hay usuarios registrados",
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        }
        
    } catch (error) {
        console.error('❌ Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: `No se pudieron cargar los usuarios: ${error.message}`,
            showConfirmButton: true
        });
    }
};

// Buscar roles
const BuscarRoles = async () => {
    console.log('🔄 Buscando roles...');
    
    const url = `${baseUrl}/usuarios/roles`;
    
    try {
        const respuesta = await fetch(url, { method: 'GET' });
        
        if (respuesta.ok) {
            const datos = await respuesta.json();
            console.log('✅ Roles recibidos:', datos);
            
            const selectRol = document.getElementById('id_rol');
            if (selectRol && datos.codigo === 1) {
                selectRol.innerHTML = '<option value="">Seleccione un rol</option>';
                datos.data.forEach(rol => {
                    selectRol.innerHTML += `<option value="${rol.id_rol}">${rol.nombre_rol}</option>`;
                });
                console.log('✅ Roles cargados en select');
            }
        }
    } catch (error) {
        console.error('❌ Error al cargar roles:', error);
    }
};

// Modificar usuario  
const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    const formData = new FormData(FormUsuarios);
    const url = `${baseUrl}/usuarios/modificarAPI`;

    try {
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                icon: "success",
                title: "¡Éxito!",
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            limpiarTodo();
            // RECARGAR USUARIOS DESPUÉS DE MODIFICAR
            await BuscarUsuarios();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true
            });
        }
    } catch (error) {
        console.error('❌ Error al modificar:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: `Error al modificar: ${error.message}`,
            showConfirmButton: true
        });
    }
    BtnModificar.disabled = false;
};

// FUNCIÓN GUARDAR USUARIO CORREGIDA
const GuardarUsuario = async (event) => {
    event.preventDefault();
    
    if (BtnGuardar) BtnGuardar.disabled = true;
    
    const formData = new FormData(FormUsuarios);
    const url = `${baseUrl}/usuarios/guardarAPI`;
    
    try {
        const respuesta = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;
        
        if (codigo === 1) {
            // Mostrar mensaje de éxito
            await Swal.fire({
                icon: "success",
                title: "¡Usuario guardado!",
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            
            // Limpiar formulario
            if (FormUsuarios) FormUsuarios.reset();
            
            // ESTO ES LO IMPORTANTE: RECARGAR LA TABLA INMEDIATAMENTE
            console.log('🔄 Recargando tabla de usuarios...');
            await BuscarUsuarios();
            
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true
            });
        }
    } catch (error) {
        console.error('❌ Error al guardar:', error);
        Swal.fire({
            icon: "error",
            title: "Error",
            text: `Error al guardar: ${error.message}`,
            showConfirmButton: true
        });
    }
    
    if (BtnGuardar) BtnGuardar.disabled = false;
};

// Eliminar usuario
const EliminarUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id;

    const confirmacion = await Swal.fire({
        title: "¿Está seguro?",
        text: "¿Desea eliminar este usuario?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const url = `${baseUrl}/usuarios/eliminar?id_usuario=${idUsuario}`;
        
        const respuesta = await fetch(url, { method: 'GET' });
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                icon: "success",
                title: "¡Eliminado!",
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            // RECARGAR USUARIOS DESPUÉS DE ELIMINAR
            await BuscarUsuarios();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true
            });
        }
    } catch (error) {
        console.error('❌ Error al eliminar:', error);
    }
};

// Cambiar estado usuario
const CambiarEstadoUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id;
    
    const body = new URLSearchParams();
    body.append('id_usuario', idUsuario);

    try {
        const url = `${baseUrl}/usuarios/cambiarEstadoAPI`;
        
        const respuesta = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            Swal.fire({
                icon: "success",
                title: "Estado cambiado",
                text: mensaje,
                timer: 2000,
                showConfirmButton: false
            });
            // RECARGAR USUARIOS DESPUÉS DE CAMBIAR ESTADO
            await BuscarUsuarios();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true
            });
        }
    } catch (error) {
        console.error('❌ Error al cambiar estado:', error);
    }
};

// Llenar formulario para modificar
const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('id_usuario').value = datos.id;
    document.getElementById('nombre').value = datos.nombre;
    document.getElementById('apellido').value = datos.apellido;
    document.getElementById('email').value = datos.email;
    document.getElementById('id_rol').value = datos.id_rol;
    
    // Manejar el select de estado
    const selectActivo = document.getElementById('activo');
    if (selectActivo) {
        selectActivo.value = datos.activo;
    }

    // Cambiar botones
    if (BtnGuardar) BtnGuardar.classList.add('d-none');
    if (BtnModificar) BtnModificar.classList.remove('d-none');

    // Scroll al formulario
    if (FormUsuarios) {
        FormUsuarios.scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }
};

// Limpiar formulario
const limpiarTodo = () => {
    if (FormUsuarios) FormUsuarios.reset();
    if (BtnGuardar) BtnGuardar.classList.remove('d-none');
    if (BtnModificar) BtnModificar.classList.add('d-none');
    
    // Limpiar el ID oculto
    const idUsuario = document.getElementById('id_usuario');
    if (idUsuario) idUsuario.value = '';
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado - inicializando usuarios');
    
    // Formulario
    if (FormUsuarios) {
        FormUsuarios.addEventListener('submit', GuardarUsuario);
        console.log('✅ Event listener agregado al formulario');
    }
    
    // Botón modificar
    if (BtnModificar) {
        BtnModificar.addEventListener('click', ModificarUsuario);
        console.log('✅ Event listener agregado a BtnModificar');
    }
    
    // DataTable events
    if (datatable) {
        datatable.on('click', '.eliminar', EliminarUsuario);
        datatable.on('click', '.cambiar-estado', CambiarEstadoUsuario);
        datatable.on('click', '.modificar', llenarFormulario);
        console.log('✅ Event listeners agregados al DataTable');
    }
    
    // Botón limpiar
    if (BtnLimpiar) {
        BtnLimpiar.addEventListener('click', limpiarTodo);
    }
    
    // Cargar datos iniciales
    BuscarUsuarios();
    BuscarRoles();
    
    console.log('✅ Módulo de usuarios inicializado correctamente');
});