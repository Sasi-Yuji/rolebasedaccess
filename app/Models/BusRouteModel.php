<?php
namespace App\Models;

use CodeIgniter\Model;

class BusRouteModel extends Model
{
    protected $table = 'bus_routes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['route_name', 'driver_name', 'driver_phone', 'timings', 'status'];
    protected $useTimestamps = true; // since created_at and updated_at might exist, but actually DB handles it.
}
