<?php
namespace App\Module\Front\Presenters;

use Nette;
use Nette\Application\UI\Form;
use app\model\PostFacade;
	
final class PostPresenter extends Nette\Application\UI\Presenter
{
 
	private PostFacade $postFacade;

	public function __construct(PostFacade $postFacade)
	{
		$this->postFacade = $postFacade;
	}

	public function renderShow(int $postId)
	{
	
		$post = $this->postFacade->getPostById($postId);

	
		if (!$post) {
			$this->error('Stránka nebyla nalezena');
		}
	
		$this->template->post = $post;
		$this->template->comments = $this->postFacade->getComments($postId);

	}


	protected function createComponentCommentForm(): Form
	{
		$form = new Form; // means Nette\Application\UI\Form
	
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
	
		$this->postFacade->addComment($postId,$data);
		$this->flashMessage('Děkuji za komentář', 'success');
		$this->redirect('this');
	}
	
	
}