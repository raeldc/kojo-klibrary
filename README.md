Comparing KoJo with Nooku
=========================

KoJo is not a competition to Nooku, it's an alternative. It has a different approach, so depending on how your brain is wired, you may appreciate the power and simplicity of KoJo a little bit more.

Nooku is revolutionary to Joomla. It is as it claims to be: it reduces the required code to create a component, and it provides many tools that makes development easier and faster.

But what if we can reduce it further? What if there is another approach that is just as powerful, but simpler and easier to understand? That's what KoJo is aiming for. 
KoJo is a "proof of concept" for now. I'm awaiting feedback from the community. Maybe this proof of concept is worth pushing forward into a full blown project.

Nooku provides an example Joomla 1.5 component named `com_library`. As a proof of concept, I rewrote it using KoJo and found inspiring results!

Using KoJo, I was able rewrite `com_library` using only 11 essential files, with 57 KB of code. Nooku's `com_library` has 21 files taking 94KB. That's 50% less files, and 40% less code size!

If I remove the redundancies, I can even reduce `com_library` to 60% less code. The redundancies in my code only serves readability. 

I named the component `com_klibrary` and it runs on Jooml 1.6(beta 3) administration backend. Nooku's `com_library` runs on Joomla 1.5.

Initialization
--------------
The following is a line by line explanation but the complete code is [here](http://github.com/raeldc/kojo-klibrary/blob/master/admin/klibrary.php). 

This is how you initialize the KoJo Framework in the gateway file of your component. 

	(JFactory::getApplication()->triggerEvent('InitializeKoJo', JPATH_COMPONENT)) or die('Please install or enable the KoJo Framework Plugin');

After that, you can now initialize the Routing. Routing allows you to interpret a URL segment. It has tremendous flexibility that allows you to format your urls almost anyway you want it using Regex.

*Note: The Routing system of KoJo is a very powerful stuff. It's OK if you don't understand it for now. But once you understand it, you'll see why this is better than the* `router.php` 
*of Joomla. If you are familiar with Django, then you can understand the routing of KoJo easier.*

	Route::set('default', '<action>(/<task>)(/<id>)(:<ordering>(:<table>))', array(
		'action' => '[a-zA-Z]*',
		// As you can see here, I can control which tasks can be accepted
		'task' => 'add|edit|delete|save|apply|cancel', 
		'ordering' => 'asc|desc',
		'table' => '[a-zA-Z]*',
	))
	->defaults(array(
		// These are the default values
		'controller' => 'library',
		'action'     => 'books',
	));

You don't need to build a complicated router in Joomla's `router.php`! Everything can be controlled here!

Because Joomla's admin backend doesn't have SEF urls, the route is passed through the `$_GET['route'] or $_GET['view']` if `$_GET['route']` doesn't exist.

	$route = JRequest::getVar('route', JRequest::getVar('view', 'books'));
	
	// If there is a $_POST, try to translate the post values into a route.
	if ($_POST) 
	{
		$route = Route::get('default')->uri($_POST);
	}

We can also generate a route based on the `$_POST`. This is useful for adminForm submissions in the Joomla admin backend. 

The default route is simply `books`.  If our component is accessed inside Joomla, the url is something like this: `http://localhost/index.php?option=com_klibrary&route=books`. 
If KoJo is running outside Joomla(yes you can use the framework outside Joomla!), the url will be like this: `http://localhost/books`. If all the values in the route are filled, the url will look like this:
`http://localhost/books/list:asc:title` and `http://localhost/index.php?option=com_klibrary&route=books/list:asc:title` inside Joomla. 

No matter what the url is, the Route declaration makes sure that only the `library` controller is used. If our application has more controllers, then we can make modify the Route to accept other controllers from the url.

Now that we know the Route, we can go to the execution of the component. You do it like this:

	echo Request::factory($route)
		->execute()
		->response;

The Request class calls and executes a controller based on the route. It returns a text response. You can do whatever you want with this text before you render it for display.

Now just de-initialize KoJo to avoid conflicts.

	JFactory::getApplication()->triggerEvent('ExitKojo', JPATH_COMPONENT_SITE);

The "library.php" controller
------------------------------

Now let's go to the Controller. Based on the route, KoJo will always run the "library" controller. Everything related to the "library" can be found there. Nooku has 3 controllers without code! 
But of course Nooku controllers extend a parent Controller that does most of the job magically. 

At this point I should explain that a KoJo controller does what a View+Controller do in Nooku or Joomla. In KoJo, the Controller and the View is combined into one controller. 
The View in KoJo is the same as a Template in Nooku or Joomla.

The controller, `library.php` can be found inside **/classes/controller**. It is named `Controller_Library` and extends a parent `Controller`. Here you have a glimpse the practical naming convention in KoJo which will be explained later.

Now let's take a look at the method inside the controller that lists all the books from the database. 

	class Controller_Library extends Controller 
	{
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
		}
	}

The Route tells the request which controller and method to execute. In this case the default is the action "books", which would mean that it will execute the controller method `action_books`. 

All actions that can be accessed by the request is prepended by `action_`. The default route is `books/list:asc:title`. Our route declaration is `<action>(/<task>)(/<id>)(:<ordering>(:<table>))`.

You don't need to understand it for now, but suffice it to say that the first segment is the `action`(books), the second segment is the `task`(list), the 3rd is the `ordering`(asc), and 4th is `table`(title). 

Let's dissect `action_books` line by line. 
	
**We get the url segments by using $this->request->param('paramter', [default])**

	$ordering = $this->request->param('ordering', 'asc');
	$table = $this->request->param('table', 'title');

**We use the Jelly ORM to get the database results**

	$books = Jelly::select('book')
				->order_by($table, $ordering)
				->execute();

**We get the view and pass the model into it.**

	// The view is located at /views/books/list.php
	$this->request->response = View::factory('books/list')
		->set('books', $books)
		->set('ordering', $ordering)
		->set('table', $ordering)
		->render();

You can check the view [here](http://github.com/raeldc/kojo-klibrary/blob/master/admin/views/books/list.php). Inside the view, we loop through the `$books` collection of objects. 
Also notice that by using `$book->author->name`, we are able to access the author to which the book belongs without issuing another query or declaring an sql join. More on models in the next section.

*Note: For the sake of brevity, pagination wasn't added yet. But it's really easy to implement. You are not tied to Joomla's pagination. You can build your own or use KoJo's pagination system.*

Declaring Models through the ORM
--------------------------------

The last missing piece needed to run a KoJo application is the Jelly ORM. This is an  well thought-of, uber-powerful, ultra-flexible ORM. 

The Model is found at **/classes/model/book.php**. It is named `Model_Book` and it extends `Jelly_Model`. Do you now have an idea how KoJo's naming convention work? More on that subject later.

	class Model_Book extends Jelly_Model
	{
		public static function initialize(Jelly_Meta $meta)
		{
			$meta->table('library_books')
				->fields(array(
					'id' => new Field_Primary(array(
						'column' => 'library_book_id'
					)),
					'title' => new Field_String,
					'author' => new Field_BelongsTo(array(
						'column' => 'library_author_id',
						'foreign' => 'author.library_author_id',
					)),
					'genre' => new Field_BelongsTo(array(
						'column' => 'library_genre_id',
						'foreign' => 'genre.library_genre_id',
					)),
				));
		}
	}

As you can see, we declared the table field-by-field. Why? So that the model is content aware! There are so many powerful stuff you can do with it. You can validate/filter/transform each field on save and fetch!
This is just the tip of the iceberg. All the features of Jelly ORM can't be discussed here.

*Note: Jelly ORM is partly inspired by Django but improved upon significantly!*

So by using Jelly ORM, we now know everything there is to know about a database object. It gets even yummier when we use it in editing and saving items!

Creating and Editing Database Items
-----------------------------------

Here is a summary of how to use Jelly ORM in creating and saving items in the database.

Your controller method can look something like this:

	public function action_book()
	{
		// The default task is edit, this means that we display the form.
		$task = $this->request->param('task', 'edit');
		$id = $this->request->param('id', NULL);
		
		// This will load the book if it exists. It will create an empty object if the book doesn't exist
		// 		If the book doesn't exist, it means that we are about to create a new item. 
		//		If it exists, then we are editing an iteam
		$book = Jelly::select('book', $id);
		
		// We can pass the form submission to the same controller method, we just have to detect it
		if ($_POST AND $task == 'save')
		{
			// Fill up the book with the data from $_POST. Our Model can make sure that the values are clean.
			// 		This will create or edit an object.
			$book->set($_POST)->save();
			
			// Since we saved it, just redirect. Note that in the com_klibrary component, 
			//		we react depending on the task which can be 'apply' or 'save'. 
			$this->request->redirect('books');
			
		}
		
		// If there is no $_POST data then we'll just load the view
		
		// Let's load the authors and genres for the dropdown select. No need to use helpers!
		$authors = Jelly::select('author')->execute()->as_array('id', 'name');
		$genres = Jelly::select('genre')->execute()->as_array('id', 'name');
		
		// We can then use the same view for creating or editing an object
		$this->request->response = View::factory('books/edit')
			->set('book', $book)
			->set('authors', $authors)
			->set('genres', $genres)
			->render();
	}

There you go! Hopefully this simple piece of explanation is enough to show you the power and flexibility of KoJo. There are still a lot of good things about it but I can't discuss everything. 
You can download the latest demo installable packages [here](http://github.com/raeldc/kojo-project/tree/master/packages/). 

If you're interested in the development of KoJo, just follow project on [GitHub](http://github.com/raeldc/kojo-project) or follow me on [Twitter](http://twitter.com/raeldc). 

Remember that KoJo is just a proof of concept. But if the Joomla community wants it to grow, it will!

Performance Summary
===================

**com_klibrary**

	Joomla 1.6 Only: 4.0MB
	After Initializing Kohana: 4.4MB
	After Displaying the View: 6MB
	Total Memory Usage without Joomla: 2MB
	Total Memory Usage without Joomla and Jelly: 1.3MB
	Total Execution Time: 0.127 seconds
		
**com_library**

	Joomla 1.5 Only: 5.3MB
	Total Memory Usage: 6.7MB
	Initializing Koowa: 5.3MB
	Total Memory Usage without Joomla: 1.4MB
	Total Queries: 13
	Total Execution Time: 0.217 seconds
	

Note: A component written in KoJo uses 600kb more memory than Nooku if the powerful Jelly ORM is used. However, if the default KoJo Database is used it will use 100kb less. 

There are other ORMs available for KoJo, but we haven't tested them yet.

The execution time is not yet properly tested since the 2 are using different platforms, J! 1.5, and 1.6.