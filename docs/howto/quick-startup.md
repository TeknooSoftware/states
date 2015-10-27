#Teknoo Software - States library - Quick Startup

##Presentation

###Requirements
This library works with PHP 5.4 and newer. It does not require external library except a Dependency Injection library.
States uses, by default, Pimple as DI, but States provides an interface to allow you to use your own DI library.
The DI library to use is defined into the bootstrap. It is described in a followed chapter.

##Write your first stated class
**All following instructions are illustrated in the demo available in the folder `demo` at the root of this library.**

###Load the library
To load the library, you can include the file `bootstrap.php` located in the folder `/src/Teknoo/States` of this library.
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


###Prepare folders
The files of your stated classes must be placed into folder called with the same name as the stated class.
A subfolder, called `States` must be added in these folders. It will welcome states files.

###Create factory
The factory is a mandatory file. It used by the loader to determine the stated class and load it. The factory must be defined
in the file `Factory.php`. The factory class must be called `Factory` (independently of the stated class's name)
and must implement the interface `\Teknoo\States\Factory\FactoryInterface`.

To help you, your factory class can extend the embedded factories `\Teknoo\States\Factory\Integrated` or
 `\Teknoo\States\Factory\Standard`.

###Create states and the default state
The states must be declared in separate files. A state is represented by PHP class and must implement the interface
`\Teknoo\States\State\StateInterface`. To help you, you can use the trait `\Teknoo\States\State\StateTrait` or
directly extend the class `\Teknoo\States\State\AbstractState`.

Each stated class must have at least one state and this state must be called `StateDefault`. It is the default state enabled
in the constructor of each stated class instance.

###Create the proxy
You can define it in the file `<StatedClassName>.php`. The proxy class must be called
 with the same name as the stated class and must implement the interface `\Teknoo\States\Proxy\ProxyInterface`.

To help you, you can use the trait `\Teknoo\States\Proxy\ProxyTrait` or directly extend one of these implementations :
`\Teknoo\States\Proxy\Standard` or `\Teknoo\States\Proxy\Integrated`. *Warning, if you use the factory
`\Teknoo\States\Factory\Integrated`, you must extend the proxy `Integrated`, else, you must extend the proxy `Standard`.*

The trait proxy is already compliant with standard interfaces `\Serializable`, `\ArrayAccess`, `\SeekableIterator`,
`\Countable` and magic methods. But theirs are not implemented in proxy's implementation.

To enable them, you must implement these interface, according to your needs, 
and add traits (available in \Teknoo\States\Proxy\*) in your proxy

###Enjoy
Now, you can use your stated class. If you use the integrated proxy, you can directly instantiate your objects with the
operator `new` like this `$myObject = new \Your\NameSpace\YourStateName();`.

It is not needed to call the directly the proxy class like this `$myObject = new \Your\NameSpace\YourStateName\YourStateName();`,
you can directly use the stated class name. The factory has created an alias from `\Your\NameSpace\YourStateName`
to `\Your\NameSpace\YourStateName\Your\NameSpace\YourStateName`

###CLI Helper
A CLI helper is available at /bin/console.php to create easily new stated class (standard or integrated), 
create new state, and extract state information from your stated class.
