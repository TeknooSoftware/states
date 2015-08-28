#Uni Alteri - States library - Details

##Presentation

###Requirements
This library works with PHP 7 and newer. It does not require external library except a Composer.

###Introduction
This library allows you to write more readable code,
 
It allows you to follow and implement the  [State Pattern](http://en.wikipedia.org/wiki/State_pattern)
 and to create easily and cleanly classes with several states, written in distinct codes blocks  :

*   No useless methods in accordance with your objects' states.
*   No several conditions in your methods to manage different states.

A stated class is written following these instructions :

*   a folder called `States` containing all states, at least the state called `StateDefault`.
*   one state by file, each state class must implement the interface `\UniAlteri\States\State\StateInterface`.
*   one class by states, into separated file.
*   the factory, defined in file Factory.php in the root folder of the stated class.
*   the factory must implement the interface `\UniAlteri\States\Factory\FactoryInterface`
*   optionally a proxy, defined in the file <StatedClass Name>.php in the root folder of the stated class, 
    used by all objects of your stated class as "$this".

These elements will be detailed in the following chapters.

###Factory
The factory is an essential component of each stated class : it loads all php classes of a stated class.
A stated class is composed of many php classes, the factory manages the stated class loading and present each
stated class as a standard php class.

This library provides a specific autoloader, built on Composer to detect and load all stated classes. 
This behavior is implemented by `\UniAlteri\States\Loader\LoaderComposer` and is defined 
by the interface `\UniAlteri\States\Loader\LoaderInterface`.
This loader is automatically registered by calling the file `bootstrap.php`, it is available into the root folder of this library.

All factory must be a class, called `Factory`, defined in the file `Factory.php` and implementing the interface
`\UniAlteri\States\Factory\FactoryInterface`. The main mandatory method is `initialize()`. It is called by the loader when
your stated class has been detected by the loader and it loads the factory to initialize your stated class.

The method `startup` is called by all new objects of your stated class (in their constructors) to prepare
the proxy, register all states.

This library provides a default implementation of this interface with the trait `\UniAlteri\States\Factory\FactoryTrait`.
`\UniAlteri\States\Factory\Standard` is the default factory, implementing this interface and uses this trait.

The method `build` can be used to create a new instance of your stated class, like with the operator `new` for standard
classes. Another way is provided with the integrated factory (described in the section Integrated proxy and Integrated factory).

###Proxy
The proxy, called `Context` in the state pattern, is the central component of each stated class : 
The proxy instance represents the instance of your stated class. It manages states and decide which states to execute calls.

All your states are registered into each proxy by the factory. The proxy manages also these states and allow you to
enable, disable or switch between your states.

All undefined methods called to the proxy are forwarded to enabled states, 
however the php keyword `$this` represents always your stated class instance, aka the proxy and its states.

Your proxy can have its own methods and attributes, like all another standard PHP classes, but these methods and attributes
will be not impacted by the states management and will be always available (according visibility of these methods and attributes).

Proxy must be called as the stated class's name and must be written into the file `<StatedClassName>.php`.
The proxy must implement the interface `\UniAlteri\States\Proxy\ProxyInterface`.

The library provides a default implementation with the trait `\UniAlteri\States\Proxy\ProxyTrait`.
`\UniAlteri\States\Proxy\Standard` is the default proxy class, implementing this interface and uses this trait.

By default, Proxy are not mandatory to create a stated class : if the factory cannot find the proxy, it will use the default proxy
`\UniAlteri\States\Proxy\Standard` and creates a class alias with the php method `class_alias.

With this implementation, all stated class cannot be instantiated with the operator `new`. You must use the method `build`
 of your factory. Unlike  with the `\UniAlteri\States\Proxy\Integrated` implementations, the operator `new` is available
 (described in the section Integrated proxy and Integrated factory).

###States
A state is a logic representation in your business class to segment your code based on the behavior and states of your objects.

All your states must be stored into the folder `States` of your each stated class folder. They must be only one state by file.
The name of the file is the name of the state. Each state must implement the interface `\UniAlteri\States\State\StateInterface`.
A default implementation of this library is available with the trait `\UniAlteri\States\State\StateTraits`. Your state can use
directly this trait or inherit the abstract class `\UniAlteri\States\State\AbstractState`.

In your state, you can use the keyword `$this` to refer to your stated class instance, like with a standard PHP object, even if the
method called is available in another state. States' methods are directly used in the proxy, the state class has not
independent existence : the keyword `$this` used in state's method represent the stated class instance and not the state.

Visibilities (private, protected and public) are also available in states and they have the same behavior as in standard PHP classes.

Your stated classed can has no defined states.
 
If the state `StateDefault` is present, it will be automatically enable at startup.

*Your stated class instance can execute several states in same time, but two enabled states cannot implement the same method, they must
be alternately enabled or the required state must be defined in the called method name, prefixed by "Of", like `$this->myMethodOfMyState`.*

###Stated class and Inheritance
Stated classes support inheritance with other stated classes and natural PHP classes:

Extends another stated class:

*   The proxy of the child stated class must extends the proxy of the mother stated class.

Extends a native PHP class:

*   The proxy must extends the native PHP class, 
*   Your proxy must implement the interface `\UniAlteri\States\Proxy\ProxyInterface` and use the trait `\UniAlteri\States\Proxy\ProxyTrait`
*   If you use an Integrated stated class (next chapter), your proxy must also implement `\UniAlteri\States\Proxy\IntegratedInterface` 
    and use the trait `\UniAlteri\States\Proxy\IntegratedTrait`

The behavior is inspired by the PHP behavior with native PHP classes :
    - All publics and privates methods defined in the parent class are available in descendant classes
    - Privates methods defined in the parent class are only available by other methods also defined in the parent class.
    - The list of available methods is the set of defined method in parents classes and in the child class.
    - A child class can redefine or overload a parent's method : Redefined methods must only have a compatible interface 
    with the original method : Additional arguments must be optional.
        
But, the behavior is completed for states :
    - The list of available states is the set of defined state in parents classes and in the child class.
    - A child class can overload a state defined in the parent class : The list of methods can be different.
    - A child class can extend a state defined in the parent class : The state PHP class must extend the original PHP state class.

###Integrated proxy and Integrated factory
Default implementations of the factory and the proxy are not fully usable to manage its stated class instance like standard php objects :
You cannot use the operator `new` to create a new instance of your stated class.

This library provides another implementation, called `integrated` to allow you to do this. They must be used together :

*   `\UniAlteri\States\Factory\Integrated` for the factory
*   `\UniAlteri\States\Proxy\Integrated` for the proxy

This new behavior is built on a second type of factory, defined by the interface `\UniAlteri\States\Factory\StartupFactoryInterface`.
It is used in a static context, the startup factory will be never instantiated. The startup factory class to use is defined
in the proxy class by the static attribute `$startupFactoryClassName`.

By default, the proxy `\UniAlteri\States\Proxy\Integrated` is configured to use `\UniAlteri\States\Factory\StandardStartupFactory`.

The behavior of the startup factory (SF) behavior is as follows :

*   The factory of the stated class is registered into the SF by the factory.
*   The proxy, in the constructor, call the SF and passes itself : the SF follows the call to the good factory.

*Warning : With the Integrated implementation, the proxy must be always defined in your stated class. You can inherits the
implementation `\UniAlteri\States\Proxy\Integrated` without complete it.*
