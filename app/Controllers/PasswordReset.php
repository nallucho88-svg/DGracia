<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetModel;
use App\Libraries\EmailService;

class PasswordReset extends BaseController
{
    /**
     * Helper para validar requisitos de contraseña:
     * - Al menos una letra
     * - Al menos un número
     * - Mínimo 6 caracteres
     */
    public static function validatePassword(string $password): array
    {
        if (strlen($password) < 6) {
            return ['valid' => false, 'message' => 'No se pudo realizar el proceso. La contraseña debe contener al menos 6 caracteres.'];
        }
        if (!preg_match('/[a-zA-Z]/', $password)) {
            return ['valid' => false, 'message' => 'No se pudo realizar el proceso. La contraseña debe contener al menos una letra.'];
        }
        if (!preg_match('/[0-9]/', $password)) {
            return ['valid' => false, 'message' => 'No se pudo realizar el proceso. La contraseña debe contener al menos un número.'];
        }
        return ['valid' => true, 'message' => 'Contraseña válida'];
    }

    public static function isStrongPassword(string $password): bool
    {
        $res = self::validatePassword($password);
        return $res['valid'];
    }

    /**
     * Vista 1: Formulario "¿Olvidaste tu contraseña?"
     */
    public function forgotForm()
    {
        return view('customer/auth/forgot_password', [
            'title' => 'Recuperar Contraseña | Dulce Gracia'
        ]);
    }

    /**
     * Procesar solicitud de correo de recuperación (Respuesta Neutra para Evitar Enumeración - Req 40)
     */
    public function sendResetLink()
    {
        $email = strtolower(trim($this->request->getPost('email') ?? ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Por favor ingresa un correo electrónico válido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

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

            // Enviar correo con el enlace y token crudo
            EmailService::sendPasswordResetEmail($email, $rawToken);
        }

        // Respuesta neutra estricta (Req 40)
        return redirect()->back()->with('info', 'Si existe una cuenta asociada a este correo electrónico, recibirás instrucciones para restablecer tu contraseña en unos momentos.');
    }

    /**
     * Vista 2: Formulario para ingresar la nueva contraseña usando el token
     */
    public function resetForm(string $token)
    {
        $tokenHash = hash('sha256', $token);
        $resetModel = new PasswordResetModel();
        $now = date('Y-m-d H:i:s');

        $record = $resetModel->where('token_hash', $tokenHash)
            ->where('expires_at >=', $now)
            ->first();

        if (!$record) {
            return redirect()->to(base_url('login'))->with('error', 'El enlace de recuperación es inválido, ha expirado o ya fue utilizado.');
        }

        return view('customer/auth/reset_password', [
            'title' => 'Nueva Contraseña | Dulce Gracia',
            'token' => $token
        ]);
    }

    /**
     * Procesar el cambio definitivo de contraseña
     */
    public function processReset()
    {
        $token           = trim($this->request->getPost('token') ?? '');
        $password        = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('error', 'No se pudo realizar el proceso. Las contraseñas no coinciden.');
        }

        $pwdCheck = self::validatePassword($password);
        if (!$pwdCheck['valid']) {
            return redirect()->back()->withInput()->with('error', $pwdCheck['message']);
        }

        $tokenHash = hash('sha256', $token);
        $resetModel = new PasswordResetModel();
        $now = date('Y-m-d H:i:s');

        $record = $resetModel->where('token_hash', $tokenHash)
            ->where('expires_at >=', $now)
            ->first();

        if (!$record) {
            return redirect()->to(base_url('login'))->with('error', 'El token ha expirado o no es válido.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $record->email)->first();

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'No se encontró la cuenta asociada.');
        }

        // Actualizar contraseña con hash seguro
        $userModel->update($user->id, [
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        // Invalidar token usado (de un solo uso)
        $resetModel->where('email', $record->email)->delete();

        return redirect()->to(base_url('login'))->with('success', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes iniciar sesión con tu nueva clave.');
    }
}
