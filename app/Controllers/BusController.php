<?php

namespace App\Controllers;

use App\Models\BusRouteModel;
use App\Models\BusStopModel;
use App\Models\StudentBusModel;
use CodeIgniter\Controller;

class BusController extends BaseController
{
    protected $busRouteModel;
    protected $busStopModel;
    protected $studentBusModel;

    public function __construct()
    {
        $this->busRouteModel = new BusRouteModel();
        $this->busStopModel = new BusStopModel();
        $this->studentBusModel = new StudentBusModel();
    }

    // --- Admin Methods ---

    public function adminBus()
    {
        $data = [
            'title' => 'Manage Transport',
            'routes' => $this->busRouteModel->findAll(),
            'stops' => $this->busStopModel->findAll()
        ];
        return view('layouts/header', $data) . view('admin/bus_routes', $data) . view('layouts/footer');
    }

    public function storeRoute()
    {
        $this->busRouteModel->save([
            'route_name'   => $this->request->getPost('route_name'),
            'driver_name'  => $this->request->getPost('driver_name'),
            'driver_phone' => $this->request->getPost('driver_phone'),
            'timings'      => $this->request->getPost('timings'),
            'status'       => 'idle'
        ]);
        return redirect()->back()->with('success', 'Bus route added successfully');
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('route_id');
        $status = $this->request->getPost('status');
        $this->busRouteModel->update($id, ['status' => $status]);
        return redirect()->back()->with('success', 'Route status updated');
    }

    public function addStop()
    {
        $this->busStopModel->save([
            'route_id'     => $this->request->getPost('route_id'),
            'stop_name'    => $this->request->getPost('stop_name'),
            'stop_order'   => $this->request->getPost('stop_order') ?: 0,
            'arrival_time' => $this->request->getPost('arrival_time')
        ]);
        return redirect()->back()->with('success', 'Bus stop added successfully');
    }

    public function deleteRoute($id)
    {
        $this->busRouteModel->delete($id);
        return redirect()->back()->with('success', 'Route deleted successfully');
    }

    // --- Student Methods ---

    public function studentBus()
    {
        $studentId = session()->get('id');
        $myBusInfo = $this->studentBusModel->getStudentBusInfo($studentId);
        
        $allRoutes = $this->busRouteModel->findAll();
        $allStops = $this->busStopModel->findAll();

        $data = [
            'title' => 'Bus Tracker',
            'myBus' => $myBusInfo,
            'allRoutes' => $allRoutes,
            'allStops' => $allStops
        ];
        return view('layouts/header', $data) . view('student/bus_tracker', $data) . view('layouts/footer');
    }
}
