<?php
$pageTitle = 'Dashboard';
$layout = 'layouts/main';

ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="stats shadow">
        <div class="stat">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </div>
            <div class="stat-title">Organization</div>
            <div class="stat-value text-primary"><?= $organization['name'] ?></div>
            <div class="stat-desc">Your company</div>
        </div>
    </div>

    <div class="stats shadow">
        <div class="stat">
            <div class="stat-figure text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <div class="stat-title">Team Members</div>
            <div class="stat-value text-secondary"><?= $stats['total_users'] ?></div>
            <div class="stat-desc">Team size</div>
        </div>
    </div>

    <div class="stats shadow">
        <div class="stat">
            <div class="stat-figure text-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
            <div class="stat-title">Departments</div>
            <div class="stat-value text-accent"><?= $stats['total_departments'] ?></div>
            <div class="stat-desc">Company structure</div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Upcoming Tasks -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                Ongoing Tasks
            </h2>
            <div class="divider mt-0"></div>
            
            <!-- Task List -->
            <div class="flex flex-col gap-2">
                <?php if (empty($ongoingTasks)): ?>
                    <div class="alert bg-base-200">
                        <span>No tasks assigned to you yet.</span>
                    </div>
                <?php else: ?>
                    <?php foreach ($ongoingTasks as $task): ?>
                        <div class="alert bg-base-200">
                            <div class="flex-1">
                                <?php
                                $icon = '';
                                if ($task['status'] == 'in_progress') {
                                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-info">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>';
                                } elseif ($task['priority'] == 'high') {
                                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-warning">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>';
                                } else {
                                    $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-6 h-6 mx-2 stroke-current">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            </svg>';
                                }
                                ?>
                                <?= $icon ?>
                                <label><?= $task['title'] ?></label>
                            </div>
                            <div class="flex-none">
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                switch ($task['status']) {
                                    case 'in_progress':
                                        $statusClass = 'badge-info';
                                        $statusText = 'In progress';
                                        break;
                                    case 'to_do':
                                        $statusClass = '';
                                        $statusText = 'To do';
                                        break;
                                    case 'completed':
                                        $statusClass = 'badge-success';
                                        $statusText = 'Completed';
                                        break;
                                    default:
                                        $statusClass = 'badge-secondary';
                                        $statusText = $task['status'];
                                }
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                <?php if (!empty($task['due_date'])): ?>
                                    <?php
                                    $dueDate = strtotime($task['due_date']);
                                    $today = strtotime('today');
                                    $tomorrow = strtotime('tomorrow');
                                    $dueText = '';
                                    $dueClass = '';
                                    
                                    if ($dueDate < $today) {
                                        $dueText = 'Overdue';
                                        $dueClass = 'badge-error';
                                    } elseif ($dueDate == $today) {
                                        $dueText = 'Due today';
                                        $dueClass = 'badge-warning';
                                    } elseif ($dueDate == $tomorrow) {
                                        $dueText = 'Due tomorrow';
                                        $dueClass = 'badge-warning';
                                    } else {
                                        $dueText = 'Due ' . date('M j', $dueDate);
                                        $dueClass = 'badge-ghost';
                                    }
                                    ?>
                                    <span class="badge <?= $dueClass ?> ml-2"><?= $dueText ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Notifications -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                Notifications
            </h2>
            <div class="divider mt-0"></div>
            
            <!-- Notification List -->
            <div class="flex flex-col gap-2">
                <?php if (empty($notifications)): ?>
                    <div class="alert bg-base-200">
                        <span>No notifications at this time.</span>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="alert bg-base-200">
                            <div>
                                <?php
                                $icon = '';
                                switch ($notification['type']) {
                                    case 'info':
                                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info flex-shrink-0 w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>';
                                        break;
                                    case 'warning':
                                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-warning flex-shrink-0 w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                </svg>';
                                        break;
                                    case 'error':
                                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-error flex-shrink-0 w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>';
                                        break;
                                    case 'success':
                                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-success flex-shrink-0 w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>';
                                        break;
                                    default:
                                        $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-primary flex-shrink-0 w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>';
                                }
                                ?>
                                <?= $icon ?>
                                <div class="flex justify-between w-full">
                                    <span><?= $notification['title'] ?></span>
                                    <span class="text-xs opacity-50">
                                        <?php
                                        $time = $notification['time'];
                                        $diff = time() - $time;
                                        
                                        if ($diff < 60) {
                                            echo 'just now';
                                        } elseif ($diff < 3600) {
                                            echo floor($diff / 60) . 'm ago';
                                        } elseif ($diff < 86400) {
                                            echo floor($diff / 3600) . 'h ago';
                                        } else {
                                            echo date('M j', $time);
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Monthly Calendar -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Calendar
            </h2>
            <div class="divider mt-0"></div>
            
            <!-- Basic Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 text-center">
                <div class="font-bold text-sm">Sun</div>
                <div class="font-bold text-sm">Mon</div>
                <div class="font-bold text-sm">Tue</div>
                <div class="font-bold text-sm">Wed</div>
                <div class="font-bold text-sm">Thu</div>
                <div class="font-bold text-sm">Fri</div>
                <div class="font-bold text-sm">Sat</div>
                
                <?php
                    // Get current month's days
                    $currentMonth = date('m');
                    $currentYear = date('Y');
                    $firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
                    $numDays = date('t', $firstDay);
                    $firstDayOfWeek = date('w', $firstDay);
                    
                    // Add empty cells for days before the 1st of the month
                    for ($i = 0; $i < $firstDayOfWeek; $i++) {
                        echo '<div class="p-1 text-xs text-gray-400"></div>';
                    }
                    
                    // Add calendar days
                    for ($day = 1; $day <= $numDays; $day++) {
                        $date = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $day, $currentYear));
                        $isToday = date('Y-m-d') === $date ? 'bg-primary text-primary-content rounded-full' : '';
                        
                        echo '<div class="p-1">';
                        echo '<div class="' . $isToday . ' h-6 w-6 flex items-center justify-center mx-auto text-xs">' . $day . '</div>';
                        
                        // Example event indicators - would come from database in real app
                        if ($day == 15) {
                            echo '<div class="w-2 h-2 bg-info rounded-full mx-auto mt-1"></div>';
                        } elseif ($day == 22) {
                            echo '<div class="w-2 h-2 bg-error rounded-full mx-auto mt-1"></div>';
                        } elseif ($day == 10) {
                            echo '<div class="w-2 h-2 bg-warning rounded-full mx-auto mt-1"></div>';
                        }
                        
                        echo '</div>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 