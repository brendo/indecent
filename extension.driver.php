<?php

	class extension_indecent extends Extension {
		public static $path = "/indecent/lib/indecentfilter.txt";

		public function about() {
			return array(
				'name'			=> 'Filter: Indecent',
				'version'		=> '0.1',
				'release-date'	=> '2010-02-15',
				'author'		=> array(
					'name'			=> 'Brendan Abbott',
					'website'		=> 'http://bloodbone.ws/',
					'email'			=> 'brendan@bloodbone.ws'
				),
				'description'	=> 'Validates your forms against a blacklist'
	 		);
		}

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
			$filter_list = $this->processFilterList();

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
		public function saveFilterList($data) {
			return file_put_contents(EXTENSIONS . self::$path, $data);
		}

		public function processFilterList($raw = false) {

			$file = file_get_contents(EXTENSIONS . self::$path);

			if(!$file) return false;
			if($raw) return $file;

			$filters = explode(PHP_EOL, $file);
			$filters = array_unique($filters);
			$filters = array_map('trim', $filters);
			$filters = array_filter($filters);

			return $filters;
		}

		public function lastUpdateFilterList() {
			return DateTimeObj::get('jS F, Y \a\t g:ia', filemtime(EXTENSIONS . self::$path));
		}
	}

?>