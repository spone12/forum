<?php
namespace App\Contracts;

/**
 * Interface CrudRepositoryInterface
 * @package App\Contracts
 */
interface CrudRepositoryInterface
{

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     * @return mixed
     */
    public function update(array $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);
}
