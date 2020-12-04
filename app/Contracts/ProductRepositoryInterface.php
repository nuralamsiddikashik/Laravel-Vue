<?php
/**
 * @author Author Name: Md Morshadun Nur
 * @email  Email: morshadunnur@gmail.com
 */

namespace App\Contracts;

interface ProductRepositoryInterface {
    /**
     * @return mixed
     */
    public function get( array $selected_fields, array $relations );

    /**
     * @param $id
     * @return mixed
     */
    public function find();

    /**
     * @param array $data
     * @return mixed
     */
    public function store( array $data );

    /**
     * @param $id
     * @return mixed
     */
    public function update( $id, array $data );

}