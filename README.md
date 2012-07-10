yasp-mf
=======

Yet Another Simple PHP MVC Framework


What is it?
-----------

yasp-mf is a simple mvc framework for building php applications. 
It uses mod_rewrite rules to route URLs to the correct controllers and actions.

I started creating the framework following the tutorial here
[http://anantgarg.com/2009/03/13/write-your-own-php-mvc-framework-part-1/](http://anantgarg.com/2009/03/13/write-your-own-php-mvc-framework-part-1/),
but extended and changed it a lot and intend to change and extend it much more.

Getting started
---------------

To get started, just copy the project to a folder in your web server's document root. 
Open the folder's url in the browser and you should read "Hello, yasp-mf".

### What happend?

When no controller and action is given in the url, yasp-mf takes _start/index_ instead.
So the method _index_ in the class _StartController_ has been called an afterwards the
view start/index.php has been rendered. The controller just set the greetings message
and the view displayed it inside a H1 tag.

To call a controller and action directly go to the URL 

    http://server/path/to/app/controller/action/param_1/param_2

### What next?

Now you can create your own controllers and actions. The filename of a controller has to be its
class name in lowercase. The controller class name has to end with _Controller_.

For every action has to be a corresponding view. In the case of our start/index example the view is
located in the views/start directory and is named index.php.

To create a link to a controller/action use the function 
   
    _link($controller, $action, $params)

### What if I want to use a database?

yasp-mf also includes a very simple way to access data stored in a mysql database called sppo
(self-persistent php objects). To use this functionallity, change the setting _USE_DB_ 
(in config/config.php) to _true_ and add your db connection data to the config.

Now you have to create a class (in the _model_ directory) for each db table you want to access.
This classes have to extend the class _Model_ and at least implement the abstract methods.

To see how it works, go to the Person class. When you have a table named _persons_ with the 
following columns

    id INTEGER AUTO INCREMENT
    first_name VARCHAR(50)
    last_name VARCHAR(50)
    age INTEGER

you can use the Person class the following way

    // fetch a Person by ID
    $p = new Person();
    $p->id = 2;
    $p->fetch();

    // fetch a list of persons by age
    $p = new Person();
    $oldOnes = $p->fetchAll('age > 60');

    // store a new person
    $p = new Person();
    $p->first_name = 'Eddie';
    $p->last_name = 'Russett';
    $p->age = 21;
    $p->persist();
    
Simple, eh?

### What else?

- You can redirect to another controller/action from within an action by calling
    
        $this->callStack($controller, $action);

- If you call 
    
        $this->setAjax();

    no header and footer is rendered. This way you can use the action for ajax/json requests.

- Only the public directory must be within the document root.

- All stylesheets, java scripts and images go to the assets directory.

- Inside the assets folder you find some stubs for the frontend, including jQuery 1.7.2, html5shiv and SASS/COMPASS stubs.


TODOs
-----

- Much more and better documentation.
- SPPOs need more work (more data types, relations, etc.).
- Adding some build framework, e.g. grunt.js.
- Adding boilerplate, etc.

Fork, anyone?

