<?php

namespace App\Services;

use App\Models\GoldenProduct;
use App\Models\SellerProduct;

class GoldenProductService
{
    public function createFromSellerProduct(SellerProduct $sellerProduct): GoldenProduct
    {
        if ($sellerProduct->golden_product_id) {
            return $sellerProduct->goldenProduct;
        }

        $goldenProduct = GoldenProduct::create([
            'product_type_id' => $sellerProduct->category->product_type_id ?? 1,
        ]);
        
        // Create localized version
        $goldenProduct->translations()->create([
            'name' => $sellerProduct->name,
            'description' => $sellerProduct->description,
            'locale' => 'en',
        ]);
        
        $sellerProduct->update(['golden_product_id' => $goldenProduct->id]);

        return $goldenProduct;
    }
}
