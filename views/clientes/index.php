<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-users me-2"></i>
                            Gestión de Clientes
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para registro, modificación y eliminación</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-user-plus me-2"></i>
                                Información del Cliente
                            </h4>
                            
                            <form id="FormClientes">
                                <input type="hidden" id="id_cliente" name="id_cliente">

                                <div class="row">
                                    <!-- Nombre -->
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

                                    <!-- Apellido -->
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

                                    <!-- Cédula -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-id-badge"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="cedula" name="cedula" placeholder="Cédula">
                                                <label for="cedula">Número de Cédula</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- NIT -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-file-invoice"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="nit" name="nit" placeholder="NIT">
                                                <label for="nit">Número de NIT</label>
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
                                                <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com">
                                                <label for="email">Correo Electrónico</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                                                <label for="telefono">Teléfono</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Dirección -->
                                    <div class="col-12">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <textarea class="form-control" id="direccion" name="direccion" placeholder="Dirección completa" style="height: 100px"></textarea>
                                                <label for="direccion">Dirección Completa</label>
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
                    <i class="fas fa-address-book me-2"></i>
                    Clientes Existentes
                </h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableClientes">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido</th>
                                <th>Cédula</th>
                                <th>NIT</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Fecha Registro</th>
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

<script src="<?= asset('build/js/clientes/clientes.js') ?>"></script>