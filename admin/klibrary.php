<?php defined('_JEXEC') or die;

(JFactory::getApplication()->triggerEvent('InitializeKoJo')) or die('Please install or enable the KoJo Framework Plugin');

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::instance()
	->defaults(array(
		'controller' => 'library',
		'action' => 'books',
	))
	->execute()
	->response;

JFactory::getApplication()->triggerEvent('ExitKojo');