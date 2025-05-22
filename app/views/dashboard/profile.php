<?php
$pageTitle = 'My Profile';
$layout = 'layouts/main';

ob_start();
?>

<div class="bg-white rounded-lg shadow-md">
    <!-- Profile Header -->
    <div class="bg-primary text-primary-content p-6 rounded-t-lg flex flex-col md:flex-row items-center gap-6">
        <div class="avatar">
            <div class="w-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?= BASE_URL ?>/storage/uploads/profile/<?= $user['profile_image'] ?>" alt="Profile" />
                <?php else: ?>
                    <div class="bg-primary-focus text-white flex items-center justify-center h-full text-3xl font-bold">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="text-center md:text-left">
            <h2 class="text-2xl font-bold"><?= $user['first_name'] . ' ' . $user['last_name'] ?></h2>
            <p class="opacity-80"><?= $role['name'] ?> - <?= $department['name'] ?> Department</p>
            <p class="opacity-80"><?= $organization['name'] ?></p>
        </div>
    </div>

    <!-- Profile Tabs -->
    <div class="px-6 py-4">
        <div class="tabs tabs-boxed">
            <a class="tab tab-active" data-tab="basic">Basic Info</a>
            <a class="tab" data-tab="family">Family</a>
            <a class="tab" data-tab="education">Education</a>
            <a class="tab" data-tab="experience">Work Experience</a>
            <a class="tab" data-tab="addresses">Addresses</a>
            <a class="tab" data-tab="health">Health</a>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="p-6">
        <!-- Basic Info Tab -->
        <div id="tab-basic" class="tab-content">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
            <form action="<?= BASE_URL ?>/dashboard/update-profile" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label for="first_name" class="label">
                            <span class="label-text">First Name</span>
                        </label>
                        <input type="text" id="first_name" name="first_name" value="<?= $user['first_name'] ?>" required class="input input-bordered">
                    </div>
                    
                    <div class="form-control">
                        <label for="last_name" class="label">
                            <span class="label-text">Last Name</span>
                        </label>
                        <input type="text" id="last_name" name="last_name" value="<?= $user['last_name'] ?>" required class="input input-bordered">
                    </div>
                    
                    <div class="form-control">
                        <label for="email" class="label">
                            <span class="label-text">Email</span>
                        </label>
                        <input type="email" id="email" name="email" value="<?= $user['email'] ?>" required class="input input-bordered">
                    </div>
                    
                    <div class="form-control">
                        <label for="phone" class="label">
                            <span class="label-text">Phone</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="<?= $user['phone'] ?>" class="input input-bordered">
                    </div>

                    <div class="form-control">
                        <label for="date_of_birth" class="label">
                            <span class="label-text">Date of Birth</span>
                        </label>
                        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= isset($profile['date_of_birth']) ? $profile['date_of_birth'] : '' ?>" class="input input-bordered">
                    </div>

                    <div class="form-control">
                        <label for="gender" class="label">
                            <span class="label-text">Gender</span>
                        </label>
                        <select id="gender" name="gender" class="select select-bordered w-full">
                            <option value="" <?= !isset($profile['gender']) ? 'selected' : '' ?>>Select Gender</option>
                            <option value="male" <?= (isset($profile['gender']) && $profile['gender'] == 'male') ? 'selected' : '' ?>>Male</option>
                            <option value="female" <?= (isset($profile['gender']) && $profile['gender'] == 'female') ? 'selected' : '' ?>>Female</option>
                            <option value="other" <?= (isset($profile['gender']) && $profile['gender'] == 'other') ? 'selected' : '' ?>>Other</option>
                            <option value="prefer_not_to_say" <?= (isset($profile['gender']) && $profile['gender'] == 'prefer_not_to_say') ? 'selected' : '' ?>>Prefer not to say</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label for="blood_group" class="label">
                            <span class="label-text">Blood Group</span>
                        </label>
                        <select id="blood_group" name="blood_group" class="select select-bordered w-full">
                            <option value="" <?= !isset($profile['blood_group']) ? 'selected' : '' ?>>Select Blood Group</option>
                            <option value="A+" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'A+') ? 'selected' : '' ?>>A+</option>
                            <option value="A-" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'A-') ? 'selected' : '' ?>>A-</option>
                            <option value="B+" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'B+') ? 'selected' : '' ?>>B+</option>
                            <option value="B-" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'B-') ? 'selected' : '' ?>>B-</option>
                            <option value="AB+" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'AB+') ? 'selected' : '' ?>>AB+</option>
                            <option value="AB-" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'AB-') ? 'selected' : '' ?>>AB-</option>
                            <option value="O+" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'O+') ? 'selected' : '' ?>>O+</option>
                            <option value="O-" <?= (isset($profile['blood_group']) && $profile['blood_group'] == 'O-') ? 'selected' : '' ?>>O-</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label for="marital_status" class="label">
                            <span class="label-text">Marital Status</span>
                        </label>
                        <select id="marital_status" name="marital_status" class="select select-bordered w-full">
                            <option value="" <?= !isset($profile['marital_status']) ? 'selected' : '' ?>>Select Marital Status</option>
                            <option value="single" <?= (isset($profile['marital_status']) && $profile['marital_status'] == 'single') ? 'selected' : '' ?>>Single</option>
                            <option value="married" <?= (isset($profile['marital_status']) && $profile['marital_status'] == 'married') ? 'selected' : '' ?>>Married</option>
                            <option value="divorced" <?= (isset($profile['marital_status']) && $profile['marital_status'] == 'divorced') ? 'selected' : '' ?>>Divorced</option>
                            <option value="widowed" <?= (isset($profile['marital_status']) && $profile['marital_status'] == 'widowed') ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </div>
                </div>

                <div class="divider">Change Password (Optional)</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-control">
                        <label for="password" class="label">
                            <span class="label-text">New Password</span>
                        </label>
                        <input type="password" id="password" name="password" class="input input-bordered">
                        <label class="label">
                            <span class="label-text-alt">Leave blank to keep current password</span>
                        </label>
                    </div>
                    
                    <div class="form-control">
                        <label for="confirm_password" class="label">
                            <span class="label-text">Confirm Password</span>
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" class="input input-bordered">
                    </div>
                </div>
                
                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>

        <!-- Family Tab -->
        <div id="tab-family" class="tab-content hidden">
            <h3 class="text-lg font-semibold mb-4">Family Members</h3>
            <p class="mb-4">You can add your family members' details here.</p>
            
            <!-- Placeholder for family members list -->
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p>No family members added yet.</p>
                <button class="btn btn-primary btn-sm mt-2">Add Family Member</button>
            </div>
        </div>

        <!-- Education Tab -->
        <div id="tab-education" class="tab-content hidden">
            <h3 class="text-lg font-semibold mb-4">Education History</h3>
            <p class="mb-4">You can add your educational qualifications here.</p>
            
            <!-- Placeholder for education list -->
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p>No education records added yet.</p>
                <button class="btn btn-primary btn-sm mt-2">Add Education</button>
            </div>
        </div>

        <!-- Experience Tab -->
        <div id="tab-experience" class="tab-content hidden">
            <h3 class="text-lg font-semibold mb-4">Work Experience</h3>
            <p class="mb-4">You can add your previous work experiences here.</p>
            
            <!-- Placeholder for experience list -->
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p>No work experience added yet.</p>
                <button class="btn btn-primary btn-sm mt-2">Add Experience</button>
            </div>
        </div>

        <!-- Addresses Tab -->
        <div id="tab-addresses" class="tab-content hidden">
            <h3 class="text-lg font-semibold mb-4">Addresses</h3>
            <p class="mb-4">You can add your addresses here.</p>
            
            <!-- Placeholder for addresses list -->
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p>No addresses added yet.</p>
                <button class="btn btn-primary btn-sm mt-2">Add Address</button>
            </div>
        </div>

        <!-- Health Tab -->
        <div id="tab-health" class="tab-content hidden">
            <h3 class="text-lg font-semibold mb-4">Health Information</h3>
            <p class="mb-4">You can add your health information here.</p>
            
            <!-- Placeholder for health information -->
            <div class="bg-base-200 p-4 rounded-lg text-center">
                <p>No health information added yet.</p>
                <button class="btn btn-primary btn-sm mt-2">Add Health Information</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for tabs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('tab-active'));
                
                // Add active class to current tab
                this.classList.add('tab-active');
                
                // Hide all tab contents
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Show the selected tab content
                document.getElementById('tab-' + tabId).classList.remove('hidden');
            });
        });
    });
</script>

<?php
$content = ob_get_clean();
include VIEW_PATH . '/' . $layout . '.php';
?> 