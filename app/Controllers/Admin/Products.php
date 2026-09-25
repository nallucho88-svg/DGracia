<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;

class Products extends BaseController
{
    public function index()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        
        $products = $productModel->orderBy('created_at', 'DESC')->findAll();
        $categories = $categoryModel->findAll();

        $data = [
            'title' => 'Gestión de Productos | Admin',
            'products' => $products,
            'categories' => $categories
        ];

        return view('admin/products/index', $data);
    }

    public function create()
    {
        $categoryModel = new CategoryModel();

        if ($this->request->getMethod() === 'POST') {
            $productModel = new ProductModel();
            
            $name = $this->request->getPost('name');
            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'name' => $name,
                'slug' => strtolower(url_title($name)) . '-' . rand(100, 999),
                'short_description' => $this->request->getPost('short_description'),
                'description' => $this->request->getPost('description'),
                'price' => $this->request->getPost('price'),
                'ingredients' => $this->request->getPost('ingredients'),
                'portions' => $this->request->getPost('portions'),
                'availability' => $this->request->getPost('availability') ?? 1,
                'status' => $this->request->getPost('status') ?? 1
            ];

            $productId = $productModel->insert($data);

            // Manejo de la imagen
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/products', $newName);
                
                $imageModel = new ProductImageModel();
                $imageModel->insert([
                    'product_id' => $productId,
                    'image_path' => 'uploads/products/' . $newName,
                    'is_main' => 1
                ]);
            }

            return redirect()->to(base_url('admin/productos'))->with('success', 'Producto creado exitosamente.');
        }

        $data = [
            'title' => 'Nuevo Producto | Admin',
            'categories' => $categoryModel->where('status', 1)->findAll()
        ];
        return view('admin/products/form', $data);
    }

    public function edit($id)
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $imageModel = new ProductImageModel();

        $product = $productModel->find($id);
        
        if (!$product) {
            return redirect()->to(base_url('admin/productos'))->with('error', 'Producto no encontrado.');
        }

        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $data = [
                'category_id' => $this->request->getPost('category_id'),
                'name' => $name,
                //'slug' => strtolower(url_title($name)), // No actualizamos el slug para no romper links
                'short_description' => $this->request->getPost('short_description'),
                'description' => $this->request->getPost('description'),
                'price' => $this->request->getPost('price'),
                'ingredients' => $this->request->getPost('ingredients'),
                'portions' => $this->request->getPost('portions'),
                'availability' => $this->request->getPost('availability') ?? 1,
                'status' => $this->request->getPost('status') ?? 1
            ];

            $productModel->update($id, $data);

            // Manejo de la nueva imagen (reemplazar la principal por ahora)
            $file = $this->request->getFile('image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/products', $newName);
                
                // Borramos las imágenes viejas
                $imageModel->where('product_id', $id)->delete();
                
                // Insertamos la nueva
                $imageModel->insert([
                    'product_id' => $id,
                    'image_path' => 'uploads/products/' . $newName,
                    'is_main' => 1
                ]);
            }

            return redirect()->to(base_url('admin/productos'))->with('success', 'Producto actualizado exitosamente.');
        }

        $data = [
            'title' => 'Editar Producto | Admin',
            'product' => $product,
            'categories' => $categoryModel->where('status', 1)->findAll()
        ];
        return view('admin/products/form', $data);
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        $productModel->delete($id); // Soft delete
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Producto eliminado.']);
        }
        
        return redirect()->to(base_url('admin/productos'))->with('success', 'Producto eliminado.');
    }
}
