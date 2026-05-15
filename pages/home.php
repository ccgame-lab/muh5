<?php

declare(strict_types=1);

require __DIR__ . '/../ui.php';

$user = current_user();

render_header('MUH5 SDK');

?>
<div class="d-flex flex-column justify-content-center flex-grow-1 py-4">
    <div class="container-tight">
        <div class="text-center mb-4">
            <span class="brand-tag text-muted">MUH5 SDK &mdash; Game access gateway</span>
        </div>

        <div class="card auth-card mx-auto">
            <div class="card-body p-4">

                <?php if ($user): ?>
                    <!-- Đã đăng nhập -->
                    <div class="text-center mb-3">
                        <div class="avatar avatar-md mb-2" style="background:linear-gradient(135deg,#206bc4,#0ca678)">
                            <?php echo strtoupper(substr($user['display_name'] ?? $user['username'], 0, 1)); ?>
                        </div>
                        <div class="fw-bold"><?php echo e($user['display_name'] ?? $user['username']); ?></div>
                        <div class="text-muted small"><?php echo e($user['username']); ?></div>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="/?p=servers" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            Vào game
                        </a>
                        <a href="/?p=logout" class="btn btn-ghost-secondary btn-sm">Đăng xuất</a>
                    </div>

                <?php else: ?>
                    <!-- Chưa đăng nhập - Login form -->
                    <h3 class="card-title text-center mb-1">Đăng nhập</h3>
                    <p class="text-muted text-center small mb-3">Nhập tài khoản để tiếp tục</p>

                    <?php if (!empty($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                            <div><?php echo e($_GET['error']); ?></div>
                            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/?p=login" autocomplete="on">
                        <div class="mb-3">
                            <label class="form-label" for="username">Tài khoản</label>
                            <input type="text" id="username" name="username" class="form-control" placeholder="username" required autocomplete="username"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="password">Mật khẩu</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password"/>
                        </div>
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        </div>
                    </form>

                    <hr class="my-3"/>

                    <!-- Register: chưa mở -->
                    <div class="text-center">
                        <button class="btn btn-ghost-secondary btn-sm" disabled title="Đăng ký chưa mở">
                            Đăng ký &mdash; Chưa mở
                        </button>
                    </div>

                <?php endif; ?>

            </div>
            <div class="card-footer text-center py-2">
                <span class="text-muted brand-tag">Launcher / SSO ready</span>
                <!-- future launcher/SSO authflow entry -->
            </div>
        </div>

    </div>
</div>

<?php render_footer(); ?>