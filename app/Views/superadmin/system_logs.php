<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-weight: 600;">Full System Activity Logs</h3>
        <button class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            <i class="fas fa-download"></i> Export CSV
        </button>
    </div>
    
    <table id="logsTable" class="data-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Action</th>
                <th>Description</th>
                <th>IP Address</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><strong><?= $log['user_name'] ?? 'System' ?></strong></td>
                    <td><span class="badge badge-<?= $log['role'] ?? 'light' ?>"><?= $log['role'] ?? 'N/A' ?></span></td>
                    <td><span class="badge badge-superadmin" style="text-transform: uppercase;"><?= $log['action'] ?></span></td>
                    <td><?= $log['description'] ?></td>
                    <td style="color: #64748b; font-family: monospace;"><?= $log['ip_address'] ?></td>
                    <td style="color: #64748b; font-size: 0.875rem;"><?= date('M d, Y - H:i:s', strtotime($log['created_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#logsTable').DataTable({
            order: [[5, 'desc']], // Default sort by Date & Time descendant
            pageLength: 10,
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search logs..."
            }
        });
    });
</script>
