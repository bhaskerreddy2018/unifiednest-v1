<?php
$pageTitle = 'Register';
$layout = 'layouts/auth';

ob_start();
?>

<div class="bg-white p-8 rounded shadow-md">
    <form action="<?= BASE_URL ?>/register/process" method="POST" class="space-y-6">
        <?php if (isset($isFirstUser) && $isFirstUser): ?>
            <div class="alert alert-info mb-4">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current flex-shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>You are the first user. You will be registered as a Super Admin.</span>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="form-control">
            <label for="organization_name" class="label">
                <span class="label-text">Organization Name</span>
            </label>
            <input type="text" id="organization_name" name="organization_name" required class="input input-bordered" placeholder="Enter your organization name">
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
                <label for="first_name" class="label">
                    <span class="label-text">First Name</span>
                </label>
                <input type="text" id="first_name" name="first_name" required class="input input-bordered" placeholder="Enter your first name">
            </div>
            
            <div class="form-control">
                <label for="last_name" class="label">
                    <span class="label-text">Last Name</span>
                </label>
                <input type="text" id="last_name" name="last_name" required class="input input-bordered" placeholder="Enter your last name">
            </div>
        </div>
        
        <div class="form-control">
            <label for="email" class="label">
                <span class="label-text">Email</span>
            </label>
            <input type="email" id="email" name="email" required class="input input-bordered" placeholder="Enter your email">
        </div>
        
        <div class="form-control">
            <label for="password" class="label">
                <span class="label-text">Password</span>
            </label>
            <input type="password" id="password" name="password" required class="input input-bordered" placeholder="Enter your password">
            <label class="label">
                <span class="label-text-alt">Password must be at least 8 characters long</span>
            </label>
        </div>
        
        <div class="form-control">
            <label for="confirm_password" class="label">
                <span class="label-text">Confirm Password</span>
            </label>
            <input type="password" id="confirm_password" name="confirm_password" required class="input input-bordered" placeholder="Confirm your password">
        </div>
        
        <div class="form-control">
            <button type="submit" class="btn btn-primary w-full">Register</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="text-sm text-gray-600">
            Already have an account?
            <a href="<?= BASE_URL ?>/login" class="text-primary hover:underline">Login</a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 