Teknoo Software - States library
================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a/mini.png)](https://insight.sensiolabs.com/projects/119ff38f-0b64-4100-8e1f-ff55d7be857a) [![Build Status](https://travis-ci.org/TeknooSoftware/states.svg?branch=next)](https://travis-ci.org/TeknooSoftware/states)

States allows you to create PHP classes following the [State Pattern](http://en.wikipedia.org/wiki/State_pattern) in PHP. 
This can be a cleaner way for an object to change its behavior at runtime without resorting to large monolithic conditional statements and this improve maintainability.

Features
--------

* **Create Several States** : Split classes in states to avoid ununderstable large monolithic statements.
* **Inherit States and Classes** : Complete and factorize states thanks to inheritance. Stated classes can be also inherited.
* **Automate States Switching** : Define states switching rules based on object's properties.
* **Implement Every Where**: Thanks to traits and interfaces, use this pattern on your existant code. Compatible with Doctrine.

Quick Example
-------------
    <?php

    require 'vendor/autoload.php';

    use \Teknoo\States\Automated\AutomatedInterface;
    use \Teknoo\States\Automated\AutomatedTrait;
    use Teknoo\States\Automated\Assertion\AssertionInterface;
    use Teknoo\States\Automated\Assertion\Property;
    use Teknoo\States\Automated\Assertion\Property\IsEqual;
    use \Teknoo\States\Proxy\ProxyInterface;
    use \Teknoo\States\Proxy\ProxyTrait;
    use \Teknoo\States\State\AbstractState;

    class English extends AbstractState
    {
        public function sayHello(): \Closure
        {
            return function(): string {
                return 'Good morning, '.$this->name;
            };
        }

        public function displayDate(): \Closure
        {
            return function(\DateTime $now): string {
                return $now->format('m d, Y');
            };
        }
    }

    class French extends AbstractState
    {
        public function sayHello(): \Closure
        {
            return function(): string {
                return 'Bonjour, '.$this->name;
            };
        }

        public function displayDate(): \Closure
        {
            return function(\DateTime $now): string {
                return $now->format('d m Y');
            };
        }
    }

    class Person implements ProxyInterface, AutomatedInterface
    {
        use ProxyTrait,
            AutomatedTrait;

        /** @var string */
        private $name;

        /** @var string */
        private $country;

        public function __construct()
        {
            $this->initializeProxy();
        }

        protected static function statesListDeclaration(): array
        {
            return [
                English::class,
                French::class
            ];
        }

        protected function listAssertions(): array
        {
            return [
                (new Property([English::class]))
                    ->with('country', new IsEqual('en')),
                (new Property([French::class]))
                    ->with('country', new IsEqual('fr')),
            ];
        }

        public function setName(string $name): Person
        {
            $this->name = $name;

            return $this;
        }

        public function setCountry(string $country): Person
        {
            $this->country = $country;
            $this->updateStates();

            return $this;
        }
    }

    $frenchMan = new Person();
    $frenchMan->setCountry('fr');
    $frenchMan->setName('Roger');

    $englishMan = new Person();
    $englishMan->setCountry('en');
    $englishMan->setName('Richard');

    $now = new \DateTime('2016-07-01');

    foreach ([$frenchMan, $englishMan] as $man) {
        echo $man->sayHello().PHP_EOL;
        echo 'Date: '.$man->displayDate($now).PHP_EOL;
    }

    //Display
    //Bonjour, Roger
    //Date: 01 07 2016
    //Good morning, Richard
    //Date: 07 01, 2016
 
Full Example
------------
An example of using this library is available in the folder : [Demo](demo/demo_article.php).

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/states

This library requires :

    * PHP 7.1+
    * A PHP autoloader (Composer is recommended)
    * Teknoo/Immutable (for Automated features).
    
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

From the version 3.2, the internal api has been redesigned to
* Following #East programming rules.
* Remove all public "getter" able to return the internal state of the object.
* Clean dead code and simplify the behavior of the library.
* Method are bound and executed by states managing object instead of object itself, but result is injected into the object.
* This behavior allows developers to execute several implementations for a called method (but only one result must be injected).
* Import from the extension teknoo/states-life-cyclable all automated feature. This implementation follows also the #east programming.
* teknoo/states-life-cyclable is deprecated and not compatible with this library since 3.2.

From the version 3.1, this library provide base implementation for doctrine from teknoo/statesBundle.
* teknoo/statesBundle is deprecated and not compatible with this library since 3.1.

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
Teknoo Software - <https://teknoo.software>

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
