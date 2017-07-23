Teknoo Software - States library
================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a/mini.png)](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a) [![Build Status](https://travis-ci.org/TeknooSoftware/states.svg?branch=next)](https://travis-ci.org/TeknooSoftware/states)

States allows you to create PHP classes following the [State Pattern](http://en.wikipedia.org/wiki/State_pattern) in PHP. 
This can be a cleaner way for an object to change its behavior at runtime without resorting to large monolithic conditional statements and this improve maintainability.

[Lifecycable extension](https://github.com/TeknooSoftware/states-life-cycle) : To automate states changes according to business rules.

Short Example
------------
    /**
     * File States/English.php
     */
    class English extends \Teknoo\States\State\AbstractState 
    {
        public function sayHello(): \Closure
        {
            return function(): string {
                return 'Good morning, '.$this->name;
            };
        }
    
        public function displayDate(\DateTime $now): \Closure
        {
            return function(): string {
                return $now->format('%m %d, %Y');
            };
        }
    }
    
    /**
     * File States/French.php
     */
    class French extends \Teknoo\States\State\AbstractState 
    {
        public function sayHello(): \Closure
        {
            return function(): string {
                return 'Bonjour, '.$this->name;
            };
        }
    
        public function displayDate(\DateTime $now): \Closure
        {
            return function(): string {
                return $now->format('%d %m %Y');
            };
        }
    }
    
    /**
     * File MyClass.php
     */
    class MyClass extends \Teknoo\States\Proxy\Standard
    {
        /**
         * @var string
         */
        private $name;
        
        public static function statesListDeclaration(): array
        {
            return [
                English::class,
                French::class
            ];
        }
        
        public function setName(string $name): MyClass
        {
            $this->name = $name;
            
            return $this;
        }
    }
    
    $frenchMan = new MyClass();
    $frenchMan->switchState(French::class);
    $frenchMan->setName('Roger');
    
    $englishMan = new MyClass();
    $englishMan->switchState(English::class);
    $englishMan->setName('Richard');
    
    $now = new \DateTime('2016-07-01');
    
    foreach ([$frenchMan, $englishMan] as $man) {
        echo $man->sayHello().PHP_EOL;
        echo 'Date: '.$man->displayDate($now);
    }
    
    //Display
    Bonjour Roger
    Date: 01 07 2016
    Good morning Richard
    Date: 07 01, 2016
 
Full Example
------------
An example of using this library is available in the folder : [Demo](demo/demo_article.php).

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/states

This library requires :

    * PHP 7+
    * A PHP autoloader (Composer is recommended)
    
Quick How-to to implement your first stated class
-------------------------------------------------
Quick How-to to learn how to use this library : [Startup](docs/howto/quick-startup.md).

Behavior Documentation
----------------------
Documentation to explain how this library works : [Behavior](docs/howto/behavior.md).

API Documentation
-----------------
The API documentation is available at : [API](docs/howto/api/index.index).

Evolutions in 3.x versions
--------------------------

From the version 3.0, this library has been redesigned to
* States's method are now builders of closure : They must return a closure, bindable with \Closure::call(). 
  The Reflection API is no longer used to get a closure.
* The library uses \Closure::call() instead of \Closure::rebindTo(), more efficient.  
* States's class must be referenced declared in the proxy class, via the static method `statesListDeclaration()`.
* Factories and Loaders are removed, they have become useless.
* Proxy standard can be now directly instantiate. Integrated proxy are also removed.

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
**Teknoo Software** is a PHP software editor, founded by Richard Déloge. 
Teknoo Software's goals : Provide to our partners and to the community a set of high quality services or software,
 sharing knowledge and skills.

License
-------
States is licensed under the MIT License - see the licenses folder for details

Contribute :)
-------------

You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)
