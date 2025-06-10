<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-user-cog me-2"></i>
                            Gestión de Usuarios
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para registro, modificación y eliminación de usuarios</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-user-plus me-2"></i>
                                Información del Usuario
                            </h4>
                            
                            <form id="FormUsuarios">
                                <input type="hidden" id="id_usuario" name="id_usuario">

                                <div class="row">
                                    <!-- Nombres -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre">
                                                <label for="nombre">Nombre</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Apellidos -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-card"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido">
                                                <label for="apellido">Apellido</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com">
                                                <label for="email">Correo Electrónico</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                                                <label for="password">Contraseña</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rol -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-tag"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="id_rol" name="id_rol">
                                                    <option value="">Seleccionar Rol</option>
                                                    <option value="1">Administrador</option>
                                                    <option value="2">Empleado</option>
                                                    <option value="3">Gerente</option>
                                                </select>
                                                <label for="id_rol">Rol del Usuario</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-toggle-on"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="activo" name="activo">
                                                    <option value="S">Activo</option>
                                                    <option value="N">Inactivo</option>
                                                </select>
                                                <label for="activo">Estado del Usuario</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    <i class="fas fa-save me-2"></i>Guardar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    <i class="fas fa-edit me-2"></i>Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    <i class="fas fa-broom me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">
                    <i class="fas fa-users me-2"></i>
                    Usuarios Existentes
                </h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableUsuarios">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script src="<?= asset('build/js/usuarios/usuarios.js') ?>"></script>