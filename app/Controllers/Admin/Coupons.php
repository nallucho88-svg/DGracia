<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CouponModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class Coupons extends BaseController
{
    public function index()
    {
        $couponModel = new CouponModel();
        $coupons = $couponModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title'   => 'Gestión de Cupones | Admin',
            'coupons' => $coupons
        ];

        return view('admin/coupons/index', $data);
    }

    public function create()
    {
        $couponModel   = new CouponModel();
        $categoryModel = new CategoryModel();
        $productModel  = new ProductModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'          => 'required|min_length[3]|max_length[150]',
                'code'          => 'required|alpha_numeric_space|min_length[3]|max_length[50]|is_unique[coupons.code]',
                'discount_type' => 'required|in_list[percentage,fixed]',
                'value'         => 'required|numeric'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $couponData = [
                'name'              => $this->request->getPost('name'),
                'description'       => $this->request->getPost('description'),
                'code'              => strtoupper(trim($this->request->getPost('code'))),
                'discount_type'     => $this->request->getPost('discount_type'),
                'value'             => $this->request->getPost('value'),
                'start_date'        => $this->request->getPost('start_date') ?: null,
                'end_date'          => $this->request->getPost('end_date') ?: null,
                'max_uses'          => $this->request->getPost('max_uses') ?? 0,
                'max_uses_per_user' => $this->request->getPost('max_uses_per_user') ?? 1,
                'category_id'       => $this->request->getPost('category_id') ?: null,
                'product_id'        => $this->request->getPost('product_id') ?: null,
                'loyal_only'        => $this->request->getPost('loyal_only') ? 1 : 0,
                'is_stackable'      => $this->request->getPost('is_stackable') ? 1 : 0,
                'status'            => $this->request->getPost('status') ?? 1,
            ];

            $couponModel->insert($couponData);
            return redirect()->to(base_url('admin/cupones'))->with('success', 'Cupón creado exitosamente.');
        }

        $data = [
            'title'      => 'Nuevo Cupón | Admin',
            'categories' => $categoryModel->where('status', 1)->findAll(),
            'products'   => $productModel->where('status', 1)->findAll(),
        ];

        return view('admin/coupons/form', $data);
    }

    public function edit($id)
    {
        $couponModel   = new CouponModel();
        $categoryModel = new CategoryModel();
        $productModel  = new ProductModel();

        $coupon = $couponModel->find($id);
        if (!$coupon) {
            return redirect()->to(base_url('admin/cupones'))->with('error', 'Cupón no encontrado.');
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'          => 'required|min_length[3]|max_length[150]',
                'code'          => "required|alpha_numeric_space|min_length[3]|max_length[50]|is_unique[coupons.code,id,{$id}]",
                'discount_type' => 'required|in_list[percentage,fixed]',
                'value'         => 'required|numeric'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $couponData = [
                'name'              => $this->request->getPost('name'),
                'description'       => $this->request->getPost('description'),
                'code'              => strtoupper(trim($this->request->getPost('code'))),
                'discount_type'     => $this->request->getPost('discount_type'),
                'value'             => $this->request->getPost('value'),
                'start_date'        => $this->request->getPost('start_date') ?: null,
                'end_date'          => $this->request->getPost('end_date') ?: null,
                'max_uses'          => $this->request->getPost('max_uses') ?? 0,
                'max_uses_per_user' => $this->request->getPost('max_uses_per_user') ?? 1,
                'category_id'       => $this->request->getPost('category_id') ?: null,
                'product_id'        => $this->request->getPost('product_id') ?: null,
                'loyal_only'        => $this->request->getPost('loyal_only') ? 1 : 0,
                'is_stackable'      => $this->request->getPost('is_stackable') ? 1 : 0,
                'status'            => $this->request->getPost('status') ?? 1,
            ];

            $couponModel->update($id, $couponData);
            return redirect()->to(base_url('admin/cupones'))->with('success', 'Cupón actualizado exitosamente.');
        }

        $data = [
            'title'      => 'Editar Cupón | Admin',
            'coupon'     => $coupon,
            'categories' => $categoryModel->where('status', 1)->findAll(),
            'products'   => $productModel->where('status', 1)->findAll(),
        ];

        return view('admin/coupons/form', $data);
    }

    public function delete($id)
    {
        $couponModel = new CouponModel();
        $couponModel->delete($id);
        return redirect()->to(base_url('admin/cupones'))->with('success', 'Cupón eliminado exitosamente.');
    }
}
