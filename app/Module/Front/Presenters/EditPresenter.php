<?php
namespace App\Module\Front\Presenters;

use Nette;
use Nette\Application\UI\Form;
use app\model\PostFacade;


final class EditPresenter extends Nette\Application\UI\Presenter
{
	private PostFacade $postFacade;

	public function __construct(PostFacade $postFacade)
	{
		$this->postFacade = $postFacade;
	}

    protected function createComponentPostForm(): Form
{
	$form = new Form;
	$form->addText('title', 'Titulek:')
		->setRequired();
	$form->addTextArea('content', 'Obsah:')
		->setRequired();
	$form->addUpload('img', 'Soubor:')
		->addRule(Form::IMAGE, 'Thumbnail must be JPEG, PNG or GIF');
	$form->addSubmit('send', 'Uložit a publikovat');
	$form->onSuccess[] = [$this, 'postFormSucceeded'];
	return $form;
}

public function postFormSucceeded($form, $data): void
{
	$postId = $this->getParameter('postId');

	if ($data->img->isOK()){
        $data->img->move('upload/' . $data->img->getSanitizedName());
        $data['img'] = ('upload/' . $data->img->getSanitizedName());
    }else{
        unset($data->image);
        $this->flashMessage('Sobor nebyl přidán', 'failed');
    }

   if($postId) {
	$post = $this->postFacade->editPost($postId,$data);

   } else {
	bdump('else');
	$post = $this->postFacade->addPost($data);
}
	$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
	$this->redirect('Post:show', $postId);


}
public function renderEdit(int $postId): void
{
	$post = $this->postFacade->getPostById($postId);

	if (!$post) {
		$this->error('Post not found');
	}

	$this->getComponent('postForm')
		 ->setDefaults($post->toArray());
}




public function startup(): void
{
	parent::startup();

	if (!$this->getUser()->isLoggedIn()) {
		$this->redirect('Sign:in');
	}
}



}