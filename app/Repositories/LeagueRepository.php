<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;

class LeagueRepository implements LeagueRepositoryInterface
{

     // model property on class instances
    protected $model;

    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    } 

    /**
     * Get's a post by it's ID
     *
     * @param int
     * @return collection
     */
    public function get($post_id)
    {
        return $this->model->find($post_id);
    }

    /**
     * Get's all posts.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Deletes a post.
     *
     * @param int
     */
    public function delete($post_id)
    {
    }

    /**
     * Updates a post.
     *
     * @param int
     * @param array
     */

    public function update($post_id, array $post_data)
    {
        return $this->model->find($post_id)->update($post_data);
    }
    public function updatedata(array $post_id, array $post_data)
    {
        return $this->model->where($post_id)->update($post_data);
    }

    /**
     * Updates a post.
     *
     * @param int
     * @param array
     */

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    //  public function with($relations)
    // {
    //     return $this->model->with($relations);
    // }
    public function with($relations=array())
    {
        return $this->model->with($relations);
    }

    public function where($sport_id)
    {
        return $this->model->where('sport_id',$sport_id);
    }

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginate($limit,$pageNumber)
    {
        return $this->model->paginate($limit, ['*'], 'page', $pageNumber);
    }

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginateWith($limit,$pageNumber,$relations=array())
    {
        return $this->model->with($relations)->paginate($limit, ['*'], 'page', $pageNumber);
    }
        public function paginateWithLeagues($limit,$pageNumber,$relations=array())
    {
        return $this->model->where('process_status','!=','complete')->with($relations)->paginate($limit, ['*'], 'page', $pageNumber);
    }
    public function paginateWithArray($limit,$pageNumber,$relations)
    {
        print_r($limit,$pageNumber,$relations); die;
        return $this->model->with($relations)->paginate($limit, ['*'], 'page', $pageNumber);
    }

    public function update_status($id, array $post_data)
    {
        return $this->model->where('id',$id)->update($post_data);
    }

    public function GetActiveLeagues($post_data,$relations=array()){
        return $this->model->with($relations)->where($post_data)->get();
    }

    public function MakeLeagueAdmin($league_id,$user_id,$post_data){
            // echo $league_id.$user_id;
        return $this->model->where([['league_id',$league_id],['user_id',$user_id]])->update($post_data);
    }

}
?>
