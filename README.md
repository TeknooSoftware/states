Teknoo Software - States library
================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a/mini.png)](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a) [![Build Status](https://travis-ci.org/TeknooSoftware/states.svg?branch=next)](https://travis-ci.org/TeknooSoftware/states)

Welcome and thank you to having downloaded this library. 
It's allow you to create PHP classes following the [State Pattern](http://en.wikipedia.org/wiki/State_pattern) in PHP. 
This can be a cleaner way for an object to change its behavior at runtime without resorting to large monolithic conditional statements and this improve maintainability.
 
Example
-------
An example of using this library is available in the folder : [Demo](demo/demo_article.php).

Installation
------------
To install this library with composer, run this command :

    composer require teknoo/states

Requirements
------------
This library requires :

    * PHP 7+ (For PHP5.4 to 5.6, please to use the first major version, States 1.0+)
    * Composer

Presentation
------------
Description about components of this library : [Startup](docs/howto/details.md).

Quick startup
-------------
Quick How-to to learn how use this library : [Startup](docs/howto/quick-startup.md).

API Documentation
-----------------
Temporary unavailable

Behavior Documentation
----------------------
Documentation to explain how this library works : [Behavior](docs/howto/behavior.md).

Mandatory evolutions in 2.x versions
------------------------------------

From the version 2.0, this library has been redesigned to 
* Reuse all composer's autoloader features instead internal autoloader.
* Reduce the number of necessary components to the internal functioning of this library (Dependency Injector, Closure Injector). 
* Forbid the usage of slows functions like `call_user_func`.
* Use Scalar Type Hinting to use PHP Engine's check instead if statements.

Credits
-------
Richard Déloge - <richarddeloge@gmail.com> - Lead developer.
Teknoo Software - <http://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge, also co-founder of the web agency Uni Alteri. 
Teknoo Software shares the same DNA as Uni Alteri : Provide to our partners and to the community a set of high quality services or software, sharing knowledge and skills.

License
-------
States is licensed under the MIT License - see the licenses folder for details

Contribute :)
-------------

You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)
