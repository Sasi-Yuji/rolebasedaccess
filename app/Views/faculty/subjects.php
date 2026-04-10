<div class="card">
    <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Course List</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Subject ID</th>
                <th>Subject Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($subjects as $sub): ?>
            <tr>
                <td>#<?= $sub['id'] ?></td>
                <td><strong><?= $sub['subject_name'] ?></strong></td>
                <td>
                    <a href="<?= base_url('faculty/marks/upload/' . $sub['id']) ?>" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.875rem;">
                        <i class="fas fa-edit"></i> Enter Marks
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
