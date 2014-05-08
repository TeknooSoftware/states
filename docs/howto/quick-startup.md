#Uni Alteri - States library - Quick Startup

##Presentation

###Requirements
This library works with PHP5.4 and newer. It does not require external library except a Dependency Injection library.
States use by default the Pimple as DI, but States provides an interface to allow you to use your own DI library.
The DI library to use is defined into the bootstrap. It is described in a followed chapter.

###Introduction
This library allows you to write more readable code : You can split your logic in many state that require your class :

*   Not useless methods in accordance with your objects' states.
*   Not several conditions in your methods to manage different states.

A stated class is made following these instructions :

*   a folder called `States` containing all states, at least the state called `StateDefault`.
*   one state by file, each state class must implement the interface `\UniAlteri\States\States\StateInterface`.
*   one class by states, into separate file.
*   the factory, defined in file Factory.php in the root folder of the stated class, called by library's loader to initialize your stated class.
*   the factory must implement the interface `\UniAlteri\States\Factory\FactoryInterface`
*   optionally a proxy, defined in the file Proxy.php in the root folder of the stated class, used by all objects of your stated class as "$this".

These elements will be detailed in the following chapters.

###Factory
The factory is an essential component of each stated class : it loads all php files of a stated class.
A stated class is composed of many php classes, the factory manage the uniqueness of the whole : to show each
stated class as a standard php class.

This library provides a specific autoloader to detect and load all stated classes. This behavior is implemented
in `\UniAlteri\States\Loader\LoaderStandard` and is defined by the interface `\UniAlteri\States\Loader\LoaderInterface`
This loader is automatically registered by calling the file `bootstrap.php`.
It is available into the root folder of this library.

All factory must be a class, called `FactoryClass`, defined in the file `Factory.php` and implementing the interface
`\UniAlteri\States\Factory\FactoryInterface`. The main mandatory method is `initialize()`. It is called by the loader when
your stated class has been detected by the loader and when it load the factory to initialize your stated class.

The method `startup` is called by all new objects of your stated class (in theirs constructors) to register prepare
the proxy all states and the DI container.

This library provides a default implementation of this interface with the trait `\UniAlteri\States\Factory\TraitFactory`.
`\UniAlteri\States\Factory\Standard` is the default factory, implementing this interface and use this trait.

The method `build` can be use to create a new instance of your stated class, like with the operator `new` with standard
classes. Another way is provided with the integrated factory (described in the section Integrated proxy and Integrated factory).

###Proxy
The proxy is the central component of each stated class : it is the instantiated php class to represent your stated object.
All your states are registered into each proxy by the factory. The proxy manages also these states and allow you to
enable, disable or switch between your states.

All method calls to the proxy are forwarded to enabled states, however the php keyword `$this` represents your
stated object, aka the proxy and its states.

Your proxy can have its owns methods and attributes, like all another standard PHP classes, but these methods and attributes
will be not impacted by the state management and will be always available (according visibility of these methods and attributes).

Proxy must be called as the stated class's name and must be defined into the file `Proxy.php`.
The proxy must implement the interface `\UniAlteri\States\Proxy\ProxyInterface`.

The library provides a default implementation with the trait `\UniAlteri\States\Proxy\TraitProxy`.
`\UniAlteri\States\Proxy\Standard` is the default proxy class, implementing this interface and use this trait.

Proxy are not mandatory to create a stated class : if the factory cannot find the proxy, it will use the default proxy
`\UniAlteri\States\Proxy\Standard` and create a class alias with the php method `class_alias` from the default proxy
with the name of your stated class to simulate an implementation.

With the default proxy, all stated class cannot be instantiated with the operator `new`. You must use the method `build`
 of your factory. Unlike  with the proxy `\UniAlteri\States\Proxy\Integrated`, the operator `new` is available
 (described in the section Integrated proxy and Integrated factory).

###States
A state is a logic representation in your business class to segment your code based on the behavior and states of your objects.

All your states must be stored into the folder `States` of your each stated class folder. They must be only one state by file.
The name of the file is the name of the state. Each state must implement the interface `\UniAlteri\States\States\StateInterface`.
A default implementation of this library is available with the trait `\UniAlteri\States\States\TraitStates`. Your state can use
directly this trait or inherits the abstract class `\UniAlteri\States\States\AbstractState`.

In your state, you can use the keyword `$this` to refer to your stated object, like with a standard PHP object, even if the
called method is available in another state. States' methods are directly used in the proxy, the state class has not independent
existence : the keyword `$this` used in state's method represent the stated object (via the proxy) and not the state.

Scope visibility (private, protected and public) are also available in states and have the same behavior than in standard PHP classes.

Your stated classed must be defined at least one state : It is the state called `StateDefault`. It is the state automatically enable
when a new stated object is instantiated.

Your stated object can execute several states in same time, but two enabled states cannot implement the same method, they must
be enabled alternatively or the required state must be defined in the method call name, prefixed by "Of", like `$this->myMethodOfMyState`.

###Integrated proxy and Integrated factory
Defaults implementations of the factory and the proxy are not fully usable to manage its stated objects like standard php objects :
You cannot use the operator `new` to create a new instance of your stated class.

This library provides also another implementation, called `integrated` to allowing you to do this. They must be used together :

*   `\UniAlteri\States\Factory\Integrated` for the factory
*   `\UniAlteri\States\Proxy\Integrated` for the proxy

This new behavior is built on a second factory, defined by the interface `\UniAlteri\States\Factory\StartupFactoryInterface`.
It is used in a static scope, startup factory will be never instantiated. The startup factory class to use is defined
in the proxy class in the static attribute `$_startupFactoryClassName`.

By default, the proxy `\UniAlteri\States\Proxy\Integrated` is configured to use `\UniAlteri\States\Factory\StandardStartupFactory`.

The behavior of the startup factory (SF) behavior is as follows :
*   The factory or the stated class register into the SF the current stated class with the factory instance to use.
*   The proxy, in the constructor, call the SF and pass it : the SF follows the call to the good factory in accordance
with the stated class of the proxy.

##Write your first stated class
**All following instructions are shown in the demo available in the folder `demo` at the root of this library.**

###Load the library
To load the library, you can include the file `bootstrap.php` locate in the folder `/lib/UniAlteri/States` of this library.
It does it :

*   This bootstrap file instantiate a new DI Container from the implementation of the library.
*   Create the service to build a new finder (object to locate files of each stated class) for each stated class. (The service is a closure).
*   Register the previous service in the DI Container.
*   Create the service to build a new injection closure (object to manage methods of states) and register in the DI Container.
*   Instantiate a new loader instance. (object called by spl_autoload to detect stated class).
*   Register the loader in the stack __autoload.

Warning : the library require also a `PSR-0 autoloader` (http://www.php-fig.org/psr/psr-0/). An implementation is available
in the root of this project (file called `psr0_autoloader.php`), if it has been detected, it is automatically loaded by
the file `bootstrap.php`. Else you can use another implementation.

###Configure autoloader
The library is now loaded but it is not known where your stated class are present. By default, the loader instantiate
in the bootstrap file use path defined in the php configuration option `include_path` to find and load stated class.

The loader can also register specifics namespaces with their locations. If one of these namespace is detected during an
autoloading, namespace's paths will be checked before `include_path`. A namespace can accept several paths. To register
a namespace, you must use the method `registerNamespace` of the loader.

The instance of the loader instantiated in the bootstrap file is returned by itself when it is included with statements
`include`, `include_once`, `require` or `require_once`.

###Prepare folders

###Create factory

###Create the default state

###Add new states

###Optional, create the proxy

###Enjoy


