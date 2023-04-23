<?php
namespace App\Model;

use Nette;

final class PostFacade	
{
	public function __construct(
		private Nette\Database\Explorer $database,
	) {
	}

	public function getPublicArticles()
	{
		return $this->database
			->table('posts')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}  

	public function getPostById(int $postId)
	{
		return $this->database
			->table('posts')
			->get($postId);
	}

	public function getComments(int $postId): Nette\Database\Table\Selection
	{
		return $this->database
			->table('comments')
			->where('post_id', $postId)
			->order('created_at');
	}

	public function addComment(int $postId, \stdClass $data): void
	{
		$this->database
			->table('comments')
			->insert([
				'post_id' => $postId,
				'name' => $data->name,
				'email' => $data->email,
				'content' => $data->content,
				'created_at' => new \DateTime,
			]);
	}

	public function addPost($data)
	{

		return $this->database
			->table('posts')
			->insert([
				'title' => $data->title,
				'content' => $data->content,
				'img' => $data->img,
				'created_at' => new \DateTime,
			]);
			
	}

	public function editPost(int $postId,$data)
	{
		$post = $this->database
			->table('posts')
			->where('id', $postId)
			->update([
				'title' => $data->title,
				'content' => $data->content,
				'img' => $data->img,
			]);
		return $post;
	}

	public function findPublishedPosts(): Nette\Database\Table\Selection
    {
        return
            $this->database->table('posts')
            ->where('created_at < ', new \DateTime)
            ->order('created_at DESC');
    }

}
		