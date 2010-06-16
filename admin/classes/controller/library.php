<?php defined('SYSPATH') or die('404 Not Found.');

class Controller_Library extends Controller 
{
	public function before()
	{
		parent::before();
		
		$views =  array(
			'books' 			=> 'Books',
			'authors' 			=> 'Authors',
			'genres' 			=> 'Genres'
	        );
                  
		foreach($views as $view => $title)
		{
			$active = ($this->request->action == $view);
			JSubMenuHelper::addEntry(JText::_($title), HTML::uri($view), $active);
		}
		
	}
	
	public function action_books()
	{	
		$ordering = $this->request->param('ordering', 'asc');
		$table = $this->request->param('table', 'title');
		
		$books = Jelly::select('book')
					->order_by($table, $ordering)
					->execute();
		
		$this->request->response = View::factory('books/list')
			->set('books', $books)
			->set('ordering', $ordering)
			->set('table', $ordering)
			->render();
			
		JToolBarHelper::title( JText::_('Library - Books'));
		JToolBarHelper::deleteList('Are you sure you want to delete these books?', 'delete');
		JToolBarHelper::addNew();
	}
	
	public function action_authors()
	{
		$ordering = $this->request->param('ordering', 'asc');
		$table = $this->request->param('table', 'name');
		
		$authors = Jelly::select('author')
					->order_by($table, $ordering)
					->execute();
		
		$this->request->response = View::factory('authors/list')
			->set('authors', $authors)
			->set('ordering', $ordering)
			->set('table', $ordering)
			->render();
			
		JToolBarHelper::title( JText::_('Library - Authors'));
		JToolBarHelper::deleteList('Are you sure you want to delete these Authors?', 'delete');
		JToolBarHelper::addNew();
	}
	
	public function action_genres()
	{
		$ordering = $this->request->param('ordering', 'asc');
		$table = $this->request->param('table', 'name');
		
		$genres = Jelly::select('genre')
					->order_by($table, $ordering)
					->execute();
		
		$this->request->response = View::factory('genres/list')
			->set('genres', $genres)
			->set('ordering', $ordering)
			->set('table', $ordering)
			->render();
			
		JToolBarHelper::title( JText::_('Library - Genres'));
		JToolBarHelper::deleteList('Are you sure you want to delete these Genres?', 'delete');
		JToolBarHelper::addNew();
	}
}