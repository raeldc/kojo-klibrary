<?php defined('SYSPATH') or die('404 Not Found.');

class ComKLibrary_Admin_Model_Book extends Jelly_Model
{
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('library_books')
			->fields(array(
				'id' => new Field_Primary,
				'title' => new Field_String,
				'author' => new Field_BelongsTo,
				'genre' => new Field_BelongsTo,
			));
	}
}