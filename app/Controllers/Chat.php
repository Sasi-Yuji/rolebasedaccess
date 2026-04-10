<?php

namespace App\Controllers;

use App\Models\User_model;
use App\Models\ConversationModel;

class Chat extends BaseController
{
    public function index()
    {
        $userModel = new User_model();
        $convModel = new ConversationModel();
        
        $userId = session()->get('id');
        
        $data = [
            'title' => 'CampusPro Chat',
            'currentUser' => $userModel->find($userId),
            'allUsers' => $userModel->where('id !=', $userId)->findAll(),
            'role' => session()->get('role')
        ];

        // This view will be a standalone full-page layout
        return view('chat/index', $data);
    }
}
