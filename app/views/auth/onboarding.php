<?php
$pageTitle = 'Complete Setup';
$layout = 'layouts/auth';

ob_start();
?>

<h3 class="text-lg font-bold mb-4">Setup Your Organization</h3>
<p class="text-sm text-gray-600 mb-6">You are the first user. Please set up your organization to get started.</p>

<form action="<?= BASE_URL ?>/onboarding/process" method="POST">
    <div class="space-y-4">
        <!-- Organization Info -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Organization Name *</span>
            </label>
            <input type="text" name="organization_name" class="input input-bordered" required autofocus>
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Organization Email</span>
            </label>
            <input type="email" name="organization_email" class="input input-bordered">
        </div>

        <div class="form-control">
            <label class="label">
                <span class="label-text">Organization Phone</span>
            </label>
            <input type="text" name="organization_phone" class="input input-bordered">
        </div>

        <!-- Department -->
        <div class="form-control">
            <label class="label">
                <span class="label-text">Department Name</span>
            </label>
            <input type="text" name="department_name" class="input input-bordered" placeholder="e.g. General, Administration">
            <label class="label">
                <span class="label-text-alt">Leave blank to create a default "General" department</span>
            </label>
        </div>

        <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary">Complete Setup</button>
        </div>
    </div>
</form>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 