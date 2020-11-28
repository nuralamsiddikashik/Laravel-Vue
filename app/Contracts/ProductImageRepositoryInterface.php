<?php

namespace App\Contracts;

interface ProductImageRepositoryInterface {
    /**
     * @param bool $paginate
     * @return mixed
     */
    public function get();

    /**
     * @param $id
     * @return mixed
     */
    public function find( $id );

    /**
     * @param array $data
     * @return mixed
     */
    public function store( array $data );

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update( $id, array $data );
}