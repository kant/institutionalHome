<?php

/**
 * @file plugins/generic/institutionalHome/InstitutionalHomePlugin.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class InstitutionalHomePlugin
 * @ingroup plugins_generic_browsebysection
 *
 * @brief Plugin that adds an institutional home field to a journal.
 */
import('lib.pkp.classes.plugins.GenericPlugin');

class InstitutionalHomePlugin extends GenericPlugin {

	/**
	 * @copydoc Plugin::register
	 */
	public function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return $success;
		if ($success && $this->getEnabled()) {
			HookRegistry::register('Schema::get::context', array($this, 'addToSchema'));
			HookRegistry::register('Form::config::before', array($this, 'addToForm'));
		}
		return $success;
	}

	/**
	 * @copydoc PKPPlugin::getDisplayName
	 */
	public function getDisplayName() {
		return __('plugins.generic.institutionalHome.name');
	}

	/**
	 * @copydoc PKPPlugin::getDescription
	 */
	public function getDescription() {
		return __('plugins.generic.institutionalHome.description');
	}

	/**
	 * Add a property to the context schema
	 *
	 * @param $hookName string `Schema::get::context`
	 * @param $schema object Context schema
	 */
	public function addToSchema($hookName, $schema) {
		$prop = '{
			"type": "string",
			"apiSummary": true,
			"validation": [
				"nullable"
			]
		}';
		$schema->properties->institutionalHome = json_decode($prop);
	}

	/**
	 * Add a form field to a form
	 *
	 * @param $hookName string `Form::config::before`
	 * @param $form FormHandler
	 */
	public function addtoForm($hookName, $form) {
		if (!defined('FORM_MASTHEAD') || $form->id !== FORM_MASTHEAD) {
			return;
		}

		$context = Application::getRequest()->getContext();

		if (!$context) {
			return;
		}

		$form->addField(new \PKP\components\forms\FieldText('institutionalHome', [
			'label' => __('plugins.generic.institutionalHome.label'),
			'groupId' => 'publishing',
			'value' => $context->getData('institutionalHome'),
		]));
	}
}

?>
