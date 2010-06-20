<?php defined('SYSPATH') or die('404 Not Found.');

/**
* 
*/
class ComKLibrary_Admin_Model_ORM_Book extends ORM
{
	protected $_table_name = 'library_books';
	protected $_belongs_to = array(
		'author' => array(
			'model' => 'author', 
			'foreign_key' => 'author_id'
		),
		'genre' => array(
			'model' => 'genre', 
			'foreign_key' => 'genre_id'
		)
	);
}
