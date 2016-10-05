#Teknoo Software - States library - Behavior

This library is built on two specifics features introducing in PHP 7 :
 
*   `Closure::call()` To rebind the closure to reference $this to the object instance.
*   The new operator `...` to unpack quickly argument passed by `__call()`    

Used collectively, these two methods allow developers to add dynamically methods on objects, rebind the variable `$this`
to these objects and static to theirs classnames.

This library reuses this behavior to implement states. A stated class is a virtual PHP class, composed of several
standard PHP classes :

*   one standard PHP class by state, implementing the StateInterface..
*   a central standard PHP class, called proxy, used to represent stated class instances (from stated classes). 
    $this referencing these proxy.
*   Each state class must be declared into the proxy class, via the static method `statesListDeclaration()`.

During proxy instantiating, the proxy finds and loads all states declared.
