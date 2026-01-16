

<?php $__env->startSection('title', 'System Settings'); ?>
<?php $__env->startSection('page-title', 'System Settings'); ?>
<?php $__env->startSection('page-description', 'Configure all system settings and preferences'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/global-optimized.css')); ?>">
<style>
    .settings-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .settings-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .settings-content {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        transition: all 0.2s ease-in-out;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        background-color: white;
    }
    
    .form-checkbox {
        width: 1rem;
        height: 1rem;
        margin-right: 0.5rem;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .btn-test {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    
    .btn-reset {
        background: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .grid-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }
    
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
    
    .status-healthy { background-color: #10b981; }
    .status-warning { background-color: #f59e0b; }
    .status-error { background-color: #ef4444; }
    
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification.success { background-color: #10b981; }
    .notification.error { background-color: #ef4444; }
    .notification.info { background-color: #3b82f6; }
    
    /* Confidence Threshold Slider Styles */
    .slider {
        -webkit-appearance: none;
        appearance: none;
        height: 8px;
        border-radius: 4px;
        outline: none;
        opacity: 0.7;
        transition: opacity .2s;
    }

    .slider:hover {
        opacity: 1;
    }

    .slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #3b82f6;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #3b82f6;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- System Health Status -->
    <div class="settings-section mb-6">
        <div class="settings-header">
            <h2 class="text-xl font-bold">System Health Status</h2>
        </div>
        <div class="settings-content">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Database</span>
                    <div class="text-xs text-gray-500" id="db-status">Healthy</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Storage</span>
                    <div class="text-xs text-gray-500" id="storage-status">85% Free</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-warning"></div>
                    <span class="text-sm font-medium">API Services</span>
                    <div class="text-xs text-gray-500" id="api-status">Configure API</div>
                </div>
                <div class="text-center">
                    <div class="status-indicator status-healthy"></div>
                    <span class="text-sm font-medium">Performance</span>
                    <div class="text-xs text-gray-500" id="perf-status">Normal</div>
                </div>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">General Settings</h2>
        </div>
        <div class="settings-content">
            <form id="general-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Institution Name</label>
                        <input type="text" name="institution_name" class="form-input" value="<?php echo e($settings['general']['institution_name']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Institution Email</label>
                        <input type="email" name="institution_email" class="form-input" value="<?php echo e($settings['general']['institution_email']); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Institution Address</label>
                    <textarea name="institution_address" class="form-input" rows="3"><?php echo e($settings['general']['institution_address']); ?></textarea>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="text" name="institution_phone" class="form-input" value="<?php echo e($settings['general']['institution_phone']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="Africa/Lagos" <?php echo e($settings['general']['timezone'] == 'Africa/Lagos' ? 'selected' : ''); ?>>Africa/Lagos</option>
                            <option value="UTC" <?php echo e($settings['general']['timezone'] == 'UTC' ? 'selected' : ''); ?>>UTC</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Format</label>
                        <select name="date_format" class="form-select">
                            <option value="Y-m-d" <?php echo e($settings['general']['date_format'] == 'Y-m-d' ? 'selected' : ''); ?>>YYYY-MM-DD</option>
                            <option value="d-m-Y" <?php echo e($settings['general']['date_format'] == 'd-m-Y' ? 'selected' : ''); ?>>DD-MM-YYYY</option>
                            <option value="m/d/Y" <?php echo e($settings['general']['date_format'] == 'm/d/Y' ? 'selected' : ''); ?>>MM/DD/YYYY</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('general')">Reset</button>
                    <button type="submit" class="btn-save">Save General Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Academic Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Academic Settings</h2>
        </div>
        <div class="settings-content">
            <form id="academic-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Current Academic Year</label>
                        <input type="text" name="current_academic_year" class="form-input" value="<?php echo e($settings['academic']['current_academic_year']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Current Semester</label>
                        <select name="current_semester" class="form-select">
                            <option value="First Semester" <?php echo e($settings['academic']['current_semester'] == 'First Semester' ? 'selected' : ''); ?>>First Semester</option>
                            <option value="Second Semester" <?php echo e($settings['academic']['current_semester'] == 'Second Semester' ? 'selected' : ''); ?>>Second Semester</option>
                        </select>
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Class Duration (minutes)</label>
                        <input type="number" name="class_duration_minutes" class="form-input" value="<?php echo e($settings['academic']['class_duration_minutes']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Attendance Tolerance (minutes)</label>
                        <input type="number" name="attendance_tolerance_minutes" class="form-input" value="<?php echo e($settings['academic']['attendance_tolerance_minutes']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Absence Threshold</label>
                        <input type="number" name="max_absence_threshold" class="form-input" value="<?php echo e($settings['academic']['max_absence_threshold']); ?>">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('academic')">Reset</button>
                    <button type="submit" class="btn-save">Save Academic Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Biometric Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Biometric Settings</h2>
        </div>
        <div class="settings-content">
            <form id="biometric-settings-form">
                <div class="form-group">
                    <label class="form-label">Face Recognition Provider</label>
                    <select name="face_provider" class="form-select">
                        <option value="faceplusplus" <?php echo e($settings['biometric']['face_provider'] == 'faceplusplus' ? 'selected' : ''); ?>>Face++</option>
                    </select>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Face++ API Key</label>
                        <input type="text" name="faceplusplus_api_key" class="form-input" value="<?php echo e($settings['biometric']['faceplusplus_api_key']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Face++ API Secret</label>
                        <input type="password" name="faceplusplus_api_secret" class="form-input" value="<?php echo e($settings['biometric']['faceplusplus_api_secret']); ?>">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Confidence Threshold (%)</label>
                        <div class="relative">
                            <input type="number" name="face_confidence_threshold" id="confidence_threshold" class="form-input" value="<?php echo e($settings['biometric']['face_confidence_threshold']); ?>" min="0" max="100" step="1">
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-sm text-gray-500">
                                <span id="threshold_indicator"><?php echo e($settings['biometric']['face_confidence_threshold']); ?>%</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <input type="range" id="confidence_slider" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider" min="0" max="100" value="<?php echo e($settings['biometric']['face_confidence_threshold']); ?>" style="background: linear-gradient(to right, #ef4444 0%, #f59e0b 50%, #10b981 100%);">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0% (Very Strict)</span>
                                <span>50% (Balanced)</span>
                                <span>100% (Very Loose)</span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-600">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                                <span id="threshold_description">Current setting: <?php echo e($settings['biometric']['face_confidence_threshold'] >= 80 ? 'Very Strict - High security, may reject valid faces' : ($settings['biometric']['face_confidence_threshold'] >= 60 ? 'Balanced - Good security and usability' : 'Loose - Lower security, more permissive')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Image Quality Threshold (%)</label>
                        <input type="number" name="face_image_quality_threshold" class="form-input" value="<?php echo e($settings['biometric']['face_image_quality_threshold']); ?>" min="0" max="100">
                        <div class="mt-1 text-xs text-gray-600">Minimum image quality required for face detection</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Detection Sensitivity</label>
                        <select name="face_detection_sensitivity" class="form-select">
                            <option value="low" <?php echo e($settings['biometric']['face_detection_sensitivity'] == 'low' ? 'selected' : ''); ?>>Low - Conservative detection</option>
                            <option value="medium" <?php echo e($settings['biometric']['face_detection_sensitivity'] == 'medium' ? 'selected' : ''); ?>>Medium - Balanced detection</option>
                            <option value="high" <?php echo e($settings['biometric']['face_detection_sensitivity'] == 'high' ? 'selected' : ''); ?>>High - Aggressive detection</option>
                        </select>
                        <div class="mt-1 text-xs text-gray-600">How sensitive the face detection algorithm should be</div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-test" onclick="testConfidenceThreshold()">Test Threshold</button>
                    <button type="button" class="btn-test" onclick="testFaceAPI()">Test API</button>
                    <button type="button" class="btn-reset" onclick="resetSettings('biometric')">Reset</button>
                    <button type="submit" class="btn-save">Save Biometric Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Security Settings</h2>
        </div>
        <div class="settings-content">
            <form id="security-settings-form">
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Password Min Length</label>
                        <input type="number" name="password_min_length" class="form-input" value="<?php echo e($settings['security']['password_min_length']); ?>" min="6" max="20">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout_minutes" class="form-input" value="<?php echo e($settings['security']['session_timeout_minutes']); ?>" min="30" max="480">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Login Attempts</label>
                        <input type="number" name="max_login_attempts" class="form-input" value="<?php echo e($settings['security']['max_login_attempts']); ?>" min="3" max="10">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Lockout Duration (minutes)</label>
                        <input type="number" name="lockout_duration_minutes" class="form-input" value="<?php echo e($settings['security']['lockout_duration_minutes']); ?>" min="5" max="60">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Require 2FA</label>
                        <div class="flex items-center mt-2">
                            <input type="checkbox" name="require_2fa" class="form-checkbox" <?php echo e($settings['security']['require_2fa'] ? 'checked' : ''); ?>>
                            <span class="text-sm text-gray-600">Enable two-factor authentication</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('security')">Reset</button>
                    <button type="submit" class="btn-save">Save Security Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Notification Settings</h2>
        </div>
        <div class="settings-content">
            <form id="notification-settings-form">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">SMTP Host</label>
                        <input type="text" name="smtp_host" class="form-input" value="<?php echo e($settings['notifications']['smtp_host']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Port</label>
                        <input type="number" name="smtp_port" class="form-input" value="<?php echo e($settings['notifications']['smtp_port']); ?>">
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">SMTP Username</label>
                        <input type="text" name="smtp_username" class="form-input" value="<?php echo e($settings['notifications']['smtp_username']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP Password</label>
                        <input type="password" name="smtp_password" class="form-input" value="<?php echo e($settings['notifications']['smtp_password']); ?>">
                    </div>
                </div>
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">SMTP Encryption</label>
                        <select name="smtp_encryption" class="form-select">
                            <option value="tls" <?php echo e($settings['notifications']['smtp_encryption'] == 'tls' ? 'selected' : ''); ?>>TLS</option>
                            <option value="ssl" <?php echo e($settings['notifications']['smtp_encryption'] == 'ssl' ? 'selected' : ''); ?>>SSL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Email</label>
                        <input type="email" name="from_email" class="form-input" value="<?php echo e($settings['notifications']['from_email']); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Name</label>
                        <input type="text" name="from_name" class="form-input" value="<?php echo e($settings['notifications']['from_name']); ?>">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-test" onclick="testEmail()">Test Email</button>
                    <button type="button" class="btn-reset" onclick="resetSettings('notifications')">Reset</button>
                    <button type="submit" class="btn-save">Save Notification Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Performance Settings -->
    <div class="settings-section">
        <div class="settings-header">
            <h2 class="text-xl font-bold">Performance Settings</h2>
        </div>
        <div class="settings-content">
            <form id="performance-settings-form">
                <div class="grid-3">
                    <div class="form-group">
                        <label class="form-label">Cache Duration (minutes)</label>
                        <input type="number" name="cache_duration_minutes" class="form-input" value="<?php echo e($settings['performance']['cache_duration_minutes']); ?>" min="5" max="1440">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Upload Size (MB)</label>
                        <input type="number" name="max_upload_size_mb" class="form-input" value="<?php echo e($settings['performance']['max_upload_size_mb']); ?>" min="1" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Image Quality (%)</label>
                        <input type="number" name="image_compression_quality" class="form-input" value="<?php echo e($settings['performance']['image_compression_quality']); ?>" min="10" max="100">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" class="btn-reset" onclick="resetSettings('performance')">Reset</button>
                    <button type="submit" class="btn-save">Save Performance Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Global notification function
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    // Trigger animation
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // General settings
    document.getElementById('general-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('general', new FormData(this));
    });
    
    // Academic settings
    document.getElementById('academic-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('academic', new FormData(this));
    });
    
    // Biometric settings
    document.getElementById('biometric-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('biometric', new FormData(this));
    });
    
    // Security settings
    document.getElementById('security-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('security', new FormData(this));
    });
    
    // Notification settings
    document.getElementById('notification-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('notifications', new FormData(this));
    });
    
    // Performance settings
    document.getElementById('performance-settings-form').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSettings('performance', new FormData(this));
    });
});

// Save settings function
function saveSettings(category, formData) {
    const settings = {};
    
    // Convert FormData to object
    for (let [key, value] of formData.entries()) {
        if (formData.getAll(key).length > 1) {
            // Handle checkboxes
            if (!settings[key]) settings[key] = [];
            settings[key].push(value);
        } else {
            settings[key] = value;
        }
    }
    
    // Convert checkbox values to boolean
    Object.keys(settings).forEach(key => {
        if (settings[key] === 'on') {
            settings[key] = true;
        }
    });
    
    fetch('/superadmin/system-settings/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category: category,
            settings: settings
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error saving settings: ' + error.message, 'error');
    });
}

// Reset settings function
function resetSettings(category) {
    if (confirm(`Are you sure you want to reset ${category} settings to default values?`)) {
        fetch('/superadmin/system-settings/reset', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ category: category })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error resetting settings: ' + error.message, 'error');
        });
    }
}

// Test Face++ API
function testFaceAPI() {
    const form = document.getElementById('biometric-settings-form');
    const formData = new FormData(form);
    
    const apiKey = formData.get('faceplusplus_api_key');
    const apiSecret = formData.get('faceplusplus_api_secret');
    
    if (!apiKey || !apiSecret) {
        showNotification('Please enter both API Key and API Secret', 'error');
        return;
    }
    
    showNotification('Testing Face++ API connection...', 'info');
    
    fetch('/superadmin/system-settings/test-face-api', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            api_key: apiKey,
            api_secret: apiSecret
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing API: ' + error.message, 'error');
    });
}

// Test email configuration
function testEmail() {
    const form = document.getElementById('notification-settings-form');
    const formData = new FormData(form);
    
    const testEmail = prompt('Enter email address to send test email to:');
    if (!testEmail) return;
    
    const emailData = {
        smtp_host: formData.get('smtp_host'),
        smtp_port: formData.get('smtp_port'),
        smtp_username: formData.get('smtp_username'),
        smtp_password: formData.get('smtp_password'),
        smtp_encryption: formData.get('smtp_encryption'),
        from_email: formData.get('from_email'),
        test_email: testEmail
    };
    
    showNotification('Testing email configuration...', 'info');
    
    fetch('/superadmin/system-settings/test-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(emailData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing email: ' + error.message, 'error');
    });
}

// Confidence threshold management
document.addEventListener('DOMContentLoaded', function() {
    const confidenceInput = document.getElementById('confidence_threshold');
    const confidenceSlider = document.getElementById('confidence_slider');
    const thresholdIndicator = document.getElementById('threshold_indicator');
    const thresholdDescription = document.getElementById('threshold_description');
    
    // Update threshold display function
    function updateThresholdDisplay(value) {
        if (thresholdIndicator) thresholdIndicator.textContent = value + '%';
        
        let description = '';
        let color = 'green';
        
        if (value >= 80) {
            description = 'Very Strict - High security, may reject valid faces';
            color = 'red';
        } else if (value >= 60) {
            description = 'Balanced - Good security and usability';
            color = 'green';
        } else if (value >= 40) {
            description = 'Loose - Lower security, more permissive';
            color = 'yellow';
        } else {
            description = 'Very Loose - Very low security, high false acceptance risk';
            color = 'red';
        }
        
        if (thresholdDescription) {
            thresholdDescription.textContent = 'Current setting: ' + description;
            
            // Update color indicator
            const indicator = thresholdDescription.previousElementSibling;
            if (indicator) {
                indicator.className = `w-3 h-3 rounded-full bg-${color}-500`;
            }
        }
    }
    
    if (confidenceInput && confidenceSlider) {
        // Sync slider with input
        confidenceSlider.addEventListener('input', function() {
            confidenceInput.value = this.value;
            updateThresholdDisplay(this.value);
        });
        
        // Sync input with slider
        confidenceInput.addEventListener('input', function() {
            confidenceSlider.value = this.value;
            updateThresholdDisplay(this.value);
        });
    }
});

// Test confidence threshold
function testConfidenceThreshold() {
    const threshold = document.getElementById('confidence_threshold').value;
    
    if (!threshold || threshold < 0 || threshold > 100) {
        showNotification('Please enter a valid confidence threshold (0-100)', 'error');
        return;
    }
    
    showNotification('Testing confidence threshold...', 'info');
    
    fetch('/superadmin/system-settings/test-confidence-threshold', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            threshold: parseInt(threshold)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        showNotification('Error testing threshold: ' + error.message, 'error');
    });
}

// Preset threshold buttons
function setThresholdPreset(preset) {
    const confidenceInput = document.getElementById('confidence_threshold');
    const confidenceSlider = document.getElementById('confidence_slider');
    
    if (!confidenceInput || !confidenceSlider) return;
    
    let value;
    switch(preset) {
        case 'strict':
            value = 85;
            break;
        case 'balanced':
            value = 70;
            break;
        case 'loose':
            value = 55;
            break;
        default:
            return;
    }
    
    confidenceInput.value = value;
    confidenceSlider.value = value;
    
    // Call updateThresholdDisplay if it exists
    if (typeof updateThresholdDisplay === 'function') {
        updateThresholdDisplay(value);
    }
}

// Add preset buttons to the form
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const confidenceGroup = document.querySelector('input[name="face_confidence_threshold"]')?.closest('.form-group');
        if (confidenceGroup && !confidenceGroup.querySelector('.preset-buttons')) {
            const presetButtons = document.createElement('div');
            presetButtons.className = 'mt-3 flex gap-2 preset-buttons';
            presetButtons.innerHTML = `
                <button type="button" onclick="setThresholdPreset('strict')" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Strict (85%)</button>
                <button type="button" onclick="setThresholdPreset('balanced')" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">Balanced (70%)</button>
                <button type="button" onclick="setThresholdPreset('loose')" class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Loose (55%)</button>
            `;
            confidenceGroup.appendChild(presetButtons);
        }
    }, 100);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\biometric-attendance\resources\views\superadmin\system-settings-backup.blade.php ENDPATH**/ ?>