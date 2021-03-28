<?php
namespace App\Repositories;

interface ResultRepositoryInterface
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
     public function paginateWithSearch($limit,$pageNumber,$relations,$str);

     /**
     * Get's first posts.
     *
     * @return mixed
     */
    public function first();
    public function getallRounds();
    public function getfixture($id, $s_id);
    public function settoAbondon($id);
    public function saveResult(array $data);
    public function getSportId( $id );
    public function getLeagueList( $sportID, $roundID);
    public function getLeagueUser( $league );
    public function filterWinner($status, $key);
    public function markWinner(array $data, $round_id, $sportID);
    public function addUnduRecord(array $leagueUser, array $fixture, $round_id, $sportID, $str); 
    public function getUndoRecord( $id );
    public function undoResultUpdate( $id );
    public function checkRoundUnduable($id);
    public function getPreRoundStats(array $user);
    
}
?>
