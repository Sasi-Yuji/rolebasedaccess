<div class="card">
    <h3 style="margin-bottom: 1.5rem; font-weight: 600;">Marks Portal</h3>
    <p style="color: #64748b; margin-bottom: 2rem;">Below are the grades submitted by your instructors for the current academic session.</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks</th>
                <th>Updated By</th>
                <th>Last Update</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($marks as $m): ?>
            <tr>
                <td><strong><?= $m['subject_name'] ?></strong></td>
                <td><span style="font-size: 1.125rem; font-weight: 600;"><?= $m['marks'] ?></span></td>
                <td style="color: #64748b;"><?= $m['teacher_name'] ?></td>
                <td style="color: #64748b; font-size: 0.875rem;"><?= date('M d, Y', strtotime($m['created_at'])) ?></td>
                <td>
                    <div style="width: 100%; max-width: 120px; background: #e2e8f0; height: 10px; border-radius: 5px; overflow: hidden; margin-bottom: 0.5rem;">
                        <div style="background: <?= ($m['marks'] >= 40) ? 'var(--success)' : 'var(--danger)' ?>; width: <?= $m['marks'] ?>%; height: 100%;"></div>
                    </div>
                    <span style="font-size: 0.75rem; font-weight: 600; color: #64748b;"><?= ($m['marks'] >= 40) ? 'Passed' : 'At Risk' ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
