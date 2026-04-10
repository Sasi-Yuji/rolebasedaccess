<div class="card" style="padding: 2.5rem; animation: slide-up 0.5s ease-out; box-shadow: var(--shadow-xl);">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h3 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.5rem;">Faculty Permissions</h3>
            <p style="color: #64748b; font-size: 0.875rem; font-weight: 500;">
                Configure specific system privileges for individual faculty members.
            </p>
        </div>
        <div style="background: #eef2ff; color: var(--primary); padding: 0.75rem 1.5rem; border-radius: 12px; font-weight: 700; font-size: 0.875rem;">
            <i class="fas fa-shield-alt" style="margin-right: 0.5rem;"></i> Identity & Access Management
        </div>
    </div>

    <form action="<?= base_url(session()->get('role') . '/permissions/save') ?>" method="POST">
        <?= csrf_field() ?>
        
        <div style="overflow-x: auto;">
            <table class="data-table" style="width: 100%; text-align: left; border-collapse: separate; border-spacing: 0;">
                <thead>
                    <?php 
                        $perms = [
                            'manage_students' => 'Manage Students',
                            'manage_marks' => 'Manage Marks',
                            'approve_requests' => 'Approve Requests'
                        ];
                    ?>
                    <tr>
                        <th style="padding: 1.25rem 1rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; border-top-left-radius: 12px; font-weight: 700; color: #475569; width: 250px;">Faculty Member</th>
                        <?php foreach ($perms as $label): ?>
                            <th style="padding: 1.25rem 1rem; background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: center; color: #64748b; font-size: 0.85rem; font-weight: 700;"><?= $label ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($facultyMembers)): ?>
                    <tr>
                        <td colspan="<?= count($perms) + 1 ?>" style="padding: 2rem; text-align: center; color: #94a3b8; font-weight: 600;">No faculty members found.</td>
                    </tr>
                    <?php else: foreach ($facultyMembers as $faculty): ?>
                    <tr>
                        <td style="padding: 1.25rem 1rem; border-bottom: 1px solid #f1f5f9;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #eef2ff; color: #4f46e5; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <?= substr($faculty['name'], 0, 1) ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #1e293b;"><?= $faculty['name'] ?></div>
                                    <div style="font-size: 0.75rem; color: #64748b;"><?= $faculty['department'] ?></div>
                                </div>
                            </div>
                        </td>
                        
                        <?php foreach ($perms as $key => $label): ?>
                        <td style="padding: 1.25rem 1rem; border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <label class="toggle-switch">
                                <input type="checkbox" name="permissions[<?= $faculty['id'] ?>][]" value="<?= $key ?>" 
                                    <?= in_array($key, $matrix[$faculty['id']] ?? []) ? 'checked' : '' ?>>
                                <span class="toggle-slider"></span>
                            </label>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; border-radius: 12px; font-weight: 800; font-size: 1.1rem; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">
                <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Configuration
            </button>
        </div>
    </form>
</div>

<style>
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    input:checked + .toggle-slider {
        background-color: #10b981;
    }
    input:disabled + .toggle-slider {
        opacity: 0.5;
        cursor: not-allowed;
    }
    input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }
    
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
