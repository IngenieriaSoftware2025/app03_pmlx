<div class="container-fluid py-4">
        <!-- Formulario Principal -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                    <div class="card-header-custom text-center">
                        <h2 class="mb-0 floating-animation">
                            <i class="fas fa-warehouse me-2"></i>
                            Gestión de Inventario
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Sistema completo para control de stock de celulares</p>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="form-section">
                            <h4 class="section-title">
                                <i class="fas fa-mobile-alt me-2"></i>
                                Información del Producto
                            </h4>
                            
                            <form id="FormInventario">
                                <input type="hidden" id="id_inventario" name="id_inventario">

                                <div class="row">
                                    <!-- Modelo -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-mobile-alt"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Modelo del celular">
                                                <label for="modelo">Modelo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Marca -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-trademark"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="id_marca" name="id_marca">
                                                    <option value="">Seleccionar Marca</option>
                                                    <!-- Se llenará dinámicamente -->
                                                </select>
                                                <label for="id_marca">Marca</label>
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

                                    <!-- Número de Serie -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-hashtag"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="text" class="form-control" id="numero_serie" name="numero_serie" placeholder="Número de serie">
                                                <label for="numero_serie">Número de Serie</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Estado del Dispositivo -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-star"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <select class="form-select" id="estado_dispositivo" name="estado_dispositivo">
                                                    <option value="">Seleccionar Estado</option>
                                                    <option value="Nuevo">Nuevo</option>
                                                    <option value="Usado">Usado</option>
                                                    <option value="Reacondicionado">Reacondicionado</option>
                                                    <option value="Reparado">Reparado</option>
                                                    <option value="Para Repuestos">Para Repuestos</option>
                                                </select>
                                                <label for="estado_dispositivo">Estado del Dispositivo</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Stock -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-boxes"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="stock" name="stock" min="0" placeholder="Cantidad en stock">
                                                <label for="stock">Stock</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Precio de Compra -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-money-bill"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="precio_compra" name="precio_compra" step="0.01" min="0" placeholder="Precio de compra">
                                                <label for="precio_compra">Precio de Compra</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Precio de Venta -->
                                    <div class="col-lg-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text">
                                                <i class="fas fa-dollar-sign"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <input type="number" class="form-control" id="precio_venta" name="precio_venta" step="0.01" min="0" placeholder="Precio de venta">
                                                <label for="precio_venta">Precio de Venta</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Descripción -->
                                    <div class="col-12">
                                        <div class="input-group mb-4">
                                            <span class="input-group-text">
                                                <i class="fas fa-align-left"></i>
                                            </span>
                                            <div class="form-floating flex-grow-1">
                                                <textarea class="form-control" id="descripcion" name="descripcion" placeholder="Descripción del producto" style="height: 100px"></textarea>
                                                <label for="descripcion">Descripción</label>
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
                                <button class="btn btn-info" type="button" id="BtnBuscarImei">
                                    <i class="fas fa-search me-2"></i>Buscar por IMEI
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Alertas -->
<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg mb-3" style="border-radius: 10px; border: 1px solid #28a745;">
            <div class="card-body p-3">
                <h5 class="text-center mb-3">
                    <i class="fas fa-filter me-2"></i>
                    Filtros y Alertas
                </h5>
                
                <div class="row">
                    <div class="col-lg-3">
                        <button class="btn btn-outline-primary w-100" id="BtnFiltrarTodos">
                            <i class="fas fa-list me-2"></i>Todos
                        </button>
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-outline-success w-100" id="BtnFiltrarStock">
                            <i class="fas fa-check me-2"></i>Con Stock
                        </button>
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-outline-warning w-100" id="BtnStockBajo">
                            <i class="fas fa-exclamation-triangle me-2"></i>Stock Bajo
                        </button>
                    </div>
                    <div class="col-lg-3">
                        <button class="btn btn-outline-danger w-100" id="BtnSinStock">
                            <i class="fas fa-times me-2"></i>Sin Stock
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Inventario -->
<div class="row justify-content-center p-3">
    <div class="col-lg-12">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">
                    <i class="fas fa-list-alt me-2"></i>
                    Inventario de Celulares
                </h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableInventario">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Modelo</th>
                                <th>Marca</th>
                                <th>IMEI</th>
                                <th>Estado</th>
                                <th>Stock</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Fecha Ingreso</th>
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

<script src="<?= asset('build/js/inventario/inventario.js') ?>"></script>