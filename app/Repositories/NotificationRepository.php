<?php
namespace App\Repositories;
use Illuminate\Database\Eloquent\Model;
use Auth;

class NotificationRepository implements NotificationRepositoryInterface
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
    public function getOrderBy() {
    	return $this->model->where('user_id',Auth::id())->orderBy('created_at','DESC')->get();
    }
    public function unReadCount() {
    	return $this->model->where('user_id',Auth::id())->where('is_read','no')->count();
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
        
    }

    public function where($post_data)
    {
        return $this->model->where($post_data)->get();
    }

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginate($limit,$pageNumber)
    {
        
    }

    /**
     * Get's pagination.
     *
     * @return mixed
     */
    public function paginateWith($limit,$pageNumber,$relations=array())
    {
        
    }

    public function StringReplace($strKeywords,$strReplace,$string){

        return str_replace($strKeywords, $strReplace,$string);
    }

    public function getTemplateBySlug($slug){
        
        return $this->model->where('slug',$slug)->first();
       
    }

    public function SendEmail(){
        
        $slug = "league_creation";        
        $mailTemplate = $obj->Template($strKeywords,$strReplace,$slug);
    }

    // public function is_read($is_read)
    // {
    //     return $this->model->where('is_read',$is_read);
    // }
    public function update_mark_as_readAll($user_id,$post_data)
    {
        return $this->model->where('user_id',$user_id)->where('is_read','no')->update($post_data);
    }

}
?>
