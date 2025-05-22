<?php
$pageTitle = 'Organizations';
$layout = 'layouts/main';

ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Organizations</h1>
    <a href="<?= BASE_URL ?>/organizations/create" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Add Organization
    </a>
</div>

<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <?php if (empty($organizations)): ?>
        <div class="p-6 text-center">
            <p class="text-gray-500">No organizations found.</p>
            <a href="<?= BASE_URL ?>/organizations/create" class="btn btn-primary btn-sm mt-4">Create your first organization</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th class="w-12">Logo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Departments</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($organizations as $index => $org): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?php if (!empty($org['logo'])): ?>
                                <div class="avatar">
                                    <div class="w-10 rounded">
                                        <img src="<?= BASE_URL ?>/storage/uploads/logos/<?= $org['logo'] ?>" alt="<?= $org['name'] ?>" />
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="avatar placeholder">
                                    <div class="bg-neutral-focus text-neutral-content rounded w-10">
                                        <span><?= strtoupper(substr($org['name'], 0, 1)) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= $org['name'] ?></td>
                        <td><?= $org['email'] ?? '-' ?></td>
                        <td><?= $org['phone'] ?? '-' ?></td>
                        <td>
                            <?php 
                            $departmentCount = $organizationModel->countDepartments($org['id']);
                            echo $departmentCount;
                            ?>
                            <a href="<?= BASE_URL ?>/departments?organization_id=<?= $org['id'] ?>" class="btn btn-xs btn-ghost">
                                View
                            </a>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end">
                                <a href="<?= BASE_URL ?>/organizations/edit/<?= $org['id'] ?>" class="btn btn-sm btn-ghost">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                                <a href="<?= BASE_URL ?>/organizations/delete/<?= $org['id'] ?>" class="btn btn-sm btn-ghost text-error" 
                                   onclick="return confirm('Are you sure you want to delete this organization and all its departments?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 