<div class="stat-grid">
    <div class="card stat-card">
        <div class="stat-icon icon-blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700;"><?= $stats['total_users'] ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Total Users</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon icon-orange">
            <i class="fas fa-user-shield"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700;"><?= $stats['admins'] ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">System Admins</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon icon-purple">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700;"><?= $stats['faculty'] ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Total Faculty</div>
        </div>
    </div>
    <div class="card stat-card">
        <div class="stat-icon icon-green">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <div style="font-size: 1.5rem; font-weight: 700;"><?= $stats['students'] ?></div>
            <div style="color: #64748b; font-size: 0.875rem;">Total Students</div>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-weight: 600;">Recent System Activities</h3>
        <a href="<?= base_url('superadmin/logs') ?>" style="color: var(--primary); text-decoration: none; font-size: 0.875rem; font-weight: 500;">View All</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Description</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentLogs as $log): ?>
                <tr>
                    <td>
                        <strong><?= $log['user_name'] ?? 'System' ?></strong>
                        <div style="font-size: 0.75rem; color: #64748b;"><?= $log['role'] ?? 'N/A' ?></div>
                    </td>
                    <td><span class="badge badge-superadmin"><?= $log['action'] ?></span></td>
                    <td><?= $log['description'] ?></td>
                    <td style="color: #64748b; font-size: 0.875rem;"><?= date('M d, H:i', strtotime($log['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
