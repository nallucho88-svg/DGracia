<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    public function index()
    {
        $model = new NotificationModel();
        
        $unreadCount = $model->where('is_read', 0)->countAllResults();
        $recent = $model->orderBy('created_at', 'DESC')->limit(10)->findAll();

        return $this->response->setJSON([
            'status'       => 'success',
            'unread_count' => $unreadCount,
            'notifications'=> $recent
        ]);
    }

    public function markAllRead()
    {
        $model = new NotificationModel();
        $model->where('is_read', 0)->set(['is_read' => 1])->update();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Todas las notificaciones marcadas como leídas.']);
        }

        return redirect()->back()->with('success', 'Todas las notificaciones han sido marcadas como leídas.');
    }

    public function read($id)
    {
        $model = new NotificationModel();
        $notification = $model->find($id);

        if ($notification) {
            $model->update($id, ['is_read' => 1]);
            $targetUrl = !empty($notification->target_url) ? $notification->target_url : base_url('admin/dashboard');
            return redirect()->to($targetUrl);
        }

        return redirect()->to(base_url('admin/dashboard'));
    }
}
