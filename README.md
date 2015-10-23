Uni Alteri - States library
===========================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/417986ff-17b4-4772-a3d8-9885d6304786/mini.png)](https://insight.sensiolabs.com/projects/417986ff-17b4-4772-a3d8-9885d6304786) [![Build Status](https://travis-ci.org/UniAlteri/states.svg?branch=master)](https://travis-ci.org/UniAlteri/states)

Welcome and thank you to having downloaded this library. 
It's allow you to create PHP classes following the [State Pattern](http://en.wikipedia.org/wiki/State_pattern) in PHP. 
This can be a cleaner way for an object to change its behavior at runtime without resorting to large monolithic conditional statements and thus improve maintainability.
 
Example
-------
An example of using this library is available in the folder : [Demo](demo/demo_article.php).

Installation
------------
To install this library with composer, run this command :

    composer require unialteri/states:next-dev

Requirements
------------
This library requires :

    * PHP 7+ (For PHP5.4 to 5.6, please to use the first major version, States 1.0+)
    * Composer

Although highly recommended, Composer is not needed, this library can be used with its own psr0 autoloader.

Presentation
------------
Quick How-to to learn how use this library : [Startup](docs/howto/details.md).

Quick startup
-------------
Quick How-to to learn how use this library : [Startup](docs/howto/quick-startup.md).

API Documentation
-----------------
Generated documentation from the library with PhpDocumentor : [Open](https://cdn.rawgit.com/TeknooSoftware/states/master/docs/api/index.html).

Behavior Documentation
----------------------
Documentation to explain how this library works : [Behavior](docs/howto/behavior.md).

Mandatory evolutions in 2.x versions
------------------------------------

From the version 2.0, this library has been redesigned to 
* Reuse all composer's autoloader usefull and powerfull features instead internal autoloader.
* Reduce the number of necessary components to the internal functioning of this library (Dependency Injector, Closure Injector). 
* Forbid the usage of slows functions like `call_user_func`.
* Use `Closure::call()` instead of `Closure::bind` to reduce memory ans cpu consumptions.
* Use Scalar Type Hinting to use PHP Engine's check instead if statements.

Credits
-------
Richard Déloge - <r.deloge@uni-alteri.com> - Lead developer.
Uni Alteri - <http://uni-alteri.com> - <http://teknoo.it>

License
-------
States is licensed under the MIT and GPL3+ Licenses - see the licenses folder for details

Contribute :)
-------------

You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)
