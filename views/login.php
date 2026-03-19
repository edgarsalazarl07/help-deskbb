<div class="d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-md-4 col-sm-8 col-12 container-glass">
        <div class="text-center">
            <h3 class="mb-4">
                <img src="public/img/logo.png" alt="Logo" class="img-fluid mb-2" style="max-height: 80px;"><br>
                <span style="color: #4e73df; font-weight: 800;">Help - Desk</span>
            </h3>
            
            <?php // Error message block removed to favor AJAX feedback ?>

            <form id="loginForm">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group mb-3 text-left">
                    <label for="usuario" style="font-weight: 600;">Usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-user text-primary"></i></span>
                        </div>
                        <input type="text" class="form-control border-left-0" id="usuario" name="usuario" required placeholder="Tu usuario">
                    </div>
                </div>
                
                <div class="form-group mb-4 text-left">
                    <label for="password" style="font-weight: 600;">Contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-lock text-primary"></i></span>
                        </div>
                        <input type="password" class="form-control border-left-0" id="password" name="password" required placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                    Entrar al Sistema
                </button>
            </form>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                $(document).ready(function() {
                    $('#loginForm').submit(function(e) {
                        e.preventDefault();
                        
                        const formData = $(this).serialize();
                        
                        // Show loading state
                        const btn = $(this).find('button[type="submit"]');
                        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Procesando...');

                        $.ajax({
                            url: 'controllers/UsuarioController.php?action=login',
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Éxito',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.href = response.redirect;
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                    btn.prop('disabled', false).text('Entrar al Sistema');
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Hubo un error al procesar la solicitud al servidor.'
                                });
                                btn.prop('disabled', false).text('Entrar al Sistema');
                            }
                        });
                    });
                });
            </script>
            <div class="mt-4 text-muted">
                <small>&copy; <?= date('Y') ?> - Help Desk Premium</small>
            </div>
        </div>
    </div>
</div>
