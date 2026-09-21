Teknoo Software - States library - Write methods of states
==========================================================

A method of a state is a builder
--------------------------------
A method of a state is never executed as the method of your stated class. It is a **builder** : a method without
argument, called once by the library, returning the closure to execute at each call.

    class Draft extends AbstractState
    {
        public function publish(): Closure
        {
            return function (DateTimeInterface $at): Article {
                $this->publishedAt = $at;
                $this->switchState(Published::class);

                return $this;
            };
        }
    }

    $article->publish(new DateTimeImmutable());

* The **name** and the **visibility** (public, protected, private) of the builder are the name and the visibility
  of the method of your stated class.
* **Arguments and return type** of the method are arguments and return type of the closure.
* The closure is bound to the proxy : inside it, `$this`, `self` and `static` reference the **stated class instance**
  and its class, never the state. Private properties and private methods of the proxy are available.
* The builder must declare the return type `Closure` : it is required by the [PHPStan extension](phpstan.md) to
  analyse the closure as a method of the proxy.
* For a single expression, the builder can return an arrow function :

      public function getTitle(): Closure
      {
          return fn (): string => $this->title;
      }

* The closure **can be static** (`static function`, `static fn`) when it does not need the instance : PHP forbids to
  bind an object to a static closure, so it is only bound to the scope of the stated class. `$this` is not available,
  but `self` and `static` reference the class of the proxy : its constants and its static properties and methods are
  available, even private ones.

      public function getMaxLength(): Closure
      {
          return static fn (): int => self::MAX_LENGTH;
      }
* A builder has **no argument** and must only build the closure, without side effect : it is executed once by state's
  instance, then its closure is reused. It is only executed when the caller is allowed to call the method.
* Static methods, methods with required arguments, the constructor and methods of the `StateTrait` are ignored : they
  are not methods of your stated class and are not callable from the proxy.
* To help your IDE, add the tag `@mixin YourProxyClass` on the state : `$this` will be understood as your proxy.

Visibility
----------
The visibility of a method is checked from the caller, like PHP does for real methods :

| Caller                                                                                  | Available methods             |
|:----------------------------------------------------------------------------------------|:------------------------------|
| The main script, a function, another class                                              | public                        |
| A child class of the stated class (instance or static method)                           | public and protected          |
| The stated class itself : its methods, its states, another instance, its static methods | public, protected and private |

With inherited stated classes, private methods of a parent's state are only available for methods of this parent class
and of its states, not for children classes.

Methods called by PHP itself through traits of the proxy (`ArrayAccessTrait`, `IteratorTrait`, `MagicCallTrait`,
`SerializableTrait`) are always called from the private scope.

Several states enabled
----------------------
When several states are enabled, a method must be provided by a single enabled state for a given caller, else the
proxy throws an exception `AvailableSeveralMethodImplementations`. When no enabled state provides the method, the proxy
throws an exception `MethodNotImplemented`.

Why builders of closures, and not real methods ?
------------------------------------------------
To execute a method of a state like a method of the proxy, its code must be bound to the proxy : `$this` must be the
stated class instance, and the scope must be its class, to access to its private properties.

PHP can create a closure from a method (`ReflectionMethod::getClosure()`, `$object->method(...)`), but forbids to bind
it to another class. With PHP 8.5 :

    $closure = (new ReflectionMethod(Draft::class, 'publish'))->getClosure($draft);

    Closure::bind($closure, $article, Article::class);
    // Warning: Cannot bind method Draft::publish() to object of class Article, this will be an error in PHP 9

    $closure->bindTo($draft, Article::class);
    // Warning: Cannot rebind scope of closure created from method, this will be an error in PHP 9

    $closure->call($article);
    // Warning: Cannot bind method Draft::publish() to object of class Article, this will be an error in PHP 9

Only closures written with `function () {}` or `fn () =>` can be bound to any object and any scope. This is why a
method of a state returns a closure instead of being directly executed.

Last versions of PHP do not change this behavior :

* **PHP 8.5, closures in constant expressions** : only static closures are allowed, without `$this`.
* **PHP 8.6, partial function application** : closures created by a partial application follow the same rules as
  closures created from methods, they can only be rebound to an object of the same class.
* **PHP 9** : these warnings will become errors.

The only way to write states with real methods would be to transform the source code of states when they are loaded.
This library does not do it : it would break debuggers, OPcache and static analysis for a small benefit.

Credits
-------
EIRL Richard Déloge - <https://deloge.io> - Lead developer.
SASU Teknoo Software - <https://teknoo.software>

License
-------
States is licensed under the 3-Clause BSD License - see the [LICENSE](../../LICENSE) file for details.
