#Uni Alteri - States library - Behavior

This library is built on two features added with PHP 5.4 :

*   `ReflectionMethod::getClosure()` to extract dynamically a class's method as a closure with the reflection API.
*   `Closure Closure::bind()` to duplicate a closure with a specific bound object and class scope.

Used collectively, these two methods allow developers to add dynamically methods on objects, the variable `$this`
referencing to these objects.

This library reuses this behavior to implement states. A stated class is a virtual PHP class, composed of several
standard PHP classes :

*   one standard PHP class by state, located in the subfolder `States`.
*   a central standard PHP, called proxy, used to represent stated class instances (from stated classes). $this referencing
    these proxy.
*   a third standard PHP, the factory, called to load the stated class and initialize each stated class instance.

When a stated class is being initialized by its factory and PHP, an object of the proxy class is instantiated and
all stated classes are registered into the proxy. Closures are extracted and bounded with the proxy object on the demand
during the first call.