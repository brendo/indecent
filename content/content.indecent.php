<?php

	require_once(TOOLKIT . '/class.administrationpage.php');

	Class contentExtensionIndecentIndecent extends AdministrationPage {
		protected $_driver;

		function __construct(&$parent){
			parent::__construct($parent);
			$this->setTitle(__('Symphony &ndash; Indecent Filter'));
		}

		public function view(){
			$this->_driver = $this->_Parent->ExtensionManager->create('indecent');

			$this->setPageType('form');
			$this->Form->setAttribute('enctype', 'multipart/form-data');
			$this->appendSubheading(__('Indecent Filter'));

			## Word List

				$container = new XMLElement('fieldset');
				$container->setAttribute('class', 'settings');
				$container->appendChild(
					new XMLElement('legend', __('Word Blacklist'))
				);

				$p = new XMLElement('p', __(sprintf(
					'Enter each of the terms on a new line. <br /> This list was last updated on %s.',
					$this->_driver->lastUpdateFilterList()
					))
				);
				$p->setAttribute('class', 'help');
				$container->appendChild($p);

				$group = new XMLElement('div');
				$group->setAttribute('class', 'group');
				$group->appendChild(Widget::Textarea('fields[words]',30,30, $this->_driver->processFilterList(true)));

			$container->appendChild($group);
			$this->Form->appendChild($container);

			## Actions

				$div = new XMLElement('div');
				$div->setAttribute('class', 'actions');

				$attr = array('accesskey' => 's');
				$div->appendChild(Widget::Input('action[save]', 'Save', 'submit', $attr));

			$this->Form->appendChild($div);
		}

		public function __actionIndex() {
			if (empty($this->_driver)) {
				$this->_driver = $this->_Parent->ExtensionManager->create('indecent');
			}

			if (isset($_POST['action']['save'])) {
				$state = $this->_driver->saveFilterList($_POST['fields']['words']);
			}

			if($state !== FALSE) {
				$this->pageAlert(
					__("Indecent Filter List was updated successfully"),
					Alert::SUCCESS
				);
			} else {
				$this->pageAlert(
					__("There was an error saving the Indecent Filter List"),
					Alert::ERROR
				);
			}
		}
	}

?>