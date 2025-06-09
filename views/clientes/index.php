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
                                    <!-- Nombres -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="nombres" name="nombres" placeholder="Nombres">
                                                <label for="nombres">Nombres</label>
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
                                                <input type="text" class="form-control" id="apellidos" name="apellidos" placeholder="Apellidos">
                                                <label for="apellidos">Apellidos</label>
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
                                                <input type="number" class="form-control" id="telefono" name="telefono" placeholder="Teléfono">
                                                <label for="telefono">Teléfono (8 dígitos)</label>
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

                                    <!-- Correo -->
                                    <div class="col-12">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@ejemplo.com">
                                                <label for="correo">Correo Electrónico</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    Limpiar
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
                <h3 class="text-center">clientes existentes</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableClientes">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<script src="<?= asset('build/js/clientes/clientes.js') ?>"></script>