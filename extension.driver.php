<?php

	class extension_indecent extends Extension {
		public static $file = "/indecent/blacklist.txt";

		// Symphony Settings
		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '/blueprints/events/new/',
					'delegate'	=> 'AppendEventFilter',
					'callback'	=> 'appendFilter'
				),
				array(
					'page'		=> '/blueprints/events/edit/',
					'delegate'	=> 'AppendEventFilter',
					'callback'	=> 'appendFilter'
				),
				array(
					'page'		=> '/frontend/',
					'delegate'	=> 'EventPreSaveFilter',
					'callback'	=> 'processData'
				)
			);
		}

		public function fetchNavigation() {
			return array(
				array(
					'location' => 'System',
					'name'	=> 'Indecent',
					'link'	=> '/indecent/'
				)
			);
		}

		public function install() {
			return General::realiseDirectory(WORKSPACE . '/indecent/');
		}

		public function uninstall() {
			return General::deleteDirectory(WORKSPACE . '/indecent/');
		}

		// Event Settings
		public function appendFilter($context) {
			$context['options'][] = array(
				'indecent',
				@in_array(
					'indecent', $context['selected']
				),
				'Indecent'
			);
		}

		public function processData($context) {
			if (!in_array('indecent', $context['event']->eParamFILTERS)) return;

			$valid = true;
			$response = null;
			$filter_list = self::processFilterList();

			if(!empty($filter_list)) foreach($_POST['fields'] as $field => $data) {
				foreach($filter_list as $term) {
					if(empty($term)) continue;

					if(preg_match('/\b' . preg_quote($term) . '\b/i', $data)) {
						$valid = false;
						$response = $term . " was detected in " . $field;
						break;
					}
				}
			}

			$context['messages'][] = array(
				'indecent', $valid, $response
			);
		}

		//  Filter List
		public static function saveFilterList($data) {
			return General::writeFile(WORKSPACE . self::$file, $data);
		}

		public static function processFilterList($raw = false) {
			if(!file_exists(WORKSPACE . self::$file)) return '';

			if($raw === true) {
				$file = file_get_contents(WORKSPACE . self::$file);
				return $file;
			}
			else {
				$file = file(WORKSPACE . self::$file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			}

			$filters = array_unique($file);
			$filters = array_map('trim', $filters);

			return $filters;
		}

		public static function lastUpdateFilterList() {
			if(!file_exists(WORKSPACE . self::$file)) return false;

			return DateTimeObj::get(DateTimeObj::getSetting('datetime_format'), filemtime(WORKSPACE . self::$file));
		}
	}
