<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Forms;
use Nette;
use Nette\Application\UI\Form;


final class SignPresenter extends Nette\Application\UI\Presenter
{
	/** @persistent */
	public string $backlink = '';

	private Forms\SignInFormFactory $signInFactory;

	private Forms\SignUpFormFactory $signUpFactory;


	public function __construct(Forms\SignInFormFactory $signInFactory, Forms\SignUpFormFactory $signUpFactory)
	{
		$this->signInFactory = $signInFactory;
		$this->signUpFactory = $signUpFactory;
	}


	/**
	 * Sign-in form factory.
	 */
	protected function createComponentSignInForm(): Form
	{
		return $this->signInFactory->create(function (): void {
			$this->restoreRequest($this->backlink);
			$this->redirect('Homepage:');
		});
	}


	/**
	 * Sign-up form factory.
	 */
	protected function createComponentSignUpForm(): Form
	{
		return $this->signUpFactory->create(function (): void {
			$this->redirect('Homepage:');
		});
	}


	public function actionOut(): void
	{
		$this->getUser()->logout();
		$this->flashMessage('Byl jste odhlášen.');
		$this->redirect('Homepage:');
	}
}
