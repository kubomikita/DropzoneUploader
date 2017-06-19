<?php

/**
 * This file is part of the AlesWita\DropzoneUploader
 * Copyright (c) 2017 Ales Wita (aleswita+github@gmail.com)
 */

declare(strict_types=1);

namespace AlesWita\DropzoneUploader;

use AlesWita;
use Nette;


/**
 * @author Ales Wita
 * @license MIT
 */
class DropzoneUploader extends Nette\Application\UI\Control
{
	/** @var Nette\Localization\ITranslator */
	private $translator;

	/** @var string */
	private $dropzoneTemplate;

	/** @var AlesWita\DropzoneUploader\UploadDriver\IUploadRiver */
	private $uploadDriver;

	/** @var array */
	private $settings;

	/** @var array */
	private $messages;

	/**
	 * @param Nette\Localization\ITranslator
	 * @return self
	 */
	public function setTranslator(Nette\Localization\ITranslator $translator): self {
		$this->translator = $translator;
		return $this;
	}

	/**
	 * @param string
	 * @return self
	 */
	public function setDropzoneTemplate(string $template): self {
		$this->dropzoneTemplate = $template;
		return $this;
	}

	/**
	 * @param AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 * @return self
	 */
	public function setUploadDriver(UploadDriver\IUploadDriver $driver): self {
		$this->uploadDriver = $driver;
		return $this;
	}

	/**
	 * @param array
	 * @return self
	 */
	public function setSettings(array $settings): self {
		if (isset($settings["maxFilesize"])) {
			$settings["maxFilesize"] = $settings["maxFilesize"] / 1024 / 1024;
		}
		if (isset($settings["maxFilesize"])) {
			$settings["acceptedFiles"] = implode(", ", $settings["acceptedFiles"]);
		}

		$this->settings = $settings;
		return $this;
	}

	/**
	 * @param array
	 * @return self
	 */
	public function setMessages(array $messages): self {
		$this->messages = $messages;
		return $this;
	}

	/**
	 * @return Nette\Localization\ITranslator
	 */
	public function getTranslator(): Nette\Localization\ITranslator {
		return $this->translator;
	}

	/**
	 * @return string
	 */
	public function getDropzoneTemplate(): string {
		return $this->dropzoneTemplate;
	}

	/**
	 * @return AlesWita\DropzoneUploader\UploadDriver\IUploadDriver
	 */
	public function getUploadDriver(): AlesWita\DropzoneUploader\UploadDriver\IUploadDriver {
		return $this->uploadDriver;
	}

	/**
	 * @return array
	 */
	public function getSettings(): array {
		return $this->settings;
	}

	/**
	 * @return array
	 */
	public function getMessages(): array {
		return $this->messages;
	}

	/**
	 * @return void
	 */
	public function render(): void {
		$this->template->fileParam = $this->getParameterId("file");
		$this->template->settings = $this->settings;
		$this->template->messages = $this->messages;

		$this->template->setFile($this->dropzoneTemplate);
		$this->template->setTranslator($this->translator);
		$this->template->render();
	}

	/** ******************** */

	/**
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentForm(): Nette\Application\UI\Form {
		$form = new Nette\Application\UI\Form;

		$form->getElementPrototype()->setClass("dropzone")
			->setId("dropzoneForm");

		$form->onSuccess[] = function (Nette\Application\UI\Form $form, array $values): void {
			$httpData = $form->getHttpData();
			$this->uploadDriver->upload($httpData["file"]);
		};

		return $form;
	}

	/**
	 * @param string
	 * @return void
	 */
	public function handleRemove(string $file = NULL): void {
		if ($file !== NULL) {
			$this->uploadDriver->remove($file);
		}
	}
}
