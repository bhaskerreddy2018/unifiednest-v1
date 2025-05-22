<footer class="bg-base-100 border-t">
    <div class="container mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <p class="text-sm text-gray-500">
                    &copy; <?= date('Y') ?> <?= APP_NAME ?> v<?= APP_VERSION ?>
                </p>
            </div>
            <div class="flex space-x-4">
                <a href="<?= BASE_URL ?>/help" class="text-sm text-gray-500 hover:text-primary">Help</a>
                <a href="<?= BASE_URL ?>/privacy" class="text-sm text-gray-500 hover:text-primary">Privacy Policy</a>
                <a href="<?= BASE_URL ?>/terms" class="text-sm text-gray-500 hover:text-primary">Terms</a>
            </div>
        </div>
    </div>
</footer> 