<?php
/**
 * Project details view
 */
$layout = 'layouts/main';
ob_start();
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold"><?= htmlspecialchars($project['name']) ?></h2>
    <div class="flex gap-2">
        <a href="<?= BASE_URL ?>/projects/edit/<?= $project['id'] ?>" class="btn btn-outline btn-sm">
            <i class="fas fa-edit mr-2"></i> Edit
        </a>
        <a href="<?= BASE_URL ?>/projects" class="btn btn-outline btn-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
    </div>
</div>

<div class="bg-base-100 rounded-box shadow-sm mb-6">
    <!-- Project Header -->
    <div class="p-6 border-b border-base-300">
        <div class="flex flex-wrap justify-between">
            <div class="mb-4 md:mb-0">
                <p class="text-lg"><?= htmlspecialchars($project['description'] ?? 'No description provided.') ?></p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <div class="badge badge-<?= getStatusBadgeClass($project['status']) ?>">
                        <?= formatStatus($project['status']) ?>
                    </div>
                    <div class="badge badge-outline">
                        <?= htmlspecialchars($department['name'] ?? 'No department') ?>
                    </div>
                    <?php if (!empty($project['client_name'])): ?>
                        <div class="badge badge-outline">
                            <i class="fas fa-user-tie mr-1"></i> <?= htmlspecialchars($project['client_name']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-title">Start Date</div>
                    <div class="stat-value text-sm">
                        <?= !empty($project['start_date']) ? date('M j, Y', strtotime($project['start_date'])) : 'Not set' ?>
                    </div>
                </div>
                <div class="stat">
                    <div class="stat-title">End Date</div>
                    <div class="stat-value text-sm">
                        <?= !empty($project['end_date']) ? date('M j, Y', strtotime($project['end_date'])) : 'Not set' ?>
                    </div>
                </div>
                <?php if (!empty($project['budget'])): ?>
                <div class="stat">
                    <div class="stat-title">Budget</div>
                    <div class="stat-value text-sm">
                        <?= formatCurrency($project['budget']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Tabs -->
    <div class="tabs tabs-boxed bg-base-200 rounded-none">
        <a class="tab tab-active" data-tab="overview">Overview</a>
        <a class="tab" data-tab="tasks">Tasks</a>
        <a class="tab" data-tab="members">Team Members</a>
        <a class="tab" data-tab="documents">Documents</a>
    </div>
    
    <!-- Tab Content -->
    <div class="p-6">
        <!-- Overview Tab -->
        <div id="overview-tab" class="tab-content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h3 class="card-title">Project Stats</h3>
                        <div class="stats stats-vertical shadow">
                            <div class="stat">
                                <div class="stat-title">Tasks</div>
                                <div class="stat-value"><?= count($tasks) ?></div>
                                <div class="stat-desc">
                                    <?= countTasksByStatus($tasks, 'completed') ?> completed / 
                                    <?= countTasksByStatus($tasks, 'in_progress') ?> in progress
                                </div>
                            </div>
                            <div class="stat">
                                <div class="stat-title">Team Members</div>
                                <div class="stat-value"><?= count($members) ?></div>
                            </div>
                            <div class="stat">
                                <div class="stat-title">Documents</div>
                                <div class="stat-value"><?= count($documents) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-base-200">
                    <div class="card-body">
                        <h3 class="card-title">Recent Activity</h3>
                        <div class="overflow-y-auto max-h-64">
                            <ul class="timeline timeline-vertical">
                                <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                                <li>
                                    <div class="timeline-start"><?= date('M j', strtotime($task['created_at'])) ?></div>
                                    <div class="timeline-middle">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="timeline-end timeline-box">
                                        <div class="font-bold"><?= htmlspecialchars($task['title']) ?></div>
                                        <div class="text-sm"><?= formatTaskStatus($task['status']) ?></div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                                <?php if (empty($tasks)): ?>
                                <li>
                                    <div class="timeline-middle">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="timeline-end timeline-box">No tasks yet</div>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tasks Tab -->
        <div id="tasks-tab" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Tasks</h3>
                <button class="btn btn-primary btn-sm" onclick="document.getElementById('add-task-modal').showModal()">
                    <i class="fas fa-plus mr-2"></i> Add Task
                </button>
            </div>
            
            <?php if (empty($tasks)): ?>
                <div class="alert">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>No tasks found. Add a task to get started.</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- To Do -->
                    <div class="bg-base-200 rounded-box p-4">
                        <h4 class="font-bold mb-2">To Do</h4>
                        <?php foreach (filterTasksByStatus($tasks, 'to_do') as $task): ?>
                            <?= renderTaskCard($task) ?>
                        <?php endforeach; ?>
                        <?php if (count(filterTasksByStatus($tasks, 'to_do')) === 0): ?>
                            <div class="text-center p-4 text-sm text-base-content/70">No tasks</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- In Progress -->
                    <div class="bg-base-200 rounded-box p-4">
                        <h4 class="font-bold mb-2">In Progress</h4>
                        <?php foreach (filterTasksByStatus($tasks, 'in_progress') as $task): ?>
                            <?= renderTaskCard($task) ?>
                        <?php endforeach; ?>
                        <?php if (count(filterTasksByStatus($tasks, 'in_progress')) === 0): ?>
                            <div class="text-center p-4 text-sm text-base-content/70">No tasks</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Completed -->
                    <div class="bg-base-200 rounded-box p-4">
                        <h4 class="font-bold mb-2">Completed</h4>
                        <?php foreach (filterTasksByStatus($tasks, 'completed') as $task): ?>
                            <?= renderTaskCard($task) ?>
                        <?php endforeach; ?>
                        <?php if (count(filterTasksByStatus($tasks, 'completed')) === 0): ?>
                            <div class="text-center p-4 text-sm text-base-content/70">No tasks</div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Members Tab -->
        <div id="members-tab" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Team Members</h3>
            </div>
            
            <?php if (empty($members)): ?>
                <div class="alert">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>No team members found.</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($members as $member): ?>
                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <div class="flex items-center gap-4">
                                    <div class="avatar">
                                        <div class="w-12 rounded-full">
                                            <?php if (!empty($member['profile_image'])): ?>
                                                <img src="<?= BASE_URL ?>/storage/<?= $member['profile_image'] ?>" alt="<?= htmlspecialchars($member['first_name']) ?>">
                                            <?php else: ?>
                                                <div class="bg-primary text-primary-content flex items-center justify-center">
                                                    <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-bold"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></h3>
                                        <p class="text-sm"><?= htmlspecialchars($member['email']) ?></p>
                                        <div class="badge badge-outline mt-1"><?= ucfirst($member['role']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Documents Tab -->
        <div id="documents-tab" class="tab-content hidden">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Documents</h3>
                <button class="btn btn-primary btn-sm" onclick="document.getElementById('add-document-modal').showModal()">
                    <i class="fas fa-upload mr-2"></i> Upload Document
                </button>
            </div>
            
            <?php if (empty($documents)): ?>
                <div class="alert">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>No documents found. Upload a document to get started.</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Size</th>
                                <th>Uploaded By</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($documents as $document): ?>
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-file-<?= getFileIcon($document['file_type']) ?> text-primary"></i>
                                            <?= htmlspecialchars($document['filename']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($document['description'] ?? 'No description') ?></td>
                                    <td><?= formatFileSize($document['file_size']) ?></td>
                                    <td><?= htmlspecialchars($document['first_name'] . ' ' . $document['last_name']) ?></td>
                                    <td><?= date('M j, Y', strtotime($document['uploaded_at'])) ?></td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="<?= BASE_URL ?>/storage/<?= $document['file_path'] ?>" target="_blank" class="btn btn-ghost btn-xs">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button class="btn btn-ghost btn-xs text-error" onclick="confirmDeleteDocument(<?= $document['id'] ?>, '<?= htmlspecialchars($document['filename']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Task Modal -->
<dialog id="add-task-modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Add New Task</h3>
        <form action="<?= BASE_URL ?>/projects/addTask/<?= $project['id'] ?>" method="post" class="mt-4">
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Title</span>
                </label>
                <input type="text" name="title" class="input input-bordered" required>
            </div>
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" class="textarea textarea-bordered" rows="3"></textarea>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Status</span>
                    </label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="to_do">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Priority</span>
                    </label>
                    <select name="priority" class="select select-bordered w-full">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Due Date</span>
                    </label>
                    <input type="date" name="due_date" class="input input-bordered">
                </div>
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Assigned To</span>
                    </label>
                    <select name="assigned_to" class="select select-bordered w-full">
                        <option value="">Unassigned</option>
                        <?php foreach ($members as $member): ?>
                            <option value="<?= $member['user_id'] ?>">
                                <?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="modal-action">
                <button type="button" class="btn" onclick="document.getElementById('add-task-modal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Task</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Add Document Modal -->
<dialog id="add-document-modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Upload Document</h3>
        <form action="<?= BASE_URL ?>/projects/uploadDocument/<?= $project['id'] ?>" method="post" enctype="multipart/form-data" class="mt-4">
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">File</span>
                </label>
                <input type="file" name="document" class="file-input file-input-bordered w-full" required>
            </div>
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Description</span>
                </label>
                <textarea name="description" class="textarea textarea-bordered" rows="3"></textarea>
            </div>
            
            <div class="modal-action">
                <button type="button" class="btn" onclick="document.getElementById('add-document-modal').close()">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</dialog>

<!-- Delete Document Modal -->
<dialog id="delete-document-modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">Confirm Delete</h3>
        <p class="py-4">Are you sure you want to delete the document "<span id="document-name"></span>"? This action cannot be undone.</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn">Cancel</button>
            </form>
            <form id="delete-document-form" action="" method="post">
                <button type="submit" class="btn btn-error">Delete</button>
            </form>
        </div>
    </div>
</dialog>

<script>
    // Tab switching
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('tab-active'));
            // Add active class to clicked tab
            this.classList.add('tab-active');
            
            // Hide all tab content
            document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
            // Show selected tab content
            document.getElementById(this.getAttribute('data-tab') + '-tab').classList.remove('hidden');
        });
    });
    
    // Delete document confirmation
    function confirmDeleteDocument(documentId, documentName) {
        const modal = document.getElementById('delete-document-modal');
        document.getElementById('document-name').textContent = documentName;
        document.getElementById('delete-document-form').action = `<?= BASE_URL ?>/projects/deleteDocument/${documentId}`;
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

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function countTasksByStatus($tasks, $status) {
    return count(array_filter($tasks, function($task) use ($status) {
        return $task['status'] === $status;
    }));
}

function filterTasksByStatus($tasks, $status) {
    return array_filter($tasks, function($task) use ($status) {
        return $task['status'] === $status;
    });
}

function formatTaskStatus($status) {
    switch ($status) {
        case 'to_do':
            return 'To Do';
        case 'in_progress':
            return 'In Progress';
        case 'completed':
            return 'Completed';
        default:
            return ucfirst($status);
    }
}

function renderTaskCard($task) {
    $priorityClass = '';
    switch ($task['priority']) {
        case 'high':
            $priorityClass = 'border-l-4 border-error';
            break;
        case 'medium':
            $priorityClass = 'border-l-4 border-warning';
            break;
        case 'low':
            $priorityClass = 'border-l-4 border-info';
            break;
    }
    
    $assignedTo = '';
    if (!empty($task['first_name'])) {
        $assignedTo = $task['first_name'] . ' ' . $task['last_name'];
    }
    
    $output = '<div class="card bg-base-100 shadow-sm mb-2 ' . $priorityClass . '">';
    $output .= '<div class="card-body p-4">';
    $output .= '<h4 class="card-title text-base">' . htmlspecialchars($task['title']) . '</h4>';
    
    if (!empty($task['description'])) {
        $output .= '<p class="text-sm">' . htmlspecialchars(substr($task['description'], 0, 100)) . 
                  (strlen($task['description']) > 100 ? '...' : '') . '</p>';
    }
    
    $output .= '<div class="card-actions justify-between items-center mt-2">';
    
    if (!empty($assignedTo)) {
        $output .= '<div class="badge badge-outline">' . htmlspecialchars($assignedTo) . '</div>';
    } else {
        $output .= '<div class="badge badge-outline">Unassigned</div>';
    }
    
    if (!empty($task['due_date'])) {
        $output .= '<div class="text-xs">' . date('M j, Y', strtotime($task['due_date'])) . '</div>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}

function getFileIcon($fileType) {
    if (strpos($fileType, 'image/') !== false) {
        return 'image';
    } elseif (strpos($fileType, 'pdf') !== false) {
        return 'pdf';
    } elseif (strpos($fileType, 'word') !== false || strpos($fileType, 'document') !== false) {
        return 'word';
    } elseif (strpos($fileType, 'excel') !== false || strpos($fileType, 'spreadsheet') !== false) {
        return 'excel';
    } elseif (strpos($fileType, 'powerpoint') !== false || strpos($fileType, 'presentation') !== false) {
        return 'powerpoint';
    } elseif (strpos($fileType, 'zip') !== false || strpos($fileType, 'archive') !== false) {
        return 'archive';
    } elseif (strpos($fileType, 'text') !== false) {
        return 'text';
    } else {
        return 'alt';
    }
}

function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }
    return round($size, 2) . ' ' . $units[$i];
}
?>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 