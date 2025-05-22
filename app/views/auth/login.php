<?php
$pageTitle = 'Login';
$layout = 'layouts/auth';

ob_start();
?>

<div class="bg-white p-8 rounded shadow-md">
    <form action="<?= BASE_URL ?>/login/process" method="POST" class="space-y-6">
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
        </div>
        
        <div class="form-control">
            <label class="label cursor-pointer">
                <span class="label-text">Remember me</span>
                <input type="checkbox" name="remember" class="checkbox checkbox-primary">
            </label>
        </div>
        
        <div class="form-control">
            <button type="submit" class="btn btn-primary w-full">Login</button>
        </div>
    </form>
    
    <div class="text-center mt-4">
        <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="<?= BASE_URL ?>/register" class="text-primary hover:underline">Register</a>
        </p>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 