<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Controllers\PasswordReset;
use App\Libraries\EmailService;

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

    /**
     * Vista: Formulario "¿Olvidaste tu contraseña?" del Admin
     */
    public function forgotForm()
    {
        if (session()->get('isLoggedIn') && session()->get('role') === 'admin') {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/auth/forgot_password', [
            'title' => 'Recuperar Contraseña Admin | Dulce Gracia'
        ]);
    }

    /**
     * Procesar solicitud de correo de recuperación para Administrador
     */
    public function sendResetLink()
    {
        $email = strtolower(trim($this->request->getPost('email') ?? ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Por favor ingresa un correo electrónico válido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->where('role', 'admin')->first();

        if ($user) {
            // Generar token seguro
            $rawToken  = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawToken);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $resetModel = new PasswordResetModel();
            // Eliminar tokens previos de este correo
            $resetModel->where('email', $email)->delete();

            $resetModel->insert([
                'email'      => $email,
                'token_hash' => $tokenHash,
                'expires_at' => $expiresAt
            ]);

            // Enviar correo con el enlace y token crudo (isAdmin = true)
            EmailService::sendPasswordResetEmail($email, $rawToken, true);
        }

        // Respuesta neutra estricta para evitar enumeración (Req 40)
        return redirect()->back()->with('info', 'Si existe una cuenta de administrador asociada a este correo electrónico, recibirás instrucciones para restablecer tu contraseña en unos momentos.');
    }

    /**
     * Vista: Formulario para ingresar la nueva contraseña usando el token (Admin)
     */
    public function resetForm(string $token)
    {
        $tokenHash  = hash('sha256', $token);
        $resetModel = new PasswordResetModel();
        $now        = date('Y-m-d H:i:s');

        $record = $resetModel->where('token_hash', $tokenHash)
            ->where('expires_at >=', $now)
            ->first();

        if (!$record) {
            return redirect()->to(base_url('admin/login'))->with('error', 'El enlace de recuperación es inválido, ha expirado o ya fue utilizado.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $record->email)->where('role', 'admin')->first();
        if (!$user) {
            return redirect()->to(base_url('admin/login'))->with('error', 'El enlace de recuperación no corresponde a una cuenta de administrador.');
        }

        return view('admin/auth/reset_password', [
            'title' => 'Nueva Contraseña Admin | Dulce Gracia',
            'token' => $token
        ]);
    }

    /**
     * Procesar el cambio definitivo de contraseña (Admin)
     */
    public function processReset()
    {
        $token           = trim($this->request->getPost('token') ?? '');
        $password        = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('error', 'No se pudo realizar el proceso. Las contraseñas no coinciden.');
        }

        $pwdCheck = PasswordReset::validatePassword($password);
        if (!$pwdCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $pwdCheck['message']);
        }

        $tokenHash  = hash('sha256', $token);
        $resetModel = new PasswordResetModel();
        $now        = date('Y-m-d H:i:s');

        $record = $resetModel->where('token_hash', $tokenHash)
            ->where('expires_at >=', $now)
            ->first();

        if (!$record) {
            return redirect()->to(base_url('admin/login'))->with('error', 'El token ha expirado o no es válido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $record->email)->where('role', 'admin')->first();

        if (!$user) {
            return redirect()->to(base_url('admin/login'))->with('error', 'No se encontró la cuenta de administrador asociada.');
        }

        // Actualizar contraseña con hash seguro
        $userModel->update($user->id, [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Invalidar token usado (de un solo uso)
        $resetModel->where('email', $record->email)->delete();

        return redirect()->to(base_url('admin/login'))->with('success', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes ingresar al panel de administración.');
    }
}
