<?php

declare(strict_types=1);

require_login();
require __DIR__ . '/../ui.php';

$user = current_user();

render_header('MUH5 SDK - Chọn máy chủ');
?>
<div class="d-flex flex-column justify-content-center flex-grow-1 py-4">
    <div class="container-tight">
        <div class="text-center mb-4">
            <span class="brand-tag text-muted">MUH5 SDK &mdash; Máy chủ</span>
        </div>

        <div class="card auth-card mx-auto">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Danh sách máy chủ</h3>
                <a href="/?p=logout" class="btn btn-ghost-secondary btn-sm">Đăng xuất</a>
            </div>
            
            <div class="list-group list-group-flush">
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="badge bg-success badge-blink"></span>
                        </div>
                        <div class="col text-truncate">
                            <a href="/?p=play&sid=1" class="text-reset d-block text-decoration-none fw-bold">S1</a>
                            <div class="d-block text-muted text-truncate mt-n1 small">Online</div>
                        </div>
                        <div class="col-auto">
                            <a href="/?p=play&sid=1" class="btn btn-primary btn-sm">Vào game</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer text-center py-2">
                <span class="text-muted brand-tag">Chào <?php echo e($user['display_name'] ?? $user['username']); ?></span>
            </div>
        </div>
    </div>
</div>

<?php render_footer(); ?>
