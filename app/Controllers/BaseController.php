<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // E.g.: $this->session = service('session');
    }

    /**
     * Check if current user is logged in
     */
    public function isLoggedIn()
    {
        return session()->get('logged_in') === true;
    }

    /**
     * Check if current user is admin
     */
    public function isAdmin()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return false;
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        
        return $user && $user['user_type'] === 'admin';
    }

    /**
     * Get current user data
     */
    protected function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $userId = session()->get('user_id');
        if (!$userId) {
            return null;
        }

        $userModel = new \App\Models\UserModel();
        return $userModel->find($userId);
    }

    /**
     * Get common data for all views (including admin status)
     */
    protected function getCommonViewData()
    {
        $user = $this->getCurrentUser();
        $isAdmin = $user && $user['user_type'] === 'admin';

        $data = [
            'is_logged_in' => $this->isLoggedIn(),
            'is_admin' => $isAdmin,
            'current_user' => $user
        ];

        if ($user) {
            $data['user_email'] = $user['email'];
            $data['user_type'] = $user['user_type'];
        }

        return $data;
    }
}
