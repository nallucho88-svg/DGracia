<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DiscountModel;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class Discounts extends BaseController
{
    public function index()
    {
        $discountModel = new DiscountModel();
        $discounts = $discountModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'     => 'Gestión de Descuentos | Admin',
            'discounts' => $discounts
        ];

        return view('admin/discounts/index', $data);
    }

    public function create()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $discountModel = new DiscountModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title'         => 'required|min_length[3]|max_length[150]',
                'type'          => 'required|in_list[product,category,loyalty,general]',
                'discount_type' => 'required|in_list[percentage,fixed]',
                'value'         => 'required|numeric'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $discountData = [
                'title'         => $this->request->getPost('title'),
                'type'          => $this->request->getPost('type'),
                'discount_type' => $this->request->getPost('discount_type'),
                'value'         => $this->request->getPost('value'),
                'product_id'    => $this->request->getPost('product_id') ?: null,
                'category_id'   => $this->request->getPost('category_id') ?: null,
                'start_date'    => $this->request->getPost('start_date') ?: null,
                'end_date'      => $this->request->getPost('end_date') ?: null,
                'status'        => $this->request->getPost('status') ?? 1,
            ];

            $discountModel->insert($discountData);
            return redirect()->to(base_url('admin/descuentos'))->with('success', 'Oferta creada exitosamente.');
        }

        $data = [
            'title'      => 'Nueva Oferta | Admin',
            'products'   => $productModel->where('status', 1)->findAll(),
            'categories' => $categoryModel->where('status', 1)->findAll(),
        ];

        return view('admin/discounts/form', $data);
    }

    public function edit($id)
    {
        $discountModel = new DiscountModel();
        $productModel  = new ProductModel();
        $categoryModel = new CategoryModel();

        $discount = $discountModel->find($id);
        if (!$discount) {
            return redirect()->to(base_url('admin/descuentos'))->with('error', 'Oferta no encontrada.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'title'         => 'required|min_length[3]|max_length[150]',
                'type'          => 'required|in_list[product,category,loyalty,general]',
                'discount_type' => 'required|in_list[percentage,fixed]',
                'value'         => 'required|numeric'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $discountData = [
                'title'         => $this->request->getPost('title'),
                'type'          => $this->request->getPost('type'),
                'discount_type' => $this->request->getPost('discount_type'),
                'value'         => $this->request->getPost('value'),
                'product_id'    => $this->request->getPost('product_id') ?: null,
                'category_id'   => $this->request->getPost('category_id') ?: null,
                'start_date'    => $this->request->getPost('start_date') ?: null,
                'end_date'      => $this->request->getPost('end_date') ?: null,
                'status'        => $this->request->getPost('status') ?? 1,
            ];

            $discountModel->update($id, $discountData);
            return redirect()->to(base_url('admin/descuentos'))->with('success', 'Oferta actualizada exitosamente.');
        }

        $data = [
            'title'      => 'Editar Oferta | Admin',
            'discount'   => $discount,
            'products'   => $productModel->where('status', 1)->findAll(),
            'categories' => $categoryModel->where('status', 1)->findAll(),
        ];

        return view('admin/discounts/form', $data);
    }

    public function delete($id)
    {
        $discountModel = new DiscountModel();
        $discountModel->delete($id);
        return redirect()->to(base_url('admin/descuentos'))->with('success', 'Oferta eliminada exitosamente.');
    }
}
