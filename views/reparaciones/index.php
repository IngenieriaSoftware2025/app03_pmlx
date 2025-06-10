<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-tools me-2"></i>
                            Gestión de Reparaciones
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para control de reparaciones de celulares</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="FormReparaciones">
                            <input type="hidden" id="id_reparacion" name="id_reparacion">

                            <!-- Información del Cliente y Dispositivo -->
                            <div class="form-section mb-4">
                                <h4 class="section-title">
                                    <i class="fas fa-user me-2"></i>
                                    Cliente y Dispositivo
                                </h4>
                                
                                <div class="row">
                                    <!-- Cliente -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-circle"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="id_cliente" name="id_cliente">
                                                    <option value="">Seleccionar Cliente</option>
                                                    <!-- Se llenará dinámicamente -->
                                                </select>
                                                <label for="id_cliente">Cliente</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fecha de Ingreso -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-plus"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="datetime-local" class="form-control" id="fecha_ingreso" name="fecha_ingreso">
                                                <label for="fecha_ingreso">Fecha de Ingreso</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tipo de Celular -->
                                    <div class="col-lg-4">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-mobile-alt"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="tipo_celular" name="tipo_celular">
                                                    <option value="">Seleccionar Tipo</option>
                                                    <option value="Smartphone">Smartphone</option>
                                                    <option value="Celular Básico">Celular Básico</option>
                                                    <option value="Tablet">Tablet</option>
                                                </select>
                                                <label for="tipo_celular">Tipo de Celular</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Marca -->
                                    <div class="col-lg-4">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-trademark"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="marca_celular" name="marca_celular" placeholder="Marca del celular">
                                                <label for="marca_celular">Marca</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modelo -->
                                    <div class="col-lg-4">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-tag"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="modelo_celular" name="modelo_celular" placeholder="Modelo del celular">
                                                <label for="modelo_celular">Modelo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- IMEI -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-barcode"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="imei" name="imei" placeholder="IMEI del dispositivo">
                                                <label for="imei">IMEI</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Motivo de Ingreso -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="motivo_ingreso" name="motivo_ingreso">
                                                    <option value="">Seleccionar Motivo</option>
                                                    <option value="Pantalla rota">Pantalla rota</option>
                                                    <option value="No enciende">No enciende</option>
                                                    <option value="Problema de batería">Problema de batería</option>
                                                    <option value="Problema de carga">Problema de carga</option>
                                                    <option value="Problema de audio">Problema de audio</option>
                                                    <option value="Problema de software">Problema de software</option>
                                                    <option value="Cambio de repuestos">Cambio de repuestos</option>
                                                    <option value="Mojado">Mojado</option>
                                                    <option value="Otros">Otros</option>
                                                </select>
                                                <label for="motivo_ingreso">Motivo de Ingreso</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Descripción del Problema -->
                                    <div class="col-12">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-clipboard-list"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <textarea class="form-control" id="descripcion_problema" name="descripcion_problema" placeholder="Descripción detallada del problema" style="height: 100px"></textarea>
                                                <label for="descripcion_problema">Descripción del Problema</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado y Asignación -->
                            <div class="form-section mb-4">
                                <h4 class="section-title">
                                    <i class="fas fa-cogs me-2"></i>
                                    Estado y Asignación
                                </h4>
                                
                                <div class="row">
                                    <!-- Estado de Reparación -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-tasks"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="estado_reparacion" name="estado_reparacion">
                                                    <option value="Recibido">Recibido</option>
                                                    <option value="En Proceso">En Proceso</option>
                                                    <option value="Terminado">Terminado</option>
                                                    <option value="Entregado">Entregado</option>
                                                    <option value="Cancelado">Cancelado</option>
                                                </select>
                                                <label for="estado_reparacion">Estado</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trabajador Asignado -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-user-cog"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="id_trabajador" name="id_trabajador">
                                                    <option value="">Sin Asignar</option>
                                                    <!-- Se llenará dinámicamente con usuarios -->
                                                </select>
                                                <label for="id_trabajador">Trabajador</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fecha Entrega Estimada -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar-check"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="date" class="form-control" id="fecha_entrega_estimada" name="fecha_entrega_estimada">
                                                <label for="fecha_entrega_estimada">Entrega Estimada</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Costo de Reparación -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="costo_reparacion" name="costo_reparacion" step="0.01" min="0" placeholder="0.00">
                                                <label for="costo_reparacion">Costo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="col-12">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-sticky-note"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <textarea class="form-control" id="observaciones" name="observaciones" placeholder="Observaciones adicionales" style="height: 80px"></textarea>
                                                <label for="observaciones">Observaciones</label>
                                            </div>
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

                                <div class="col-auto">
                                    <button class="btn btn-info" type="button" id="BtnImprimir">
                                        <i class="fas fa-print me-2"></i>Orden de Trabajo
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros por Estado -->
        <div class="row justify-content-center p-3">
            <div class="col-lg-10">
                <div class="card custom-card shadow-lg mb-3" style="border-radius: 10px; border: 1px solid #28a745;">
                    <div class="card-body p-3">
                        <h5 class="text-center mb-3">
                            <i class="fas fa-filter me-2"></i>
                            Filtros por Estado
                        </h5>
                        
                        <div class="row">
                            <div class="col-lg-2">
                                <button class="btn btn-outline-primary w-100" id="BtnFiltrarTodos">
                                    <i class="fas fa-list me-2"></i>Todas
                                </button>
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-outline-info w-100" id="BtnFiltrarRecibido">
                                    <i class="fas fa-inbox me-2"></i>Recibidas
                                </button>
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-outline-warning w-100" id="BtnFiltrarProceso">
                                    <i class="fas fa-cog me-2"></i>En Proceso
                                </button>
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-outline-success w-100" id="BtnFiltrarTerminado">
                                    <i class="fas fa-check me-2"></i>Terminadas
                                </button>
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-outline-primary w-100" id="BtnFiltrarEntregado">
                                    <i class="fas fa-check-double me-2"></i>Entregadas
                                </button>
                            </div>
                            <div class="col-lg-2">
                                <button class="btn btn-outline-danger w-100" id="BtnFiltrarVencidas">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Vencidas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Reparaciones -->
        <div class="row justify-content-center p-3">
            <div class="col-lg-12">
                <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                    <div class="card-body p-3">
                        <h3 class="text-center">
                            <i class="fas fa-wrench me-2"></i>
                            Reparaciones Registradas
                        </h3>

                        <div class="table-responsive p-2">
                            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableReparaciones">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Dispositivo</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                        <th>Trabajador</th>
                                        <th>Fecha Ingreso</th>
                                        <th>Fecha Estimada</th>
                                        <th>Costo</th>
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

<script src="<?= asset('build/js/reparaciones/reparaciones.js') ?>"></script>