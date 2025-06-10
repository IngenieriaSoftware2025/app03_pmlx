<div class="container-fluid">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4 col-md-6">
            <div class="card custom-card shadow-lg" style="border-radius: 20px;">
                <div class="card-header-custom text-center">
                    <h2 class="mb-0 floating-animation">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Sistema de Inventario
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Reparación y Venta de Celulares</p>
                </div>
                
                <div class="card-body p-4">
                    <div class="form-section">
                        <h4 class="section-title text-center">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Iniciar Sesión
                        </h4>
                        
                        <!-- Alertas -->
                        <div id="alertas"></div>
                        
                        <form id="FormLogin">
                            <div class="row">
                                <!-- Email -->
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="fas fa-envelope"></i>
                                        </span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="correo@ejemplo.com" required>
                                            <label for="email">Correo Electrónico</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <div class="form-floating flex-grow-1">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                            <label for="password">Contraseña</label>
                                        </div>
                                        <span class="input-group-text" style="cursor: pointer;" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Remember Me -->
                                <div class="col-12">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="recordar" name="recordar">
                                        <label class="form-check-label" for="recordar">
                                            Recordar mis datos
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center mt-3">
                                <div class="col-auto">
                                    <button class="btn btn-primary w-100" type="submit" id="BtnLogin">
                                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Info del Sistema -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Usuario de Prueba:</strong><br>
                            Email: admin@inventario.com<br>
                            Contraseña: admin123
                        </small>
                    </div>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">¿Problemas para acceder? Contacta al administrador</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/auth/login.js') ?>"></script>