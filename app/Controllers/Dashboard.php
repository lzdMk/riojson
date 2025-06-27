<?php

namespace App\Controllers;

use App\Models\UserModel;

class Dashboard extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    public function index()
    {
        // Check if user is logged in
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }
        
        // Get user info
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return redirect()->to('/signin');
        }
        
        // If user is admin, redirect to admin panel
        if ($user['user_type'] === 'admin') {
            return redirect()->to('/admin');
        }
        
        // For regular users, redirect to silos page
        return redirect()->to('/dashboard/silos');
    }
     public function main()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/signin');
        }

        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/signin');
        }

        $data = [
            'title' => 'Dashboard - RioConsoleJSON',
            'user' => $user,
            'user_email' => $user['email'],
            'is_admin' => $user['user_type'] === 'admin'
        ];

        return view('dashboard/main', $data);
    }
 


}
