<?php defined('_JEXEC') or die;

JFactory::getApplication()->triggerEvent('InitializeKojo', JPATH_COMPONENT);

/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */

Route::set('default', '<action>(/<id>)(:<ordering>(:<table>))', array(
		'action' => '[a-zA-Z]*',
		'ordering' => 'asc|desc',
		'table' => '[a-zA-Z]*',
	))
	->defaults(array(
		'controller' => 'library',
		'action'     => 'books',
	));
	
/*
 * Follow Joomla's standard, use the "view" $_GET variable as the route. 
 *		This is to allow Joomla to create Menu Items in the admin section that points 
 *		to different controllers of the Kojo Application.
*/
$route = JRequest::getVar('route', JRequest::getVar('view', 'books'));

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::factory($route)
	->execute()
	->response;

JFactory::getApplication()->triggerEvent('ExitKojo', JPATH_COMPONENT_SITE);