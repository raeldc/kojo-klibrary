KoJo Simple Tutorial
====================

The philosophy of KoJo can be summed up in this statement: 

> **KoJo gets out of the developer's way but assists them where it matters.**

KoJo is not your baby sitter to assist you in implementing good practices in programming. 

This Tutorial is the simplest way to use KoJo. Larger applications may require a stricter MVC compliance, and application of other design patterns which you are free to use or misuse within the KoJo Framework.

Initialization
--------------
The following is a line by line explanation but the complete code is [here](http://github.com/raeldc/kojo-klibrary/blob/master/admin/klibrary.php). 

This is how you initialize the KoJo Framework in the gateway file of your component. 

	(JFactory::getApplication()->triggerEvent('InitializeKoJo') or die('Please install or enable the KoJo Framework Plugin');

After that, you issue a request. The request is at the core of Kohana because it's an HMVC(Hierarchical Model View Controller) framework. 

	echo Request::instance()
		->defaults(array(
			'controller' => 'library',
			'action' = 'books'
		))
		->execute()
		->response;

The Request class calls and executes a controller based on the url or based on the defaults you set. It returns a text response. You can do whatever you want with this text before you render it for display.

After the request, just de-initialize KoJo to avoid conflicts.

	JFactory::getApplication()->triggerEvent('ExitKojo');

The "library.php" controller
------------------------------

Now let's go to the Controller. If you access `index.php?option=com_klibrary`, KoJo will run the "library" controller based on the Request call defaults. 
If you access `index.php?option=com_klibrary&controller=foo`, the Request will look for the controller named `foo`. If it's not found, a fatar error will be thrown.

At this point I should explain that a simple KoJo controller can do what a View+Controller do in Nooku or Joomla. You can implement your own View class to achieve more separation between the MVC triad. 
But in simple applications, this simple controller->model->controller->template connection should be enough. 

The View in KoJo is just a class that accepts variables and outputs a PHP. You can overload this View class so you can implement your own.

The controller, `library.php` can be found inside **/classes/controller**. It is named `ComKLibrary_Admin_Controller_Library` and extends a parent `Controller`. 
Here you have a glimpse the practical naming convention in KoJo which is explained by the end of this introduction.

Now let's take a look at the method inside the controller that lists all the books from the database. 

	class ComKLibrary_Admin_Controller_Library extends Controller 
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

The Request determines which controller and method to execute based on the URL or based on the defaults that you assign. In this case the default is the action "books", which would mean that it will execute the controller method `action_books`.
All actions that can be accessed by the request is prepended by `action_`. 

The Request also calls the method `before()` before it runs `action_books()`. Then it calls the method `after()` after it runs `action_books()`. In your controller, you can extend these pre/post methods to perform additional stuff.

Let's dissect `action_books` line by line. 
	
**We get the url parameters by using $this->request->param('paramter', [default])**

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

*Also avoid the pitfall of creating a [fat controller](http://www.survivethedeepend.com/zendframeworkbook/en/1.0/the.model#zfbook.the.model.the.fat.stupid.ugly.controller). Jelly ORM helps you offset the load from the controller by providing a Builder.*


Declaring Models through the ORM
--------------------------------

The last missing piece needed to run a KoJo application is the Jelly ORM. This is an  well thought-of, uber-powerful, ultra-flexible ORM. 

The Model is found at **/classes/model/book.php**. It is named `ComKlibrary_Admin_Model_Book` and it extends `Jelly_Model`. Do you now have an idea how KoJo's naming convention work? More on that subject later.

	class ComKlibrary_Admin_Model_Book extends Jelly_Model
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

As you can see, we declared the table field-by-field. Why? So that the model is content aware! There are so many powerful stuff you can do with it. You can validate/filter/transform each field on save and fetch!
This is just the tip of the iceberg. All the features of Jelly ORM can't be discussed here.

*Note: Jelly ORM is partly inspired by Django but improved upon significantly!*

So by using Jelly ORM, we now know everything there is to know about a database object. It gets even yummier when we use it in editing and saving items!

Creating and Editing Database Items
-----------------------------------

Here is a summary of how to use Jelly ORM in creating and saving items in the database. Note that this is not in the code of the existing package

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

The Naming Convention
---------------------

A Class is usually named `ComExtensionName_ClassDirectory_Class` or `ComExtensionName_Admin_ClassDirectory_Class`. The first 3 letters can be `com`, `mod`, `plg`. 
This is to indicate the type of Joomla extension you are calling. After the extension type prefix is the extension name. 

*	So `ComExtensionName_ClassDirectory_Class` will mean that the file where this class is located is at `/components/com_extensionname/classes/classdirectory/class.php`.
*	`ComExtensionName_Admin_Class` loads `/administrator/components/com_extensionname/classes/class.php`.
*	`ModExtensionName_ClassDirectory_Class` loads `/modules/mod_extensionname/classes/classdirectory/class.php`.

Explanation of the Cascading File Sytem
---------------------------------------

Coming Soon...


There you go! Hopefully this simple piece of explanation is enough to show you the power and flexibility of KoJo. There are still a lot of good things about it but I can't discuss everything. 
You can download the latest demo installable packages [here](http://github.com/raeldc/kojo-project/downloads). 

If you're interested in the development of KoJo, just follow project on [GitHub](http://github.com/raeldc/kojo-project) or follow me on [Twitter](http://twitter.com/raeldc). 

Remember that KoJo is just a proof of concept. But if the Joomla community wants it to grow, it will!

Comparing KoJo with Nooku
=========================

KoJo is not a competition to Nooku, it's an alternative. It has a different approach, so depending on how your brain is wired, you may appreciate the power and simplicity of KoJo a little bit more.

Nooku is revolutionary to Joomla. It is as it claims to be: it reduces the required code to create a component, and it provides many tools that makes development easier and faster.

Nooku is now at 0.67 and is on the fast track to 0.7. If you're looking for a framework that does many things for you, then Nooku would be right for you. 

KoJo is a bare minimum framework, it does almost nothing for you. But you can use a rich pool of libraries that are provided by the OpenSource community. 

In KoJo, you have room to implement your own style of programming. If in your experience with frameworks, you usually end up doing custom code that is out of the "frame", then KoJo would be just right for you.


[Read More about KoJo](http://github.com/raeldc/kojo-project/).

Performance Summary
===================

**com_klibrary**

	Total Memory Usage with Joomla: 6MB
	Joomla 1.6 Only: 4.0MB
	After Initializing KoJo: 4.45MB
	Total Memory Usage without Joomla: 2MB
	Total Memory Usage without Joomla and Jelly: 1.3MB
	Total Execution Time: 0.127 seconds
		

Note: 

There are other ORMs available for KoJo, but I haven't tested them yet.

It's interesting to note that Koowa takes 450KB on initialization. Koowa is initialized on every Joomla page as of version 0.6. KoJo 0.1 uses 430KB on initialization. It's not initialized on every Joomla page outside KoJo application.

I wonder what would happen if I optimize KoJo for Joomla? I think it will get even lighter.

The execution time is not yet properly tested since the 2 are using different platforms, J! 1.5, and 1.6.