#Uni Alteri - States library - Quick Startup

##Presentation

###Requirements
This library works with PHP 5.4 and newer. It does not require external library except a Dependency Injection library.
States uses, by default, Pimple as DI, but States provides an interface to allow you to use your own DI library.
The DI library to use is defined into the bootstrap. It is described in a followed chapter.

###Introduction
This library allows you to write more readable code : You can segment your logic in many states that require your class :

*   No useless methods in accordance with your objects' states.
*   No several conditions in your methods to manage different states.

A stated class is written following these instructions :

*   a folder called `States` containing all states, at least the state called `StateDefault`.
*   one state by file, each state class must implement the interface `\UniAlteri\States\State\StateInterface`.
*   one class by states, into separated file.
*   the factory, defined in file Factory.php in the root folder of the stated class.
*   the factory must implement the interface `\UniAlteri\States\Factory\FactoryInterface`
*   optionally a proxy, defined in the file <StatedClass Name>.php in the root folder of the stated class, used by all objects of your stated class as "$this".

These elements will be detailed in the following chapters.

###Factory
The factory is an essential component of each stated class : it loads all php files of a stated class.
A stated class is composed of many php classes, the factory manage the uniqueness of the whole, to present each
stated class as a standard php class.

This library provides a specific autoloader to detect and load all stated classes. This behavior is implemented
by `\UniAlteri\States\Loader\LoaderStandard` and is defined by the interface `\UniAlteri\States\Loader\LoaderInterface`
This loader is automatically registered by calling the file `bootstrap.php`.
It is available into the root folder of this library.

All factory must be a class, called `FactoryClass`, defined in the file `Factory.php` and implementing the interface
`\UniAlteri\States\Factory\FactoryInterface`. The main mandatory method is `initialize()`. It is called by the loader when
your stated class has been detected by the loader and it loads the factory to initialize your stated class.

The method `startup` is called by all new objects of your stated class (in their constructors) to prepare
the proxy, register all states and the DI container.

This library provides a default implementation of this interface with the trait `\UniAlteri\States\Factory\FactoryTrait`.
`\UniAlteri\States\Factory\Standard` is the default factory, implementing this interface and uses this trait.

The method `build` can be used to create a new instance of your stated class, like with the operator `new` for standard
classes. Another way is provided with the integrated factory (described in the section Integrated proxy and Integrated factory).

###Proxy
The proxy is the central component of each stated class : it is the instantiated php class to represent your stated object.
All your states are registered into each proxy by the factory. The proxy manages also these states and allow you to
enable, disable or switch between your states.

All method calls to the proxy are forwarded to enabled states, however the php keyword `$this` represents always your
stated object, aka the proxy and its states.

Your proxy can have its own methods and attributes, like all another standard PHP classes, but these methods and attributes
will be not impacted by the states management and will be always available (according visibility of these methods and attributes).

Proxy must be called as the stated class's name and must be written into the file `<StatedClassName>.php`.
The proxy must implement the interface `\UniAlteri\States\Proxy\ProxyInterface`.

The library provides a default implementation with the trait `\UniAlteri\States\Proxy\ProxyTrait`.
`\UniAlteri\States\Proxy\Standard` is the default proxy class, implementing this interface and uses this trait.

By default, Proxy are not mandatory to create a stated class : if the factory cannot find the proxy, it will use the default proxy
`\UniAlteri\States\Proxy\Standard` and creates a class alias with the php method `class_alias` from the default proxy
with the name of your stated class to simulate an implementation. But in other implementation, the proxy may be mandatory.
This is the case with the Integrated proxy.

With the default proxy, all stated class cannot be instantiated with the operator `new`. You must use the method `build`
 of your factory. Unlike  with the proxy `\UniAlteri\States\Proxy\Integrated`, the operator `new` is available
 (described in the section Integrated proxy and Integrated factory).

###States
A state is a logic representation in your business class to segment your code based on the behavior and states of your objects.

All your states must be stored into the folder `States` of your each stated class folder. They must be only one state by file.
The name of the file is the name of the state. Each state must implement the interface `\UniAlteri\States\State\StateInterface`.
A default implementation of this library is available with the trait `\UniAlteri\States\State\StateTraits`. Your state can use
directly this trait or inherit the abstract class `\UniAlteri\States\State\AbstractState`.

In your state, you can use the keyword `$this` to refer to your stated object, like with a standard PHP object, even if the
method called is available in another state. States' methods are directly used in the proxy, the state class has not
existence independent : the keyword `$this` used in state's method represent the stated object (via the proxy) and not the state.

Visibilities (private, protected and public) are also available in states and they have the same behavior as in standard PHP classes.

Your stated classed must be defined at least one state : It is the state called `StateDefault`. It is the state automatically enabled
when a new stated object is instantiated.

*Your stated object can execute several states in same time, but two enabled states cannot implement the same method, they must
be alternately enabled or the required state must be defined in the called method name, prefixed by "Of", like `$this->myMethodOfMyState`.*

###Integrated proxy and Integrated factory
Default implementations of the factory and the proxy are not fully usable to manage its stated objects like standard php objects :
You cannot use the operator `new` to create a new instance of your stated class.

This library provides also another implementation, called `integrated` to allow you to do this. They must be used together :

*   `\UniAlteri\States\Factory\Integrated` for the factory
*   `\UniAlteri\States\Proxy\Integrated` for the proxy

This new behavior is built on a second factory, defined by the interface `\UniAlteri\States\Factory\StartupFactoryInterface`.
It is used in a static use, the startup factory will be never instantiated. The startup factory class to use is defined
in the proxy class in the static attribute `$startupFactoryClassName`.

By default, the proxy `\UniAlteri\States\Proxy\Integrated` is configured to use `\UniAlteri\States\Factory\StandardStartupFactory`.

The behavior of the startup factory (SF) behavior is as follows :

*   The factory or the stated class is registered into the SF the current stated class with the factory instance to use.
*   The proxy, in the constructor, call the SF and passes itself : the SF follows the call to the good factory in accordance with the stated class.

*Warning : With the Integrated implementation, the proxy must be always defined in your stated class. You can inherits the
implementation `\UniAlteri\States\Proxy\Integrated` without complete it.*

##Write your first stated class
**All following instructions are illustrated in the demo available in the folder `demo` at the root of this library.**

###Load the library
To load the library, you can include the file `bootstrap.php` located in the folder `/src/UniAlteri/States` of this library.
It does it :

*   This bootstrap file instantiate a new DI Container from the implementation of the library.
*   Creates the service to build a new finder (object to locate files of each stated class) for each stated class. (The service is a closure).
*   Registers the previous service in the DI Container.
*   Creates the service to build a new injection closure (object to manage methods of states) and registers in the DI Container.
*   Instantiates a new loader instance. (object called by spl_autoload to detect stated class).
*   Registers the loader in the stack __autoload.

*Warning : the library require also a `autoloader PSR-0` or `PSR-4` (http://www.php-fig.org/psr/psr-0/). By default, this lib use the 
PSR-4 autoloader of Composer, but an implementation is available in the root of this project (file called `autoloader_psr0.php`), 
if it has been detected, it is automatically loaded by the file `bootstrap.php` if composer is unavailable. 
Else you can use another implementation.*

###Configure autoloader
The library is now loaded but it is not known where yours stated classes are localized. By default, the loader instantiated
in the file bootstrap uses paths defined in the php configuration option `include_path` to find stated class.

The loader can also register namespaces with their locations. If one of these namespace is detected during an
autoloading, namespace's locations will be checked before `include_path`. A namespace can accept several paths. To register
a namespace, you must use the method `registerNamespace` of the loader.

The loader instantiated in the bootstrap file is returned when it is included with statements
`include`, `include_once`, `require` or `require_once`.

###Prepare folders
The files of your stated classes must be placed into folder called with the same name as the stated class.
A subfolder, called `States` must be added in these folders. It will welcome states files.

###Create factory
The factory is a mandatory file. It used by the loader to determine the stated class and load it. The factory must be defined
in the file `Factory.php`. The factory class must be called `FactoryClass` (independently of the stated class's name)
and must implement the interface `\UniAlteri\States\Factory\FactoryInterface`.

To help you, your factory class can extend the embedded factories `\UniAlteri\States\Factory\Integrated` or
 `\UniAlteri\States\Factory\Standard`.

###Create states and the default state
The states must be declared in separate files. A state is represented by PHP class and must implement the interface
`\UniAlteri\States\State\StateInterface`. To help you, you can use the trait `\UniAlteri\States\State\StateTrait` or
directly extend the class `\UniAlteri\States\State\AbstractState`.

Each stated class must have at least one state and this state must be called `StateDefault`. It is the default state enabled
in the constructor of each stated object.

###Create the proxy
The proxy is mandatory to use a stated class only if we use the Integrated implementation.
If there are no defined proxy, the embedded proxy `\UniAlteri\States\Proxy\Standard`  will be used. (the proxy is defined by the factory).

But if you need to add some features to your proxy, you can define it in the file `<StatedClassName>.php`. The proxy class must be called
 with the same name as the stated class and must implement the interface `\UniAlteri\States\Proxy\ProxyInterface`.

To help you, you can use the trait `\UniAlteri\States\Proxy\ProxyTrait` or directly extend one of these implementations :
`\UniAlteri\States\Proxy\Standard` or `\UniAlteri\States\Proxy\Integrated`. *Warning, if you use the factory
`\UniAlteri\States\Factory\Integrated`, you must extend the proxy `Integrated`, else, you must extend the proxy `Standard`.*

The trait proxy is already compliant with standard interfaces `\Serializable`, `\ArrayAccess`, `\SeekableIterator` and
`\Countable` : methods of these interfaces are already implemented! But to avoid errors in the usage of this lib, these
interfaces are not defined with released proxies. You must implement these interface, according to your needs, in your
derived proxies.

###Inheritance
Since versions 1.2 and 2.0, the library States supports inheritance of stated classes. The behavior is inspired by 
the PHP behavior with traditional classes :
    - All publics and privates methods defined in the parent class are available in descendant classes
    - Privates methods defined in the parent class are only available by other methods also defined in the parent class.
    - The list of available methods is the set of defined method in parents classes and in the child class.
    - A child class can redefine or overload a parent's method : Redefined methods must only have a compatible interface 
    with the original method : Additional arguments must be optional.
        
But, the behavior is completed for states :
    - The list of available states is the set of defined state in parents classes and in the child class.
    - A child class can overload a state defined in the parent class : The list of methods can be different.
    - A child class can extend a state defined in the parent class : The state PHP class must extend the original PHP state class.

###Enjoy
Now, you can use your stated class. If you use the integrated proxy, you can directly instantiate your objects with the
operator `new` like this `$myObject = new \Your\NameSpace\YourStateName();`.

It is not needed to call the directly the proxy class like this `$myObject = new \Your\NameSpace\YourStateName\YourStateName();`,
you can directly use the stated class name. The factory has created an alias from `\Your\NameSpace\YourStateName`
to `\Your\NameSpace\YourStateName\Your\NameSpace\YourStateName`

###CLI Helper
A CLI helper is available at /bin/console.php to create easily new stated class (standard or integrated), 
create new state, and extract state information from your stated class.