<?php defined('SYSPATH') or die('404 Not Found.');

/**
* 
*/
class ComKLibrary_Admin_Model_ORM_Author extends ORM
{
	protected $_table_name = 'library_authors';
	protected $_has_many = array(
		'books' => array(),
	);
}
