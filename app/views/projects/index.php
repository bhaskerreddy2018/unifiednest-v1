<?php
/**
 * Project listing page
 */
$layout = 'layouts/main';
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Projects</h2>
    <a href="<?= BASE_URL ?>/projects/create" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> New Project
    </a>
</div>

<!-- Filters -->
<div class="bg-base-100 rounded-box p-4 mb-6 shadow-sm">
    <form action="<?= BASE_URL ?>/projects" method="get" class="flex flex-wrap gap-3">
        <!-- Status filter -->
        <div class="form-control">
            <select name="status" class="select select-bordered w-full max-w-xs">
                <option value="">All Statuses</option>
                <option value="planning" <?= $currentStatus === 'planning' ? 'selected' : '' ?>>Planning</option>
                <option value="in_progress" <?= $currentStatus === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="on_hold" <?= $currentStatus === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                <option value="completed" <?= $currentStatus === 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="cancelled" <?= $currentStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        
        <!-- Department filter -->
        <div class="form-control">
            <select name="department_id" class="select select-bordered w-full max-w-xs">
                <option value="">All Departments</option>
                <?php foreach ($departments as $department): ?>
                    <option value="<?= $department['id'] ?>" <?= $currentDepartment == $department['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($department['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Search -->
        <div class="form-control flex-grow">
            <div class="input-group">
                <input type="text" name="search" placeholder="Search projects..." class="input input-bordered w-full" 
                       value="<?= htmlspecialchars($search ?? '') ?>">
                <button class="btn btn-square" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Projects List -->
<?php if (empty($projects)): ?>
    <div class="alert">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>No projects found. Create a new project to get started.</span>
        </div>
    </div>
<?php else: ?>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Client</th>
                    <th>Status</th>
                    <th>Timeline</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $project): ?>
                    <tr>
                        <td>
                            <div class="font-bold"><?= htmlspecialchars($project['name']) ?></div>
                        </td>
                        <td><?= htmlspecialchars($project['department_name']) ?></td>
                        <td><?= htmlspecialchars($project['client_name']) ?></td>
                        <td>
                            <div class="badge badge-<?= getStatusBadgeClass($project['status']) ?>">
                                <?= formatStatus($project['status']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($project['start_date']) && !empty($project['end_date'])): ?>
                                <div class="text-sm">
                                    <?= date('M j, Y', strtotime($project['start_date'])) ?> - 
                                    <?= date('M j, Y', strtotime($project['end_date'])) ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-400">Not set</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <div class="dropdown dropdown-end">
                                <label tabindex="0" class="btn btn-ghost btn-xs">
                                    <i class="fas fa-ellipsis-v"></i>
                                </label>
                                <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                    <li>
                                        <a href="<?= BASE_URL ?>/projects/viewProject/<?= $project['id'] ?>">
                                            <i class="fas fa-eye mr-2"></i> View
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?= BASE_URL ?>/projects/edit/<?= $project['id'] ?>">
                                            <i class="fas fa-edit mr-2"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="text-error" onclick="confirmDelete(<?= $project['id'] ?>, '<?= htmlspecialchars($project['name']) ?>')">
                                            <i class="fas fa-trash mr-2"></i> Delete
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($pagination['totalPages'] > 1): ?>
        <div class="flex justify-center mt-6">
            <div class="btn-group">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <a href="<?= BASE_URL ?>/projects?page=<?= $i ?>&status=<?= $currentStatus ?>&department_id=<?= $currentDepartment ?>&search=<?= urlencode($search ?? '') ?>" 
                       class="btn <?= $pagination['page'] == $i ? 'btn-active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<dialog id="delete-modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Confirm Delete</h3>
        <p class="py-4">Are you sure you want to delete the project "<span id="project-name"></span>"? This action cannot be undone.</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Cancel</button>
            </form>
            <form id="delete-form" action="" method="post">
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
        </div>
    </div>
</dialog>

<script>
    function confirmDelete(projectId, projectName) {
        const modal = document.getElementById('delete-modal');
        document.getElementById('project-name').textContent = projectName;
        document.getElementById('delete-form').action = `<?= BASE_URL ?>/projects/delete/${projectId}`;
        modal.showModal();
    }
</script>

<?php
/**
 * Helper functions for the view
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'planning':
            return 'info';
        case 'in_progress':
            return 'primary';
        case 'on_hold':
            return 'warning';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'error';
        default:
            return 'ghost';
    }
}

function formatStatus($status) {
    switch ($status) {
        case 'planning':
            return 'Planning';
        case 'in_progress':
            return 'In Progress';
        case 'on_hold':
            return 'On Hold';
        case 'completed':
            return 'Completed';
        case 'cancelled':
            return 'Cancelled';
        default:
            return ucfirst($status);
    }
}

$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 