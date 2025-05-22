<?php
$pageTitle = 'Server Error';
$layout = 'layouts/main';

ob_start();
?>

<div class="flex flex-col items-center justify-center py-12">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-error">500</h1>
        <h2 class="text-3xl font-semibold mt-4">Server Error</h2>
        <p class="text-gray-600 mt-2">Something went wrong on our end. Please try again later.</p>
        
        <div class="mt-8">
            <a href="<?= BASE_URL ?>" class="btn btn-primary">Go to Homepage</a>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 