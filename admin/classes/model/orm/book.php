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
	
	public function fetch_all()
	{
		$query = DB::select(
					$this->_table_name.'.*', 
					array('library_authors.id', 'author_id'),
					array('library_authors.name', 'author_name'),
					array('library_genres.name', 'genre_name'),
					array('library_genres.name', 'genre_name')
				)->from($this->_table_name)
				->join('library_authors')
				->on($this->_table_name.'.author_id', '=', 'library_authors.id')
				->join('library_genres')
				->on($this->_table_name.'.genre_id', '=', 'library_genres.id');
						
		foreach ($this->_sorting as $column => $table) 
		{
			$query->order_by($column, $table);
		}

		return $query->as_object()->execute();
	}
}
