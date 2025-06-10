<div class="container-fluid py-4">
    <!-- Formulario Principal -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-11 col-xl-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-user-cog me-2"></i>
                        Gestión de Usuarios
                    </h2>
                    <p class="mb-0 mt-2">Sistema completo para registro, modificación y eliminación de usuarios</p>
                </div>
                
                <div class="card-body p-4">
                    <h4 class="border-bottom pb-2 mb-4">
                        <i class="fas fa-user-plus me-2"></i>
                        Información del Usuario
                    </h4>
                    
                    <form id="FormUsuarios">
                        <input type="hidden" id="id_usuario" name="id_usuario">

                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" required>
                                        <label for="nombre">Nombre *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Apellido -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-id-card text-success"></i>
                                    </span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" required>
                                        <label for="apellido">Apellido *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope text-info"></i>
                                    </span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com" required>
                                        <label for="email">Correo Electrónico *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock text-warning"></i>
                                    </span>
                                    <div class="form-floating flex-grow-1">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña">
                                        <label for="password">Contraseña</label>
                                    </div>
                                </div>
                                <small class="text-muted">Dejar vacío para mantener la contraseña actual (solo en modificación)</small>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user-tag text-purple"></i>
                                    </span>
                                    <div class="form-floating flex-grow-1">
                                        <select class="form-select" id="id_rol" name="id_rol" required>
                                            <option value="">Seleccionar Rol</option>
                                            <!-- Se llenarán dinámicamente desde el JavaScript -->
                                        </select>
                                        <label for="id_rol">Rol del Usuario *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-toggle-on text-danger"></i>
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

                        <!-- Botones -->
                        <div class="row justify-content-center mt-4 pt-3">
                            <div class="col-auto">
                                <button class="btn btn-success btn-lg" type="submit" id="BtnGuardar">
                                    <i class="fas fa-save me-2"></i>Guardar Usuario
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-warning btn-lg d-none" type="button" id="BtnModificar">
                                    <i class="fas fa-edit me-2"></i>Modificar Usuario
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary btn-lg" type="button" id="BtnLimpiar">
                                    <i class="fas fa-broom me-2"></i>Limpiar Formulario
                                </button>
                                
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3 class="mb-0">
                            <i class="fas fa-users me-2 text-primary"></i>
                            Usuarios Registrados
                        </h3>
                        <span class="badge bg-primary fs-6" id="contador-usuarios">
                            <i class="fas fa-user-friends me-1"></i>0 usuarios
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover w-100" id="TableUsuarios">
                            <!-- Las columnas se definirán en JavaScript -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/usuarios/usuarios.js') ?>"></script>