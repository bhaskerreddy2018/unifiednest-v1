<nav class="bg-base-100 shadow-md">
    <div class="container mx-auto px-4">
        <div class="navbar">
            <!-- Mobile menu button -->
            <div class="navbar-start">
                <label for="sidebar-drawer" class="btn btn-ghost drawer-button lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                </label>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-ghost normal-case text-xl hidden lg:flex">
                    <?= APP_NAME ?>
                </a>
            </div>

            <!-- Center logo for mobile only -->
            <div class="navbar-center lg:hidden">
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-ghost normal-case text-xl">
                    <?= APP_NAME ?>
                </a>
            </div>

            <!-- User menu and notifications -->
            <div class="navbar-end">
                <!-- Notifications -->
                <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-circle">
                        <div class="indicator">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span class="badge badge-sm indicator-item badge-primary">3</span>
                        </div>
                    </label>
                    <div tabindex="0" class="mt-3 z-[1] card card-compact dropdown-content w-80 bg-base-100 shadow">
                        <div class="card-body">
                            <h3 class="font-bold text-lg">Notifications</h3>
                            <div class="divider my-0"></div>
                            <ul class="menu bg-base-100">
                                <li>
                                    <a class="flex justify-between">
                                        <span>Leave request approved</span>
                                        <span class="text-xs opacity-50">2m ago</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="flex justify-between">
                                        <span>New task assigned</span>
                                        <span class="text-xs opacity-50">1h ago</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="flex justify-between">
                                        <span>Project deadline updated</span>
                                        <span class="text-xs opacity-50">5h ago</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="card-actions">
                                <a href="<?= BASE_URL ?>/notifications" class="btn btn-primary btn-sm btn-block">View All</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown dropdown-end ml-2">
                    <label tabindex="0" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <?php if (isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                                <img src="<?= BASE_URL ?>/storage/uploads/profile/<?= $user['profile_image'] ?>" alt="Profile" />
                            <?php else: ?>
                                <div class="bg-primary text-white flex items-center justify-center h-full">
                                    <?= isset($user) ? strtoupper(substr($user['first_name'], 0, 1)) : 'U' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </label>
                    <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li class="menu-title p-2">
                            <span><?= isset($user) ? $user['first_name'] . ' ' . $user['last_name'] : 'User' ?></span>
                        </li>
                        <div class="divider my-0"></div>
                        <li><a href="<?= BASE_URL ?>/profile">Profile</a></li>
                        <li><a href="<?= BASE_URL ?>/settings">Settings</a></li>
                        <div class="divider my-0"></div>
                        <li><a href="<?= BASE_URL ?>/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav> 