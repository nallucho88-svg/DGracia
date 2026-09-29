<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Libraries\DiscountService;

class Shop extends BaseController
{
    protected $discountService;

    public function __construct()
    {
        $this->discountService = new DiscountService();
    }

    public function index()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $imageModel = new ProductImageModel();

        $categories = $categoryModel->where('status', 1)->orderBy('name', 'ASC')->findAll();
        $products = $productModel->where('status', 1)->orderBy('category_id', 'ASC')->orderBy('name', 'ASC')->findAll();

        $isLoyal = session()->get('is_loyal') ?? false;

        foreach ($products as &$product) {
            $image = $imageModel->where('product_id', $product->id)
                                ->where('is_main', 1)
                                ->first();
            $product->main_image = media_url($image ? $image->image_path : '', 'assets/img/placeholder.jpg');
            
            $cat = array_filter($categories, fn($c) => $c->id == $product->category_id);
            $product->category_name = !empty($cat) ? reset($cat)->name : 'General';

            $product->discount_info = $this->discountService->calculateProductDiscount($product, $isLoyal);
        }

        $userFavorites = [];
        if (session()->get('is_logged_in')) {
            $favoriteModel = new \App\Models\FavoriteModel();
            $userFavorites = array_column($favoriteModel->where('user_id', session()->get('user_id'))->findAll(), 'product_id');
            $userFavorites = array_map('intval', $userFavorites);
        }

        $data = [
            'title'         => 'Catálogo | Dulce Gracia',
            'categories'    => $categories,
            'products'      => $products,
            'userFavorites' => $userFavorites
        ];

        return view('shop/index', $data);
    }

    public function category($slug)
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $imageModel = new ProductImageModel();

        $category = $categoryModel->where('slug', $slug)->where('status', 1)->first();

        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $categories = $categoryModel->where('status', 1)->orderBy('name', 'ASC')->findAll();
        $products = $productModel->where('category_id', $category->id)->where('status', 1)->orderBy('name', 'ASC')->findAll();

        $isLoyal = session()->get('is_loyal') ?? false;

        foreach ($products as &$product) {
            $image = $imageModel->where('product_id', $product->id)
                                ->where('is_main', 1)
                                ->first();
            $product->main_image = media_url($image ? $image->image_path : '', 'assets/img/placeholder.jpg');
            $product->category_name = $category->name;
            $product->discount_info = $this->discountService->calculateProductDiscount($product, $isLoyal);
        }

        $userFavorites = [];
        if (session()->get('is_logged_in')) {
            $favoriteModel = new \App\Models\FavoriteModel();
            $userFavorites = array_column($favoriteModel->where('user_id', session()->get('user_id'))->findAll(), 'product_id');
            $userFavorites = array_map('intval', $userFavorites);
        }

        $data = [
            'title'            => $category->name . ' | Dulce Gracia',
            'categories'       => $categories,
            'products'         => $products,
            'current_category' => $category,
            'userFavorites'    => $userFavorites
        ];

        return view('shop/index', $data);
    }

    public function product($slug)
    {
        $productModel  = new ProductModel();
        $imageModel    = new ProductImageModel();
        $categoryModel = new CategoryModel();

        $product = is_numeric($slug)
            ? $productModel->where('id', (int)$slug)->orWhere('slug', $slug)->first()
            : $productModel->where('slug', $slug)->first();

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $images = $imageModel->where('product_id', $product->id)->findAll();
        $category = $categoryModel->find($product->category_id);

        $isLoyal = session()->get('is_loyal') ?? false;
        $product->discount_info = $this->discountService->calculateProductDiscount($product, $isLoyal);

        $isFavorite = false;
        if (session()->get('is_logged_in')) {
            $favoriteModel = new \App\Models\FavoriteModel();
            $isFav = $favoriteModel->where('user_id', session()->get('user_id'))
                                   ->where('product_id', $product->id)
                                   ->first();
            $isFavorite = (bool) $isFav;
        }

        $ratingModel = new \App\Models\RatingModel();
        $ratingSummary = $ratingModel->getProductRatingSummary($product->id);

        $db = \Config\Database::connect();
        $reviews = $db->table('ratings r')
            ->select('r.*, u.name as customer_name')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->where('r.product_id', $product->id)
            ->where('r.status', 'visible')
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultArray();

        $data = [
            'title'         => $product->name . ' | Dulce Gracia',
            'product'       => $product,
            'images'        => $images,
            'category'      => $category,
            'isFavorite'    => $isFavorite,
            'ratingSummary' => $ratingSummary,
            'reviews'       => $reviews
        ];

        return view('shop/detail', $data);
    }
}
