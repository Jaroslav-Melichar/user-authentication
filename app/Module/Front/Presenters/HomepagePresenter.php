<?php
namespace App\Module\Front\Presenters;

use App\Model\PostFacade;
use Nette;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {
	}

	public function renderdefault(int $page = 1): void
    {
        $posts = $this->facade->findPublishedPosts();
        $lastPage = 0;
        $this->template->posts = $posts->page($page, 4, $lastPage);

        $this->template->lastPage = $lastPage;
        $this->template->page = $page;
    }

	public function renderShow(int $postId): void
	{
		$post = $this->facade->getPostById($postId);

		if (!$post) {
			$this->error('Stránka nebyla nalezena');
		}

		$this->template->post = $post;
		$this->template->comments = $this->facade->getComments($postId);
	}

	protected function createComponentCommentForm(): Nette\Application\UI\Form
	{
		$form = new Nette\Application\UI\Form; // means Nette\Application\UI\Form

		$form->addText('name', 'Jméno:')
			->setRequired();

		$form->addEmail('email', 'E-mail:');

		$form->addTextArea('content', 'Komentář:')
			->setRequired();

		$form->addSubmit('send', 'Publikovat komentář');
		$form->onSuccess[] = [$this, 'commentFormSucceeded'];
		return $form;
	}

	public function commentFormSucceeded(\stdClass $data): void
	{
		$postId = $this->getParameter('postId');

		$this->facade->addComment($postId, $data);
		$this->flashMessage('Děkuji za komentář', 'success');
		$this->redirect('this');
	}

	public function save(User $user): User
    {
        $data = [
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'role' => $user->getRole(),
        ];

        $this->database->table('users')->insert($data);
        $user->setId($this->database->getInsertId());

        return $user;
    }
}