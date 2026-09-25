<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BannerModel;

class Banners extends BaseController
{
    public function index()
    {
        $bannerModel = new BannerModel();

        if ($this->request->getMethod() === 'POST') {
            $title = $this->request->getPost('title');
            
            $data = [
                'title' => $title,
                'link' => $this->request->getPost('link'),
                'priority' => $this->request->getPost('priority') ?? 0,
                'status' => $this->request->getPost('status') ?? 1
            ];

            // Manejo de la imagen
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/banners', $newName);
                
                $data['image_path'] = 'uploads/banners/' . $newName;
                
                $bannerModel->insert($data);
                return redirect()->to(base_url('admin/banners'))->with('success', 'Banner agregado exitosamente.');
            } else {
                return redirect()->to(base_url('admin/banners'))->with('error', 'Debes seleccionar una imagen válida.');
            }
        }

        $banners = $bannerModel->orderBy('priority', 'ASC')->findAll();

        $data = [
            'title' => 'Gestión de Banners | Admin',
            'banners' => $banners
        ];

        return view('admin/banners/index', $data);
    }

    public function delete($id)
    {
        $bannerModel = new BannerModel();
        
        $banner = $bannerModel->find($id);
        if ($banner) {
            // Eliminar imagen del servidor si quisiéramos, aunque el soft delete es mejor
            // if(strpos($banner->image_path, base_url()) !== false) { ... unlink ... }
            $bannerModel->delete($id);
            return redirect()->to(base_url('admin/banners'))->with('success', 'Banner eliminado.');
        }
        
        return redirect()->to(base_url('admin/banners'))->with('error', 'Banner no encontrado.');
    }
}
