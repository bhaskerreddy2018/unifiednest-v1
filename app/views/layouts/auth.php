<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/daisyui@3.0.0/dist/full.css" rel="stylesheet" type="text/css" />
    
    <!-- Custom styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-base-200 flex flex-col">
    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-primary"><?= APP_NAME ?></h1>
                <?php if (isset($pageTitle)): ?>
                    <h2 class="mt-6 text-2xl font-bold text-gray-900"><?= $pageTitle ?></h2>
                <?php endif; ?>
            </div>
            
            <?php if (isset($flash) && $flash): ?>
                <div class="alert alert-<?= $flash['type'] ?> shadow-lg">
                    <span><?= $flash['message'] ?></span>
                </div>
            <?php endif; ?>
            
            <div class="bg-base-100 rounded-lg shadow-xl p-8">
                <?= $content ?? '' ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include VIEW_PATH . '/components/footer.php'; ?>

    <!-- Scripts -->
    <script>
        // Close alert messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.style.display = 'none';
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html> 