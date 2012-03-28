<?php

	require_once(TOOLKIT . '/class.administrationpage.php');

	Class contentExtensionIndecentIndecent extends AdministrationPage {

		public function view(){
			$this->setTitle(__('Symphony &ndash; Indecent Filter'));
			$this->setPageType('form');
			$this->Form->setAttribute('enctype', 'multipart/form-data');
			$this->appendSubheading(__('Indecent Filter'));
			$words = extension_indecent::processFilterList(true);

			// Word List
			$container = new XMLElement('fieldset');
			$container->setAttribute('class', 'settings');
			$container->appendChild(
				new XMLElement('legend', __('Word Blacklist'))
			);

			$p = new XMLElement('p', __('Enter each of the terms on a new line.'), array(
				'class' => 'help'
			));
			$container->appendChild($p);

			// Add last saved timestamp
			if(!empty($words)) {
				$p->setValue($p->getValue() . '<br / >' . __('Last updated: %s', array(
					extension_indecent::lastUpdateFilterList()
				)));
			}

			$container->appendChild(
				Widget::Textarea('fields[words]',30, 30, $words)
			);
			$this->Form->appendChild($container);

			// Actions
			$div = new XMLElement('div');
			$div->setAttribute('class', 'actions');
			$div->appendChild(Widget::Input('action[save]', 'Save', 'submit', array('accesskey' => 's')));

			$this->Form->appendChild($div);
		}

		public function __actionIndex() {
			if (isset($_POST['action']['save'])) {
				$state = extension_indecent::saveFilterList($_POST['fields']['words']);
			}

			if($state !== FALSE) {
				$this->pageAlert(
					__("Indecent Filter List was updated successfully"),
					Alert::SUCCESS
				);
			}
			else {
				$this->pageAlert(
					__("There was an error saving the Indecent Filter List"),
					Alert::ERROR
				);
			}
		}
	}
