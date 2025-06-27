<?php if (isset($is_admin) && $is_admin): ?>
<div class="admin-nav-section mb-3">
    <h6 class="text-danger mb-2 px-3">
        <i class="bi bi-shield-check me-2"></i>Admin Controls
    </h6>
    <a class="nav-link text-danger" href="<?= base_url('admin') ?>">
        <i class="bi bi-speedometer2 me-2"></i>Admin Dashboard
    </a>
    <a class="nav-link text-danger" href="<?= base_url('admin/users') ?>">
        <i class="bi bi-people me-2"></i>Manage Users
    </a>
    <hr class="border-secondary">
</div>
<?php endif; ?>
