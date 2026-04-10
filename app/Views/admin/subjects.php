<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Subject Catalog</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $sub): ?>
                <tr>
                    <td>#<?= $sub['id'] ?></td>
                    <td><strong><?= $sub['subject_name'] ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Subject Assignment -->
        <h3 style="margin: 2rem 0 1rem; font-weight: 600;">Assign Subject to Faculty</h3>
        <form action="<?= base_url('admin/subjects/assign') ?>" method="POST" style="display: flex; gap: 1rem; align-items: flex-end;">
            <?= csrf_field() ?>
            <div class="form-group" style="flex: 1;">
                <label>Faculty Member</label>
                <select name="faculty_id" class="form-control" required>
                    <option value="">Select Faculty</option>
                    <?php foreach ($facultyMembers as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= $f['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Subject</label>
                <select name="subject_id" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php foreach ($subjects as $sub): ?>
                        <option value="<?= $sub['id'] ?>"><?= $sub['subject_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-bottom: 1.25rem;">Assign Now</button>
        </form>
    </div>

    <div class="card" style="height: fit-content;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Create Subject</h3>
        <form action="<?= base_url('admin/subjects/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Subject Name</label>
                <input type="text" name="subject_name" class="form-control" placeholder="e.g. Computer Architecture" required>
            </div>
            <div style="text-align: right; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem;">Create New Subject</button>
            </div>
        </form>
    </div>
</div>
