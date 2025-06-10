<div class="container-fluid py-4">
    <!-- Header del Dashboard -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom-card shadow-lg mb-4" style="border-radius: 20px;">
                <div class="card-header-custom text-center">
                    <h2 class="mb-0 floating-animation">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard - Panel de Control
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Resumen general del sistema de inventario</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row justify-content-center mb-4">
        <!-- Ventas del Día -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card custom-card shadow-lg h-100" style="border-radius: 15px; border-left: 5px solid #28a745;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Ventas del Día</h6>
                            <h3 class="text-success" id="ventasDelDia">Q. 0.00</h3>
                            <small class="text-muted" id="cantidadVentas">0 ventas</small>
                        </div>
                        <div class="icon-large text-success">
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reparaciones Pendientes -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card custom-card shadow-lg h-100" style="border-radius: 15px; border-left: 5px solid #ffc107;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Reparaciones</h6>
                            <h3 class="text-warning" id="reparacionesPendientes">0</h3>
                            <small class="text-muted">Pendientes</small>
                        </div>
                        <div class="icon-large text-warning">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card custom-card shadow-lg h-100" style="border-radius: 15px; border-left: 5px solid #dc3545;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Stock Bajo</h6>
                            <h3 class="text-danger" id="stockBajo">0</h3>
                            <small class="text-muted">Productos</small>
                        </div>
                        <div class="icon-large text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clientes Registrados -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card custom-card shadow-lg h-100" style="border-radius: 15px; border-left: 5px solid #007bff;">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Clientes</h6>
                            <h3 class="text-primary" id="totalClientes">0</h3>
                            <small class="text-muted">Registrados</small>
                        </div>
                        <div class="icon-large text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficas Principales -->
    <div class="row justify-content-center mb-4">
        <!-- Gráfica de Ventas Diarias -->
        <div class="col-lg-8">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2 text-success"></i>
                        Ventas de los Últimos 30 Días
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="ventasDiariasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfica de Estados de Reparaciones -->
        <div class="col-lg-4">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-cog me-2 text-warning"></i>
                        Estados de Reparaciones
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="reparacionesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Más Gráficas -->
    <div class="row justify-content-center mb-4">
        <!-- Ingresos Mensuales -->
        <div class="col-lg-6">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-money-bill-wave me-2 text-info"></i>
                        Ingresos: Ventas vs Reparaciones
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="ingresosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Productos Vendidos -->
        <div class="col-lg-6">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-trophy me-2 text-primary"></i>
                        Top 5 Productos Más Vendidos
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="topProductosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock por Marcas -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-6">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-boxes me-2 text-secondary"></i>
                        Distribución de Stock por Marcas
                    </h5>
                    <div style="height: 300px;">
                        <canvas id="stockMarcasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accesos Rápidos -->
        <div class="col-lg-6">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-bolt me-2 text-info"></i>
                        Accesos Rápidos
                    </h5>
                    
                    <div class="d-grid gap-3">
                        <a href="/ventas" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-cash-register me-2"></i>Nueva Venta
                        </a>
                        <a href="/reparaciones" class="btn btn-outline-warning btn-lg">
                            <i class="fas fa-tools me-2"></i>Nueva Reparación
                        </a>
                        <a href="/inventario" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Agregar Producto
                        </a>
                        <a href="/clientes" class="btn btn-outline-info btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                        </a>
                    </div>

                    <!-- Resumen Rápido -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-center mb-3">Resumen del Mes</h6>
                        <div class="row text-center">
                            <div class="col-6">
                                <h5 class="text-success" id="ventasMes">Q. 0.00</h5>
                                <small class="text-muted">Ventas</small>
                            </div>
                            <div class="col-6">
                                <h5 class="text-warning" id="reparacionesMes">Q. 0.00</h5>
                                <small class="text-muted">Reparaciones</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos con Stock Bajo -->
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card custom-card shadow-lg" style="border-radius: 15px;">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Productos con Stock Bajo (Requieren Atención)
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="TableStockBajo">
                            <thead class="table-dark">
                                <tr>
                                    <th>Modelo</th>
                                    <th>Marca</th>
                                    <th>Stock Actual</th>
                                    <th>Precio Venta</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="/inventario" class="btn btn-outline-danger">
                            Ver Todo el Inventario
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= asset('build/js/dashboard/dashboard.js') ?>"></script>