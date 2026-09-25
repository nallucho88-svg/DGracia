<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MessageModel;

class Contact extends BaseController
{
    public function index()
    {
        $messageModel = new MessageModel();
        $messages = $messageModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'    => 'Mensajes de Contacto & PQRS | Admin',
            'messages' => $messages
        ];

        return view('admin/contact/index', $data);
    }

    public function toggleRead($id)
    {
        $messageModel = new MessageModel();
        $message = $messageModel->find($id);

        if (!$message) {
            return redirect()->to(base_url('admin/contacto'))->with('error', 'Mensaje no encontrado.');
        }

        $newStatus = ($message->status === 'read') ? 'unread' : 'read';
        $messageModel->update($id, ['status' => $newStatus]);

        $msg = ($newStatus === 'read') ? 'Mensaje marcado como leído.' : 'Mensaje marcado como pendiente.';
        return redirect()->to(base_url('admin/contacto'))->with('success', $msg);
    }

    public function delete($id)
    {
        $messageModel = new MessageModel();
        $message = $messageModel->find($id);

        if ($message) {
            $messageModel->delete($id);
            return redirect()->to(base_url('admin/contacto'))->with('success', 'Mensaje eliminado correctamente.');
        }

        return redirect()->to(base_url('admin/contacto'))->with('error', 'No se pudo eliminar el mensaje.');
    }
}
