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
    public function find( $id ) {
        return $this->model->find( $id );
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
        $product = $this->model->find( $id );
        $product->update( [
            'title'       => $data['title'],
            'slug'        => Str::slug( $data['title'] ),
            'sku'         => $data['sku'],
            'category_id' => $data['category_id'],
        ] );
        return $product;
    }

    public function detailsById( $product_id, array $selected_fields = ['*'], array $relations = [] ) {
        return $this->model->select( $selected_fields )
            ->where( 'id', $product_id )
            ->with( $relations )
            ->first();
    }

    public function destroy( $id ) {
        return $this->model->destroy( $id );
    }
}