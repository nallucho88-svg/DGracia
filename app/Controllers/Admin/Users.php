<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\PointTransactionModel;

/**
 * Users — Gestión completa de clientes registrados, puntos e integridad histórica.
 */
class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $search = trim($this->request->getGet('search') ?? '');

        $builder = $userModel->where('role', 'customer');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('lastname', $search)
                ->orLike('email', $search)
                ->orLike('phone', $search)
            ->groupEnd();
        }

        $users = $builder->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users/index', [
            'title'  => 'Gestión de Clientes | Admin',
            'users'  => $users,
            'search' => $search
        ]);
    }

    /**
     * Alternar estado de Cliente Fiel (is_loyal)
     */
    public function toggleLoyal(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }

        $userModel->update($id, ['is_loyal' => $user->is_loyal ? 0 : 1]);

        return redirect()->back()->with('success', 'Estado de cliente fiel actualizado correctamente.');
    }

    /**
     * Agregar Puntos Manualmente a un Cliente (con Motivo e Historial)
     */
    public function addPoints()
    {
        $userId = (int) $this->request->getPost('user_id');
        $pointsToAdd = (int) $this->request->getPost('points');
        $reason = trim($this->request->getPost('reason') ?? 'Ajuste administrativo de puntos');

        if ($userId <= 0 || $pointsToAdd <= 0) {
            return redirect()->back()->with('error', 'Debes ingresar un cliente válido y una cantidad de puntos mayor a cero.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'El cliente especificado no existe.');
        }

        $currentPoints = (int) ($user->points ?? 0);
        $newPoints = $currentPoints + $pointsToAdd;

        // Actualizar saldo de puntos en usuario
        $userModel->update($userId, ['points' => $newPoints]);

        // Registrar movimiento en el historial
        $ptModel = new PointTransactionModel();
        $ptModel->insert([
            'user_id'     => $userId,
            'order_id'    => null,
            'points'      => $pointsToAdd,
            'type'        => 'adjustment',
            'description' => 'Ajuste manual (+' . $pointsToAdd . ' pts): ' . $reason
        ]);

        return redirect()->back()->with('success', "Se agregaron {$pointsToAdd} puntos a {$user->name} exitosamente.");
    }

    /**
     * Eliminar / Desactivar Cliente (Soft Delete para Preservar Integridad Histórica)
     */
    public function deleteCustomer()
    {
        $userId = (int) $this->request->getPost('user_id');

        if ($userId <= 0) {
            return redirect()->back()->with('error', 'ID de cliente inválido.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->back()->with('error', 'El cliente no existe o ya fue eliminado.');
        }

        // Realizar Soft Delete marcando deleted_at para no romper pedidos históricos
        $userModel->update($userId, [
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'El cliente ha sido eliminado/desactivado conservando su historial de compras intacto.');
    }
}

