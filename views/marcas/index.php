<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-mobile-alt me-2"></i>
                            Gestión de Marcas
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para registro, modificación y eliminación de marcas</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-tag me-2"></i>
                                Información de la Marca
                            </h4>
                            
                            <form id="FormMarcas">
                                <input type="hidden" id="id_marca" name="id_marca">

                                <div class="row">
                                    <!-- Nombre de la Marca -->
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-trademark"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="nombre_marca" name="nombre_marca" placeholder="Nombre de la Marca">
                                                <label for="nombre_marca">Nombre de la Marca</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Descripción -->
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-align-left"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción de la marca" style="height: 100px"></textarea>
                                                <label for="descripcion">Descripción</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-12">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-toggle-on"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="activo" name="activo">
                                                    <option value="S">Activa</option>
                                                    <option value="N">Inactiva</option>
                                                </select>
                                                <label for="activo">Estado de la Marca</label>
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
                    <i class="fas fa-list me-2"></i>
                    Marcas Existentes
                </h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableMarcas">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre de la Marca</th>
                                <th>Descripción</th>
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

<script src="<?= asset('build/js/marcas/marcas.js') ?>"></script>