<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-cash-register me-2"></i>
                            Gestión de Ventas
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para registro y control de ventas de celulares</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <form id="FormVentas">
                            <input type="hidden" id="id_venta" name="id_venta">
                            <input type="hidden" id="id_usuario" name="id_usuario" value="1"> <!-- Usuario logueado -->

                            <!-- Información del Cliente -->
                            <div class="form-section mb-4">
                                <h4 class="section-title">
                                    <i class="fas fa-user me-2"></i>
                                    Información del Cliente
                                </h4>
                                
                                <div class="row">
                                    <!-- Cliente -->
                                    <div class="col-lg-8">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
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

                                    <!-- Fecha de Venta -->
                                    <div class="col-lg-4">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="datetime-local" class="form-control" id="fecha_venta" name="fecha_venta">
                                                <label for="fecha_venta">Fecha y Hora</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Productos -->
                            <div class="form-section mb-4">
                                <h4 class="section-title">
                                    <i class="fas fa-mobile-alt me-2"></i>
                                    Productos a Vender
                                </h4>
                                
                                <div class="row">
                                    <!-- Producto -->
                                    <div class="col-lg-5">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-box"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="producto_select">
                                                    <option value="">Seleccionar Producto</option>
                                                    <!-- Se llenará dinámicamente con inventario -->
                                                </select>
                                                <label for="producto_select">Producto</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="col-lg-2">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-hashtag"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="cantidad_producto" min="1" value="1">
                                                <label for="cantidad_producto">Cantidad</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Precio Unitario -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="precio_unitario" step="0.01" readonly>
                                                <label for="precio_unitario">Precio Unitario</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón Agregar -->
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-primary w-100 h-100" id="BtnAgregarProducto">
                                            <i class="fas fa-plus me-2"></i>Agregar
                                        </button>
                                    </div>
                                </div>

                                <!-- Tabla de Productos Agregados -->
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped table-bordered" id="TableProductosVenta">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Producto</th>
                                                <th>Marca</th>
                                                <th>Cantidad</th>
                                                <th>Precio Unit.</th>
                                                <th>Subtotal</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Se llena dinámicamente -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Totales -->
                            <div class="form-section mb-4">
                                <h4 class="section-title">
                                    <i class="fas fa-calculator me-2"></i>
                                    Totales de la Venta
                                </h4>
                                
                                <div class="row">
                                    <!-- Subtotal -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-coins"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="subtotal" name="subtotal" step="0.01" readonly>
                                                <label for="subtotal">Subtotal</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Impuesto -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-percent"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="impuesto" name="impuesto" step="0.01" readonly>
                                                <label for="impuesto">Impuesto (12%)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Total -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="total" name="total" step="0.01" readonly>
                                                <label for="total">Total</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado -->
                                    <div class="col-lg-3">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="estado_venta" name="estado_venta">
                                                    <option value="Completada">Completada</option>
                                                    <option value="Pendiente">Pendiente</option>
                                                </select>
                                                <label for="estado_venta">Estado</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Observaciones -->
                                <div class="row">
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
                                        <i class="fas fa-save me-2"></i>Guardar Venta
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
                                        <i class="fas fa-print me-2"></i>Imprimir
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Ventas Realizadas -->
        <div class="row justify-content-center p-3">
            <div class="col-lg-12">
                <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                    <div class="card-body p-3">
                        <h3 class="text-center">
                            <i class="fas fa-history me-2"></i>
                            Ventas Realizadas
                        </h3>

                        <div class="table-responsive p-2">
                            <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableVentas">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Fecha</th>
                                        <th>Subtotal</th>
                                        <th>Impuesto</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                        <th>Usuario</th>
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

<script src="<?= asset('build/js/ventas/ventas.js') ?>"></script>