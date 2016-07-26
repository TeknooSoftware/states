#Teknoo Software - States library - Behavior

This library is built on three features, added in PHP 5.4 and 5.6, updated in PHP 7 :

*   `ReflectionMethod::getClosure()` to extract dynamically a class's method as a closure with the reflection API (`\ReflectionMethod`). 
*   `Closure::bind()Ti` To rebind the closure to reference $this to the object instance.
*   The new operator `...` to unpack quickly argument passed by `__call()`    

Used collectively, these three methods allow developers to add dynamically methods on objects, the variable `$this`
referencing to these objects.

This library reuses this behavior to implement states. A stated class is a virtual PHP class, composed of several
standard PHP classes :

*   one standard PHP class by state, located in the subfolder `States`.
*   a central standard PHP class, called proxy, used to represent stated class instances (from stated classes). 
    $this referencing these proxy.
*   a third standard PHP class, the factory, called to load the stated class and initialize each stated class instance.

When a stated class is being initialized by the AutoLoader mechanism, the factory load states and proxy.
All stated classes are registered into the proxy during it's initialisation by the factory. A proxy must be constructed
 by a factory with the default implementation, but can be instantiate wit the `new` operator with the integrated implementation.
Closures are extracted and cached on the demand during the first call, $this is bounded automatically by php at first call.
