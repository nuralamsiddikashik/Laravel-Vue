<?php
/**
 * @author Author Name: Md Morshadun Nur
 * @email  Email: morshadunnur@gmail.com
 */
namespace App\Repositories;
use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface {

    private $model;

    public function __construct( Product $model ) {
        $this->model = $model;
    }

    public function get( array $selected_fields = ['*'], array $relations = [] ) {
        $products = $this->model
            ->select( $selected_fields )
            ->with( $relations );

        return $products->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find() {

    }

    /**
     * @param array $data
     * @return mixed
     */
    public function store( array $data ) {
        return $this->model->create( [
            'title'         => $data['title'],
            'slug'          => Str::slug( $data['title'] ),
            'description'   => $data['description'],
            'sku'           => $data['sku'],
            'category_id'   => $data['category_id'],
            'feature_image' => $data['image']['file_name'],
            'status'        => $data['status'],
            'published_by'  => $data['published_by'],
        ] );
    }

    /**
     * @param $id
     * @return mixed
     */
    public function update( $id, array $data ) {

    }

}