Teknoo Software - States library - Write a stated class
=======================================================

Presentation
------------

### Requirements

This library works with PHP 8.4 and newer. It does not require any external library except Composer and
`teknoo/immutable`.

Write your first stated class
-----------------------------
**All following instructions are illustrated in the demo available in the folder `demo` at the root of this library's
repository.**

### Create states and the default state

The states must be declared in separate files. A state is represented by a PHP class and must implement the interface
`\Teknoo\States\State\StateInterface`. To help you, you can use the trait `\Teknoo\States\State\StateTrait` or
directly extend the class `\Teknoo\States\State\AbstractState`.

Methods of a state are not directly executed : they are builders, returning the closure to execute. It is detailed in
[how to write methods of states](state-methods.md).

    class English extends AbstractState
    {
        public function sayHello(): Closure
        {
            return function (): string {
                return 'Good morning, ' . $this->name;
            };
        }
    }

When the class name of the state is `StateDefault`, it will be automatically enabled at the proxy instantiation.

### Create the proxy

The proxy class is your stated class : the class instantiated by your code, owning properties. It must implement
the interface `\Teknoo\States\Proxy\ProxyInterface`.

To help you, you can use the trait `\Teknoo\States\Proxy\ProxyTrait` or directly extend the abstract implementation
`\Teknoo\States\Proxy\Standard`.

The proxy can also be compliant with standard PHP interfaces and magic methods. To enable them, use also these traits,
available in the namespace `\Teknoo\States\Proxy` : they forward calls to methods of enabled states.

| Trait               | PHP feature                                 | Methods to provide in a state                                                    |
|:--------------------|:--------------------------------------------|:---------------------------------------------------------------------------------|
| `ArrayAccessTrait`  | `\ArrayAccess` and `\Countable`             | `offsetExists`, `offsetGet`, `offsetSet`, `offsetUnset`, `count`                 |
| `IteratorTrait`     | `\SeekableIterator` or `\IteratorAggregate` | `current`, `key`, `next`, `rewind`, `seek`, `valid`, or `getIterator`            |
| `MagicCallTrait`    | `__invoke()` and `__toString()`             | `invoke`, `toString`                                                             |
| `SerializableTrait` | `__serialize()`                             | `__serialize` (declared without return type, PHP requires `array` for this name) |

These traits call themselves the proxy : methods of states called by them are always called from the private scope
of the stated class, whatever the caller.

All state classes must be declared in this proxy class via the attribute `#[Teknoo\States\Attributes\StateClass()]`

    #[StateClass(English::class)]
    #[StateClass(French::class)]

or

    #[StateClass([English::class, French::class])]

or via the deprecated static method `statesListDeclaration()`, like here :

    public static function statesListDeclaration(): array
    {
        return [
            StateClassName1::class,
            StateClassName2::class
        ];
    }

### Set the constructor

If your proxy class uses the trait directly, instead of extending the `Standard` implementation, you must initialize
your stated object at its creation by calling the protected method `initializeStateProxy`.

    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
    }

With Doctrine ORM/ODM implementation, you must also set a callback on the method `postLoadDoctrine` for the `postLoad`
event, see [how to use stated classes with Doctrine](doctrine.md).

### Inherit a stated class

A stated class can be extended. States of parents classes are automatically available in children classes.

* A child class overloads a state of its parent by declaring a state with the same short class name (the class name
  without its namespace). The overloaded state can be referenced with its name or with the name of the parent's state.
* Private methods of a parent's state are only available for methods of states of this parent class, like private
  methods in PHP. Protected methods are available for children classes.

### Enjoy

Now, you can use your stated class. You can directly instantiate your objects with the operator `new` like this
`$myObject = new \Your\NameSpace\YourStatedClass();`.

Use your stated class
---------------------

To enable a state, call the method `enableState` with the full qualified name of the state.

    $motherInstance->enableState(StateOne::class);

To disable :

    $motherInstance->disableState(StateOne::class);

To disable all states :

    $motherInstance->disableAllStates();

To disable all states and enable a single state, in a single method :

    $motherInstance->switchState(StateOne::class);

To know if at least one of these states is active, or if at least one of these states is inactive :

    $motherInstance->isInState([StateOne::class, StateTwo::class], fn (array $enabledStates) => doAnyThing());
    $motherInstance->isNotInState([StateOne::class, StateTwo::class], fn (array $enabledStates) => doAnyThing());

To know if all these states are active, or if all these states are inactive :

    $motherInstance->isInState([StateOne::class, StateTwo::class], fn (array $enabledStates) => doAnyThing(), true);
    $motherInstance->isNotInState([StateOne::class, StateTwo::class], fn (array $enabledStates) => doAnyThing(), true);

The callback is only called when the condition is satisfied, with the sorted list of enabled states. According to
the #East programming rules followed by this library, there is no method returning the states of an object.

### Register states at runtime

A state can also be registered on a single instance, with `registerState`, and removed with `unregisterState`.
A state is registered under a name, used to enable and disable it : its class name, or the name of an interface it
implements. It is mandatory for states defined with an anonymous class, which has no usable name.

    interface FlyingInterface extends StateInterface
    {
    }

    $bird->registerState(
        FlyingInterface::class,
        new class (false, $bird::class) extends AbstractState implements FlyingInterface {
            public function fly(): Closure
            {
                return fn (): string => 'flap flap';
            }
        }
    );
    $bird->enableState(FlyingInterface::class);

The first argument of a state's constructor is its private mode (`true` when the state is owned by a parent class
of the instance : its private methods are then only available for states of this same parent class), the second
argument is the stated class owning the state.

When an enabled state is registered again, the new instance immediately replaces the old one.

### Clone and serialize

A stated class instance can be cloned : states are also cloned, enabled states stay enabled.

It can also be serialized : enabled states are restored with the object. With the `SerializableTrait`, the
serialization is delegated to the method `__serialize` of an enabled state : the restored object is then not
initialized, your class must provide a method `__unserialize()` calling `initializeStateProxy()`.
