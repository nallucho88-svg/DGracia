<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;

class Categories extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $isPost = strtoupper($this->request->getMethod()) === 'POST';

        // Manejar Crear/Editar por POST (AJAX o Form normal)
        if ($isPost) {
            $id = $this->request->getPost('id');
            $name = trim($this->request->getPost('name') ?? '');
            
            if (empty($name)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'El nombre es obligatorio.']);
                }
                return redirect()->back()->with('error', 'El nombre es obligatorio.');
            }

            $data = [
                'name'   => $name,
                'slug'   => strtolower(url_title($name, '-', true)),
                'status' => (int) ($this->request->getPost('status') ?? 1)
            ];

            if ($id) {
                $categoryModel->update($id, $data);
                $msg = 'Categoría actualizada exitosamente.';
            } else {
                $categoryModel->insert($data);
                $msg = 'Categoría creada exitosamente.';
            }

            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
            }
            return redirect()->to(base_url('admin/categorias'))->with('success', $msg);
        }

        // Obtener un registro vía AJAX
        if ($this->request->isAJAX() && $this->request->getGet('id')) {
            $category = $categoryModel->find($this->request->getGet('id'));
            return $this->response->setJSON($category ?: []);
        }

        // Listado normal
        $categories = $categoryModel->findAll();

        $data = [
            'title'      => 'Gestión de Categorías | Admin',
            'categories' => $categories
        ];

        return view('admin/categories/index', $data);
    }

    public function delete($id)
    {
        $categoryModel = new CategoryModel();
        $category = $categoryModel->find($id);

        if ($category) {
            $categoryModel->delete($id);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Categoría eliminada.']);
            }
            return redirect()->to(base_url('admin/categorias'))->with('success', 'Categoría eliminada.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Categoría no encontrada.']);
        }
        return redirect()->to(base_url('admin/categorias'))->with('error', 'Categoría no encontrada.');
    }
}
