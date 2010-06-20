<?php defined('SYSPATH') or die('404 Not Found.');

class ComKLibrary_Admin_Model_Genre extends Jelly_Model
{
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('library_genres')
			->fields(array(
				'id' => new Field_Primary,
				'name' => new Field_String,
				'books' => new Field_HasMany
			));
	}
}