<?php

namespace App\Controllers;

use App\Models\UserModel;

class CustomerAuth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            return redirect()->to(base_url('mi-cuenta'));
        }

        $data = ['title' => 'Iniciar Sesión | Dulce Gracia'];
        return view('customer/auth/login', $data);
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
        $email     = trim($this->request->getPost('email'));
        $password  = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            if ($user->role === 'admin') {
                return redirect()->back()->withInput()->with('error', 'Esta cuenta es de administración. Por favor ingresa desde el panel administrativo.');
            }

            // Crear sesión de cliente
            $sessionData = [
                'user_id'    => $user->id,
                'name'       => $user->name,
                'lastname'   => $user->lastname ?? '',
                'email'      => $user->email,
                'phone'      => $user->phone ?? '',
                'whatsapp'   => $user->whatsapp ?? '',
                'role'       => 'customer',
                'is_loyal'   => (bool) $user->is_loyal,
                'points'     => (int) ($user->points ?? 0),
                'isLoggedIn' => true
            ];

            session()->set($sessionData);

            // Sincronizar y fusionar carrito persistente
            $sessionCart = session()->get('cart') ?? [];
            $userCartModel = new \App\Models\UserCartModel();
            $syncedCart = $userCartModel->syncSessionToDb($user->id, $sessionCart);
            session()->set('cart', $syncedCart);

            return redirect()->to(base_url('mi-cuenta'))->with('success', '¡Bienvenido de nuevo, ' . esc($user->name) . '!');
        }

        return redirect()->back()->withInput()->with('error', 'Correo electrónico o contraseña incorrectos.');
    }

    public function register()
    {
        if (session()->get('isLoggedIn') && session()->get('role') === 'customer') {
            return redirect()->to(base_url('mi-cuenta'));
        }

        $data = ['title' => 'Crear Cuenta | Dulce Gracia'];
        return view('customer/auth/register', $data);
    }

    public function processRegister()
    {
        $rules = [
            'name'             => 'required|min_length[2]|max_length[100]',
            'lastname'         => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'phone'            => 'required|min_length[7]|max_length[50]',
            'whatsapp'         => 'permit_empty|min_length[7]|max_length[50]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]'
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Este correo electrónico ya se encuentra registrado.'
            ],
            'password_confirm' => [
                'matches' => 'Las contraseñas no coinciden.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $password = $this->request->getPost('password');
        $pwdCheck = \App\Controllers\PasswordReset::validatePassword($password);
        if (!$pwdCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $pwdCheck['message']);
        }

        $userModel = new UserModel();

        $userData = [
            'name'       => trim($this->request->getPost('name')),
            'lastname'   => trim($this->request->getPost('lastname')),
            'email'      => strtolower(trim($this->request->getPost('email'))),
            'phone'      => trim($this->request->getPost('phone')),
            'whatsapp'   => trim($this->request->getPost('whatsapp')),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => 'customer',
            'is_loyal'   => 0,
            'points'     => 0
        ];

        $userId = $userModel->insert($userData);

        // Auto-iniciar sesión
        $sessionData = [
            'user_id'    => $userId,
            'name'       => $userData['name'],
            'lastname'   => $userData['lastname'],
            'email'      => $userData['email'],
            'phone'      => $userData['phone'],
            'whatsapp'   => $userData['whatsapp'],
            'role'       => 'customer',
            'is_loyal'   => false,
            'points'     => 0,
            'isLoggedIn' => true
        ];

        session()->set($sessionData);

        return redirect()->to(base_url('mi-cuenta'))->with('success', '¡Registro exitoso! Te damos la bienvenida a Dulce Gracia.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Has cerrado sesión correctamente.');
    }
}
