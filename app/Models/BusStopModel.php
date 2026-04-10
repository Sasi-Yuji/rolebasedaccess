<?php
namespace App\Models;

use CodeIgniter\Model;

class BusStopModel extends Model
{
    protected $table = 'bus_stops';
    protected $primaryKey = 'id';
    protected $allowedFields = ['route_id', 'stop_name', 'stop_order', 'arrival_time'];
}
