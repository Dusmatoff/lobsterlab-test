<?php

class ProductController {
    private $productModel;

    public function __construct(ProductModel $productModel) {
        $this->productModel = $productModel;
    }

    public function importProducts($fileName) {
        return $this->productModel->importProductsFromCSV($fileName);
    }
}
