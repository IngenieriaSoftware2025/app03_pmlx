// Dashboard JavaScript - Gráficas y funcionalidades
document.addEventListener('DOMContentLoaded', function() {
    
    // Variables globales para las gráficas
    let ventasDiariasChart, reparacionesChart, ingresosChart, topProductosChart, stockMarcasChart;
    
    // Inicializar gráficas
    inicializarGraficas();
    
    // Cargar datos del dashboard
    cargarDatosDashboard();
    
    // Actualizar cada 5 minutos
    setInterval(cargarDatosDashboard, 300000);

    // 1. Gráfica de Ventas Diarias (Línea)
    function crearGraficaVentasDiarias() {
        const ctx = document.getElementById('ventasDiariasChart').getContext('2d');
        ventasDiariasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // Se llena dinámicamente
                datasets: [{
                    label: 'Ventas Diarias (Q)',
                    data: [], // Se llena dinámicamente
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                return 'Ventas: Q. ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Q. ' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfica de Estados de Reparaciones (Dona)
    function crearGraficaReparaciones() {
        const ctx = document.getElementById('reparacionesChart').getContext('2d');
        reparacionesChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Recibidas', 'En Proceso', 'Terminadas', 'Entregadas'],
                datasets: [{
                    data: [0, 0, 0, 0], // Se llena dinámicamente
                    backgroundColor: [
                        '#17a2b8',
                        '#ffc107', 
                        '#28a745',
                        '#007bff'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' reparaciones';
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. Gráfica de Ingresos (Barras)
    function crearGraficaIngresos() {
        const ctx = document.getElementById('ingresosChart').getContext('2d');
        ingresosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [], // Últimos 6 meses
                datasets: [{
                    label: 'Ventas',
                    data: [],
                    backgroundColor: '#28a745',
                    borderRadius: 5,
                    borderSkipped: false
                }, {
                    label: 'Reparaciones',
                    data: [],
                    backgroundColor: '#ffc107',
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Q. ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Q. ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Gráfica Top Productos (Barras Horizontales)
    function crearGraficaTopProductos() {
        const ctx = document.getElementById('topProductosChart').getContext('2d');
        topProductosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [], // Nombres de productos
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: [],
                    backgroundColor: [
                        '#007bff',
                        '#28a745', 
                        '#ffc107',
                        '#dc3545',
                        '#6c757d'
                    ],
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.x + ' unidades vendidas';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // 5. Gráfica de Stock por Marcas (Pie)
    function crearGraficaStockMarcas() {
        const ctx = document.getElementById('stockMarcasChart').getContext('2d');
        stockMarcasChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Samsung', 'Apple', 'Huawei', 'Xiaomi', 'LG', 'Motorola'],
                datasets: [{
                    data: [0, 0, 0, 0, 0, 0], // Se llena dinámicamente
                    backgroundColor: [
                        '#007bff',
                        '#6c757d',
                        '#dc3545', 
                        '#fd7e14',
                        '#6f42c1',
                        '#20c997'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + ' productos';
                            }
                        }
                    }
                }
            }
        });
    }

    // Inicializar todas las gráficas
    function inicializarGraficas() {
        crearGraficaVentasDiarias();
        crearGraficaReparaciones();
        crearGraficaIngresos();
        crearGraficaTopProductos();
        crearGraficaStockMarcas();
    }

    // Cargar datos del dashboard desde el servidor
    async function cargarDatosDashboard() {
        try {
            // Mostrar indicador de carga
            mostrarCargando(true);

            // Petición AJAX al controlador
            const response = await fetch('/dashboard/datos', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error('Error al cargar datos del dashboard');
            }

            const datos = await response.json();

            // Actualizar las métricas principales
            actualizarMetricas(datos.metricas);

            // Actualizar las gráficas
            actualizarTodasLasGraficas(datos.graficas);

            // Actualizar tabla de stock bajo
            actualizarTablaStockBajo(datos.stockBajo);

            // Ocultar indicador de carga
            mostrarCargando(false);

        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('Error al cargar los datos del dashboard', 'error');
            mostrarCargando(false);
        }
    }

    // Actualizar métricas principales
    function actualizarMetricas(metricas) {
        document.getElementById('ventasDelDia').textContent = 'Q. ' + metricas.ventasDelDia.toLocaleString();
        document.getElementById('cantidadVentas').textContent = metricas.cantidadVentas + ' ventas';
        document.getElementById('reparacionesPendientes').textContent = metricas.reparacionesPendientes;
        document.getElementById('stockBajo').textContent = metricas.stockBajo;
        document.getElementById('totalClientes').textContent = metricas.totalClientes;
        document.getElementById('ventasMes').textContent = 'Q. ' + metricas.ventasMes.toLocaleString();
        document.getElementById('reparacionesMes').textContent = 'Q. ' + metricas.reparacionesMes.toLocaleString();
    }

    // Actualizar todas las gráficas
    function actualizarTodasLasGraficas(datos) {
        // Ventas Diarias
        ventasDiariasChart.data.labels = datos.ventasDiarias.fechas;
        ventasDiariasChart.data.datasets[0].data = datos.ventasDiarias.valores;
        ventasDiariasChart.update('active');

        // Reparaciones
        reparacionesChart.data.datasets[0].data = datos.reparaciones;
        reparacionesChart.update('active');

        // Ingresos
        ingresosChart.data.labels = datos.ingresos.meses;
        ingresosChart.data.datasets[0].data = datos.ingresos.ventas;
        ingresosChart.data.datasets[1].data = datos.ingresos.reparaciones;
        ingresosChart.update('active');

        // Top Productos
        topProductosChart.data.labels = datos.topProductos.nombres;
        topProductosChart.data.datasets[0].data = datos.topProductos.cantidades;
        topProductosChart.update('active');

        // Stock por Marcas
        stockMarcasChart.data.datasets[0].data = datos.stockMarcas;
        stockMarcasChart.update('active');
    }

    // Actualizar tabla de stock bajo
    function actualizarTablaStockBajo(productos) {
        const tbody = document.querySelector('#TableStockBajo tbody');
        tbody.innerHTML = '';

        if (productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay productos con stock bajo</td></tr>';
            return;
        }

        productos.forEach(producto => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${producto.modelo}</td>
                <td>${producto.marca}</td>
                <td>
                    <span class="badge bg-${producto.stock <= 2 ? 'danger' : 'warning'}">
                        ${producto.stock}
                    </span>
                </td>
                <td>Q. ${producto.precio_venta}</td>
                <td>
                    <span class="badge bg-${producto.stock <= 2 ? 'danger' : 'warning'}">
                        ${producto.stock <= 2 ? 'Crítico' : 'Bajo'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="editarProducto(${producto.id_inventario})">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Función para editar producto (llamada desde la tabla)
    window.editarProducto = function(id) {
        window.location.href = '/inventario?editar=' + id;
    };

    // Mostrar/ocultar indicador de carga
    function mostrarCargando(mostrar) {
        // Puedes implementar un spinner o indicador de carga aquí
        if (mostrar) {
            console.log('Cargando datos...');
        } else {
            console.log('Datos cargados');
        }
    }

    // Mostrar alertas
    function mostrarAlerta(mensaje, tipo = 'info') {
        // Implementar sistema de alertas si es necesario
        console.log(`${tipo.toUpperCase()}: ${mensaje}`);
    }

    // Función pública para actualizar gráficas desde otros archivos
    window.actualizarGraficasDashboard = function(datos) {
        actualizarTodasLasGraficas(datos);
    };
});

// Funciones de utilidad
function formatearMoneda(valor) {
    return 'Q. ' + parseFloat(valor).toLocaleString('es-GT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-GT');
}