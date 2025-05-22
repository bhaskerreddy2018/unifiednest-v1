<?php
$pageTitle = 'Page Not Found';
$layout = 'layouts/main';

ob_start();
?>

<div class="flex flex-col items-center justify-center py-12">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-primary">404</h1>
        <h2 class="text-3xl font-semibold mt-4">Page Not Found</h2>
        <p class="text-gray-600 mt-2">The page you are looking for doesn't exist or has been moved.</p>
        
        <div class="mt-8">
            <a href="<?= BASE_URL ?>" class="btn btn-primary">Go to Homepage</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 