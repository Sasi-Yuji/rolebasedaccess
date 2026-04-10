<?php
namespace App\Models;

use CodeIgniter\Model;

class StudentBusModel extends Model
{
    protected $table = 'student_bus';
    protected $primaryKey = 'id';
    protected $allowedFields = ['student_id', 'route_id', 'stop_id'];

    public function getStudentBusInfo($studentId)
    {
        return $this->select('student_bus.*, bus_routes.route_name, bus_routes.driver_name, bus_routes.driver_phone, bus_routes.status, bus_stops.stop_name, bus_stops.arrival_time')
            ->join('bus_routes', 'bus_routes.id = student_bus.route_id', 'left')
            ->join('bus_stops', 'bus_stops.id = student_bus.stop_id', 'left')
            ->where('student_id', $studentId)
            ->first();
    }
}
