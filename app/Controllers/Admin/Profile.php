<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

/**
 * Profile — Gestión del perfil del administrador:
 * - Ver perfil
 * - Cambiar contraseña (password_verify + password_hash)
 */
class Profile extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $admin = $userModel->find(session()->get('id'));

        if (!$admin) {
            return redirect()->to(base_url('admin/dashboard'))
                             ->with('error', 'No se encontró el perfil del administrador.');
        }

        return view('admin/profile/index', [
            'title' => 'Mi Perfil | Admin',
            'admin' => $admin,
        ]);
    }

    public function changePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        $messages = [
            'current_password' => ['required' => 'La contraseña actual es obligatoria.'],
            'new_password'     => [
                'required'   => 'La nueva contraseña es obligatoria.',
                'min_length' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            ],
            'confirm_password' => [
                'required' => 'La confirmación de contraseña es obligatoria.',
                'matches'  => 'Las contraseñas nuevas no coinciden.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $userModel      = new UserModel();
        $adminId        = session()->get('id');
        $admin          = $userModel->find($adminId);

        if (!$admin) {
            return redirect()->back()->with('error', 'Sesión no válida. Por favor inicie sesión nuevamente.');
        }

        // Verificar contraseña actual
        if (!password_verify($this->request->getPost('current_password'), $admin->password)) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'La contraseña actual es incorrecta.');
        }

        $newPassword = $this->request->getPost('new_password');
        $pwdCheck = \App\Controllers\PasswordReset::validatePassword($newPassword);
        if (!$pwdCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $pwdCheck['message']);
        }

        // Actualizar contraseña con hash seguro
        $userModel->update($adminId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_BCRYPT),
        ]);

        // Notificar sin revelar datos sensibles
        log_message('info', "Admin ID={$adminId} cambió su contraseña exitosamente.");

        return redirect()->to(base_url('admin/perfil'))
                         ->with('success', '✅ Contraseña actualizada exitosamente.');
    }

    /**
     * Actualiza el nombre y email del perfil (sin cambiar contraseña).
     */
    public function updateInfo()
    {
        $rules = [
            'name'  => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $adminId   = session()->get('id');
        $userModel = new UserModel();

        $userModel->update($adminId, [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
        ]);

        // Actualizar datos en sesión
        session()->set('name',  $this->request->getPost('name'));
        session()->set('email', $this->request->getPost('email'));

        return redirect()->to(base_url('admin/perfil'))
                         ->with('success', '✅ Información actualizada correctamente.');
    }
}
