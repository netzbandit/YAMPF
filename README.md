yasp-mf
=======

Yet Another Simple PHP MVC Framework


What is it?
-----------

yasp-mf is a simple MVC framework for building PHP applications. 
It uses mod_rewrite rules to route URLs to the correct controllers and actions.

Getting started
---------------

To get started, just copy the project to a folder in your web server's document root. 
Open the folder's URL in the browser and you should read "Hello, yasp-mf".

### What happend?

When no controller and action is given in the URL, yasp-mf takes _start/index_ instead.
So the method _index_ in the class _StartController_ has been called an afterwards the
view start/index.php has been rendered. The controller just set the greetings message
and the view displayed it inside a H1 tag.

To call a controller and action directly go to the URL 
http://<SERVER>/<PATH_TO_APP>/<CONTROLLER>/<ACTION>/<PARAM_1>/<PARAM_2>

### What next?

Now you can create your own controllers and actions. The filename of a controller has to be its
class name in lowercase. The controller class name has to end with _Controller_.

For every action has to be a corresponding view. In the case of our start/index example the view is
located in the views/start directory and is named index.php.

To create a link to a controller/action use the function 
> _link($controller, $action, $params)


