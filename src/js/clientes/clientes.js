import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import {validarFormulario } from "../funciones";
import { lenguaje } from "../funciones";

const FormClientes = document.getElementById('FormCLientes');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const validarTelefono = document.getElementById('telefono');
const validarNit = document.getElementById('nit');

const validacionTelefono = () => {
    const cantidadDigitos = validarTelefono.value;

    if (cantidadDigitos.length < 1) {
        validarTelefono.classList.remove('is_valid','is_invalid')
    } else {
        if (cantidadDigitos.length != 8) {
            Swal.fire({
                position:"center",
                icon: "warning",
                title:"Datos invalidos ¡PENDEJO!",
                text: "Ingresa bien tu numero pendejo",
                timer: 2000
            });
            
        } else {
            
        }
        
    }
}