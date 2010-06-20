<?php defined('SYSPATH') or die('404 Not Found.');

/**
* 
*/
class ComKLibrary_Admin_Model_ORM_Genre extends ORM
{
	protected $_table_name = 'library_genres';
	protected $_has_many = array(
		'books' => array(),
	);
}
