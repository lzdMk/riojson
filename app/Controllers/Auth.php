<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    public function signin()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login');
    }
    
    public function signup()
    {
        // Check if user is already logged in
        if ($this->isLoggedIn()) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/signup');
    }
    
    public function login()
    {
        // Handle login POST request
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');
        
        // Basic validation
        if (empty($email) || empty($password)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please fill in all fields'
            ]);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address'
            ]);
        }
        
        // Verify login credentials
        $user = $this->userModel->verifyLogin($email, $password);
        
        if ($user) {
            // Set session data
            $sessionData = [
                'user_id' => $user['user_id'], // Updated to use new column name
                'email' => $user['email'],
                'logged_in' => true
            ];
            
            session()->set($sessionData);
            
            // Set remember me cookie if requested
            if ($remember) {
                $this->response->setCookie('remember_token', base64_encode($user['email']), 86400 * 30); // 30 days
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => base_url('dashboard')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid email or password'
            ]);
        }
    }
    
    public function register()
    {
        // Handle signup POST request
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('confirm_password');
        $agreeTerms = $this->request->getPost('agree_terms');
        
        // Basic validation
        if (empty($email) || empty($password) || empty($confirmPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please fill in all fields'
            ]);
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address'
            ]);
        }
        
        if (strlen($password) < 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must be at least 8 characters long'
            ]);
        }
        
        if ($password !== $confirmPassword) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Passwords do not match'
            ]);
        }
        
        if (!$agreeTerms) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please agree to the Terms of Service and Privacy Policy'
            ]);
        }
        
        // Check if email already exists
        if ($this->userModel->emailExists($email)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This email is already registered. Please use a different email.'
            ]);
        }
        
        // Create new user
        try {
            $userId = $this->userModel->createUser($email, $password);
            
            if ($userId) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Account created successfully! You can now sign in.',
                    'redirect' => base_url('signin')
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create account. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while creating your account. Please try again.'
            ]);
        }
    }
    
    public function logout()
    {
        // Clear session
        session()->destroy();
        
        // Clear remember me cookie
        $this->response->deleteCookie('remember_token');
        
        return redirect()->to('/signin')->with('message', 'You have been logged out successfully');
    }
    
    public function forgotPassword()
    {
        // Handle forgot password request
        $email = $this->request->getPost('email');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please enter a valid email address'
            ]);
        }
        
        // Check if email exists
        if (!$this->userModel->emailExists($email)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No account found with this email address'
            ]);
        }
        
        // TODO: Implement password reset logic
        // - Generate reset token
        // - Send reset email
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Password reset instructions will be sent to your email (Feature coming soon)'
        ]);
    }
}
