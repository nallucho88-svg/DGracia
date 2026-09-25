<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de categorías
        $tortasId = $this->db->table('categories')->where('slug', 'tortas')->get()->getRow()->id ?? 1;
        $galletasId = $this->db->table('categories')->where('slug', 'galletas')->get()->getRow()->id ?? 2;
        $postresId = $this->db->table('categories')->where('slug', 'postres')->get()->getRow()->id ?? 3;

        $products = [
            [
                'category_id' => $tortasId,
                'name' => 'Torta de Chocolate Supremo',
                'slug' => 'torta-de-chocolate-supremo',
                'short_description' => 'Deliciosa torta de chocolate con triple relleno.',
                'description' => 'Nuestra Torta de Chocolate Supremo es la elección perfecta para los amantes del cacao. Elaborada con cacao al 70%, cuenta con un triple relleno de ganache y cubierta de crema de chocolate.',
                'price' => 85000.00,
                'ingredients' => 'Chocolate 70%, harina, huevos, leche, crema de leche, mantequilla.',
                'portions' => '15 personas',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $tortasId,
                'name' => 'Torta Red Velvet',
                'slug' => 'torta-red-velvet',
                'short_description' => 'Clásica Red Velvet con frosting de queso crema.',
                'description' => 'El balance perfecto entre el sabor a cacao suave y la acidez del frosting de queso crema. Una torta elegante y deliciosa.',
                'price' => 90000.00,
                'ingredients' => 'Harina, cacao, buttermilk, colorante natural, queso crema, mantequilla, azúcar.',
                'portions' => '12 personas',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $galletasId,
                'name' => 'Galletas Choco-Chips (Caja x6)',
                'slug' => 'galletas-choco-chips',
                'short_description' => 'Galletas crujientes por fuera y suaves por dentro.',
                'description' => 'Caja con 6 galletas grandes estilo neoyorquino, repletas de chips de chocolate semiamargo y un toque de sal marina.',
                'price' => 25000.00,
                'ingredients' => 'Harina, chips de chocolate semiamargo, mantequilla, azúcar morena, huevos, sal marina.',
                'portions' => '6 porciones',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $postresId,
                'name' => 'Cheesecake de Frutos Rojos',
                'slug' => 'cheesecake-de-frutos-rojos',
                'short_description' => 'Cheesecake cremoso con mermelada artesanal.',
                'description' => 'Cremoso cheesecake horneado al estilo New York, cubierto con nuestra mermelada artesanal de fresas, moras y arándanos.',
                'price' => 15000.00,
                'ingredients' => 'Queso crema, galleta de vainilla, mantequilla, huevos, fresas, moras, arándanos.',
                'portions' => '1 persona',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $postresId,
                'name' => 'Tiramisú Clásico',
                'slug' => 'tiramisu-clasico',
                'short_description' => 'Postre italiano con café y mascarpone.',
                'description' => 'Auténtico postre italiano con bizcotelas bañadas en café espresso y licor de café, intercaladas con suave crema mascarpone.',
                'price' => 18000.00,
                'ingredients' => 'Queso mascarpone, café espresso, bizcotelas, cacao en polvo, huevos, azúcar.',
                'portions' => '1 persona',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $tortasId,
                'name' => 'Torta de Zanahoria',
                'slug' => 'torta-de-zanahoria',
                'short_description' => 'Con nueces y cubierta de queso crema.',
                'description' => 'Húmeda torta de zanahoria con especias, nueces troceadas y una generosa capa de frosting de queso crema.',
                'price' => 80000.00,
                'ingredients' => 'Zanahoria fresca, nueces, canela, harina, huevos, aceite vegetal, queso crema.',
                'portions' => '10 personas',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'category_id' => $galletasId,
                'name' => 'Galletas de Avena y Pasas',
                'slug' => 'galletas-de-avena-y-pasas',
                'short_description' => 'Galletas saludables y deliciosas (Caja x6).',
                'description' => 'Nuestras galletas de avena son suaves, masticables y están llenas de pasas jugosas. Perfectas para un antojo más saludable.',
                'price' => 22000.00,
                'ingredients' => 'Avena en hojuelas, pasas, canela, harina, mantequilla, huevos.',
                'portions' => '6 porciones',
                'availability' => 1,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Insertar productos
        foreach ($products as $p) {
            $this->db->table('products')->insert($p);
            $productId = $this->db->insertID();

            // Insertar imagen de prueba para cada producto
            $images = [
                'torta-de-chocolate-supremo' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?q=80&w=800&auto=format&fit=crop',
                'torta-red-velvet' => 'https://images.unsplash.com/photo-1616541823729-00fe0aacd32c?q=80&w=800&auto=format&fit=crop',
                'galletas-choco-chips' => 'https://images.unsplash.com/photo-1499636136210-6f4ee915583e?q=80&w=800&auto=format&fit=crop',
                'cheesecake-de-frutos-rojos' => 'https://images.unsplash.com/photo-1533134242443-d4fd215305ad?q=80&w=800&auto=format&fit=crop',
                'tiramisu-clasico' => 'https://images.unsplash.com/photo-1571115177098-24ec42ed204d?q=80&w=800&auto=format&fit=crop',
                'torta-de-zanahoria' => 'https://images.unsplash.com/photo-1587314168485-3236d6710814?q=80&w=800&auto=format&fit=crop',
                'galletas-de-avena-y-pasas' => 'https://images.unsplash.com/photo-1605807646983-377bc5a76493?q=80&w=800&auto=format&fit=crop',
            ];

            $this->db->table('product_images')->insert([
                'product_id' => $productId,
                'image_path' => $images[$p['slug']] ?? 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?q=80&w=800&auto=format&fit=crop',
                'is_main' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
