<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- List of Routes -->
    <div class="card">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;"><i class="fas fa-bus"></i> Bus Routes & Live Status</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Route Name</th>
                    <th>Driver Info</th>
                    <th>Timings</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($routes as $route): ?>
                <tr>
                    <td><strong><?= esc($route['route_name']) ?></strong></td>
                    <td><?= esc($route['driver_name']) ?><br><small><?= esc($route['driver_phone']) ?></small></td>
                    <td><?= esc($route['timings']) ?></td>
                    <td>
                        <form action="<?= base_url('admin/bus/update-status') ?>" method="POST" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="route_id" value="<?= $route['id'] ?>">
                            <select name="status" class="form-control" onchange="this.form.submit()" style="padding: 0.2rem 0.5rem; font-size: 0.8rem; width: auto; border-radius: 4px;">
                                <option value="idle" <?= $route['status'] == 'idle' ? 'selected' : '' ?>>Idle</option>
                                <option value="departed" <?= $route['status'] == 'departed' ? 'selected' : '' ?>>Departed</option>
                                <option value="on_route" <?= $route['status'] == 'on_route' ? 'selected' : '' ?>>On Route</option>
                                <option value="arrived" <?= $route['status'] == 'arrived' ? 'selected' : '' ?>>Arrived</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="<?= base_url('admin/bus/delete/'.$route['id']) ?>" class="btn btn-danger" style="padding: 0.3rem 0.6rem; font-size: 0.8rem; border-radius: 6px;" onclick="return confirm('Delete this route?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($routes)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No routes found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <hr style="margin: 2rem 0; border: 0; border-top: 1px solid var(--border);">

        <h3 style="margin: 0 0 1.5rem; font-weight: 600;"><i class="fas fa-map-marker-alt"></i> Add Stop to Route</h3>
        <form action="<?= base_url('admin/bus/add-stop') ?>" method="POST" style="display:flex; gap: 1rem; align-items:flex-end; flex-wrap: wrap;">
            <?= csrf_field() ?>
            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label>Route</label>
                <select name="route_id" class="form-control" required>
                    <option value="">Select Route</option>
                    <?php foreach($routes as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= $r['route_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
                <label>Stop Name</label>
                <input type="text" name="stop_name" class="form-control" required>
            </div>
            <div class="form-group" style="width: 100px; margin-bottom: 0;">
                <label>ETA</label>
                <input type="text" name="arrival_time" class="form-control" placeholder="08:30 AM" required>
            </div>
            <div class="form-group" style="width: 80px; margin-bottom: 0;">
                <label>Order</label>
                <input type="number" name="stop_order" class="form-control" value="1" required>
            </div>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1rem;">Add Stop</button>
        </form>
    </div>

    <!-- Create Route Form -->
    <div class="card" style="height: fit-content;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 600;"><i class="fas fa-plus"></i> Create New Route</h3>
        <form action="<?= base_url('admin/bus/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Route Name / Number</label>
                <input type="text" name="route_name" class="form-control" placeholder="e.g. Route 1A - City Center" required>
            </div>
            <div class="form-group">
                <label>Driver Name</label>
                <input type="text" name="driver_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Driver Phone</label>
                <input type="text" name="driver_phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Timings</label>
                <input type="text" name="timings" class="form-control" placeholder="07:00 AM - 09:00 AM" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem; padding: 0.75rem;">Save Details</button>
        </form>
    </div>
</div>
