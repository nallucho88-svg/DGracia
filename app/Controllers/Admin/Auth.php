<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        // Si ya está logueado y es admin, redirigir al dashboard
        if (session()->get('isLoggedIn') && session()->get('role') === 'admin') {
            return redirect()->to(base_url('admin/dashboard'));
        }

        $data = ['title' => 'Login | Dulce Gracia Admin'];
        return view('admin/auth/login', $data);
    }

    public function processLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            if ($user->role !== 'admin') {
                return redirect()->back()->with('error', 'Acceso denegado. No tienes permisos de administrador.');
            }

            // Crear sesión
            $sessionData = [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role'       => $user->role,
                'isLoggedIn' => true
            ];

            session()->set($sessionData);
            
            return redirect()->to(base_url('admin/dashboard'))->with('success', 'Bienvenido al panel de administración.');
        }

        return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }
}
