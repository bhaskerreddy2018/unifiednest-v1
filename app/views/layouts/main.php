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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .drawer-content {
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex-grow: 1; 
            padding: 1rem;
        }
        .drawer-side {
            position: fixed;
        }
        @media (min-width: 1024px) {
            .drawer-side {
                position: fixed;
                height: 100vh;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-base-200">
    <!-- Drawer component for sidebar -->
    <div class="drawer lg:drawer-open">
        <!-- Drawer toggle checkbox -->
        <input id="sidebar-drawer" type="checkbox" class="drawer-toggle"> 
        
        <!-- Page content -->
        <div class="drawer-content flex flex-col">
            <!-- Top Navigation Bar -->
            <?php include VIEW_PATH . '/components/navbar.php'; ?>
            
            <!-- Main Content Area -->
            <div class="main-content container mx-auto px-4 py-4">
                <?php if (isset($flash) && $flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?> mb-6">
                        <span><?= $flash['message'] ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($pageTitle) && !isset($hidePageTitle)): ?>
                    <h1 class="text-2xl font-bold mb-6"><?= $pageTitle ?></h1>
                <?php endif; ?>
                
                <?= $content ?? '' ?>
            </div>
            
            <!-- Footer -->
            <?php include VIEW_PATH . '/components/footer.php'; ?>
        </div>
        
        <!-- Sidebar -->
        <?php include VIEW_PATH . '/components/sidebar.php'; ?>
    </div>

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
            
            // Handle sidebar toggle on mobile
            const sidebarToggle = document.getElementById('sidebar-drawer');
            const drawerOverlay = document.querySelector('.drawer-overlay');
            
            // Close sidebar when clicking outside
            if (drawerOverlay) {
                drawerOverlay.addEventListener('click', function() {
                    sidebarToggle.checked = false;
                });
            }
            
            // Close sidebar when clicking menu items on mobile
            const sidebarLinks = document.querySelectorAll('.drawer-side a');
            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        sidebarToggle.checked = false;
                    }
                });
            });
        });
    </script>
</body>
</html> 