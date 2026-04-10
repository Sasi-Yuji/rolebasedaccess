<div class="card">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;"><i class="fas fa-bus"></i> Live Campus Bus Tracker</h2>
    
    <?php if(!empty($myBus)): ?>
        <div style="margin-bottom: 2rem; background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%); color: white; padding: 1.5rem; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3);">
            <div>
                <h3 style="font-size: 1rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; opacity: 0.9;">Your Assigned Transport</h3>
                <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; font-family: 'Outfit', sans-serif;"><?= esc($myBus['route_name']) ?></h2>
                <p style="font-size: 0.95rem; opacity: 0.9;"><i class="fas fa-map-marker-alt"></i> Drop Stop: <strong><?= esc($myBus['stop_name']) ?: 'Not Assigned' ?></strong> (ETA: <?= esc($myBus['arrival_time']) ?: 'N/A' ?>)</p>
            </div>
            <div style="background: rgba(255,255,255,0.15); padding: 1rem 1.5rem; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);">
                <p style="font-size: 0.85rem; text-transform: uppercase; opacity: 0.9; margin-bottom: 0.3rem;">Current Status</p>
                <div style="font-size: 1.25rem; font-weight: 700;">
                    <?php if($myBus['status'] == 'departed'): ?><i class="fas fa-bus-alt" style="color: #fcd34d;"></i> Departed
                    <?php elseif($myBus['status'] == 'on_route'): ?><i class="fas fa-route" style="color: #60a5fa;"></i> On Route
                    <?php elseif($myBus['status'] == 'arrived'): ?><i class="fas fa-check-circle" style="color: #34d399;"></i> Arrived
                    <?php else: ?><i class="fas fa-parking" style="color: #cbd5e1;"></i> Idle<?php endif; ?>
                </div>
            </div>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--dark);">All Campus Routes</h3>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <?php foreach($allRoutes as $route): ?>
        <div style="border: 1px solid var(--border); border-radius: 12px; padding: 1.5rem; background: var(--white); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); transition: transform 0.2s;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--dark);"><?= esc($route['route_name']) ?></h3>
                    <p style="color: var(--secondary); font-size: 0.875rem;"><i class="fas fa-clock"></i> <?= esc($route['timings']) ?></p>
                </div>
                <?php 
                    $statusColor = '#64748b';
                    $statusIcon = 'fa-parking';
                    $statusKey = $route['status'];
                    if($statusKey == 'departed') { $statusColor = '#f59e0b'; $statusIcon = 'fa-bus-alt'; }
                    elseif($statusKey == 'on_route') { $statusColor = '#3b82f6'; $statusIcon = 'fa-route'; }
                    elseif($statusKey == 'arrived') { $statusColor = '#10b981'; $statusIcon = 'fa-check-circle'; }
                ?>
                <div style="background: <?= $statusColor ?>15; color: <?= $statusColor ?>; padding: 0.5rem 0.8rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; display: flex; align-items: center; gap: 0.5rem; border: 1px solid <?= $statusColor ?>30;">
                    <i class="fas <?= $statusIcon ?>"></i> <?= esc(str_replace('_', ' ', $route['status'])) ?>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; background: #f8fafc; padding: 1rem; border-radius: 8px;">
                <div style="width: 40px; height: 40px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <p style="font-size: 0.875rem; font-weight: 600; color: var(--dark); margin: 0;"><?= esc($route['driver_name']) ?></p>
                    <p style="font-size: 0.75rem; color: var(--secondary); margin: 0; margin-top: 0.2rem;"><i class="fas fa-phone-alt"></i> <?= esc($route['driver_phone']) ?></p>
                </div>
            </div>

            <h4 style="font-size: 0.85rem; font-weight: 700; margin-bottom: 1rem; color: var(--secondary); text-transform: uppercase; letter-spacing: 0.5px;">Route Schedule</h4>
            <div style="position: relative; padding-left: 1.25rem; border-left: 2px dashed #cbd5e1; margin-left: 0.5rem;">
                <?php 
                $routeStops = array_filter($allStops, function($s) use ($route) { return $s['route_id'] == $route['id']; });
                usort($routeStops, function($a, $b) { return $a['stop_order'] <=> $b['stop_order']; });
                
                if(!empty($routeStops)):
                    foreach($routeStops as $index => $stop): 
                        // simple logic to "highlight" current stop if departed
                        $isNext = ($statusKey == 'departed' || $statusKey == 'on_route') && $index == 1; // dummy visual logic
                        $dotColor = $isNext ? 'var(--primary)' : '#94a3b8';
                ?>
                    <div style="position: relative; margin-bottom: 1.25rem;">
                        <div style="position: absolute; left: -1.65rem; top: 0.25rem; width: 14px; height: 14px; background: <?= $dotColor ?>; border: 3px solid white; border-radius: 50%; box-shadow: 0 0 0 1px <?= $dotColor ?>;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <strong style="font-size: 0.95rem; color: var(--dark); font-weight: <?= $isNext ? '700' : '500' ?>;"><?= esc($stop['stop_name']) ?></strong>
                            <span style="font-size: 0.8rem; background: <?= $isNext ? 'var(--primary)' : '#e2e8f0' ?>; color: <?= $isNext ? 'white' : 'var(--secondary)' ?>; padding: 0.2rem 0.5rem; border-radius: 6px; font-weight: 600;"><i class="far fa-clock"></i> <?= esc($stop['arrival_time']) ?></span>
                        </div>
                    </div>
                <?php 
                    endforeach;
                else: 
                ?>
                    <p style="font-size: 0.85rem; color: var(--secondary); font-style: italic;">No stops defined for this route yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if(empty($allRoutes)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 4rem; background: #f8fafc; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                    <i class="fas fa-route" style="font-size: 2.5rem; color: #cbd5e1;"></i>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 600; color: var(--dark); margin-bottom: 0.5rem;">No Active Routes</h3>
                <p style="color: var(--secondary);">The administration hasn't set up any bus routes yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
