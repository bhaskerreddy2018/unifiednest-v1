<?php
/**
 * Edit project form
 */
$layout = 'layouts/main';
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Edit Project: <?= htmlspecialchars($project['name']) ?></h2>
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>/projects/viewProject/<?= $project['id'] ?>" class="btn btn-outline btn-sm">
            <i class="fas fa-eye mr-2"></i> View Project
        </a>
        <a href="<?= BASE_URL ?>/projects" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back to Projects
        </a>
    </div>
</div>

<div class="bg-base-100 rounded-box p-6 shadow-sm">
    <form action="<?= BASE_URL ?>/projects/update/<?= $project['id'] ?>" method="post">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div>
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Project Name *</span>
                    </label>
                    <input type="text" name="name" class="input input-bordered" value="<?= htmlspecialchars($project['name']) ?>" required>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Description</span>
                    </label>
                    <textarea name="description" class="textarea textarea-bordered" rows="5"><?= htmlspecialchars($project['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Client Name</span>
                    </label>
                    <input type="text" name="client_name" class="input input-bordered" value="<?= htmlspecialchars($project['client_name'] ?? '') ?>">
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Department *</span>
                    </label>
                    <select name="department_id" class="select select-bordered w-full" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $department): ?>
                            <option value="<?= $department['id'] ?>" <?= $project['department_id'] == $department['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($department['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Start Date</span>
                        </label>
                        <input type="date" name="start_date" class="input input-bordered" 
                               value="<?= !empty($project['start_date']) ? date('Y-m-d', strtotime($project['start_date'])) : '' ?>">
                    </div>
                    
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">End Date</span>
                        </label>
                        <input type="date" name="end_date" class="input input-bordered"
                               value="<?= !empty($project['end_date']) ? date('Y-m-d', strtotime($project['end_date'])) : '' ?>">
                    </div>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="planning" <?= $project['status'] === 'planning' ? 'selected' : '' ?>>Planning</option>
                        <option value="in_progress" <?= $project['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="on_hold" <?= $project['status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                        <option value="completed" <?= $project['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="cancelled" <?= $project['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Budget</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="budget" class="input input-bordered w-full" min="0" step="0.01" 
                               value="<?= $project['budget'] ?? '' ?>">
                    </div>
                </div>
                
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Team Members</span>
                    </label>
                    <select name="members[]" class="select select-bordered w-full" multiple size="5">
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= in_array($user['id'], $memberIds) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Hold Ctrl/Cmd to select multiple members</span>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end gap-2">
            <a href="<?= BASE_URL ?>/projects/viewProject/<?= $project['id'] ?>" class="btn btn-ghost">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i> Update Project
            </button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 