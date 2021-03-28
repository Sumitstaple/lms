<?php
namespace App\Repositories;

interface LeagueRepositoryInterface
{
    /**
     * Get's a post by it's ID
     *
     * @param int
     */
    public function get($id);

    /**
     * Get's all posts.
     *
     * @return mixed
     */
    public function all();

    /**
     * Deletes a post.
     *
     * @param int
     */
    public function delete($id);

    /**
     * Updates a post.
     *
     * @param int
     * @param array
     */
    public function update($id, array $data);

    /**
     * Array a post.
     *
     * @param int
     * @param array
     */
     public function create(array $data);

     /**
     * Array a post.
     *
     * @param int
     * @param array
     */
     public function paginate($limit,$pageNumber);

      /**
     * Array a post.
     *
     * @param int
     * @param array
     */
     public function paginateWith($limit,$pageNumber,$relations);
}
?>
