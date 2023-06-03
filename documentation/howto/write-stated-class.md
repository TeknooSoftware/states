Teknoo Software - States library - Write a stated class
=======================================================

Presentation
------------

### Requirements
This library works with PHP 8.1 and newer. It does not require external library excepts a Composer and Immutable.

Write your first stated class
-----------------------------
**All following instructions are illustrated in the demo available in the folder `demo` at the root of this library.**

### Create states and the default state
The states must be declared in separate files. A state is represented by PHP class and must implement the interface
`\Teknoo\States\State\StateInterface`. To help you, you can use the trait `\Teknoo\States\State\StateTrait` or
directly extend the class `\Teknoo\States\State\AbstractState`.

When the class name of the state is `StateDefault`, it will be automatically enabled at proxy's instantiating.

### Create the proxy
The proxy class must be called with the same name as the stated class and must implement 
the interface `\Teknoo\States\Proxy\ProxyInterface`.

To help you, you can use the trait `\Teknoo\States\Proxy\ProxyTrait` or directly extend the implementation :
`\Teknoo\States\Proxy\Standard`

The trait proxy can be compliant with standard interfaces `\Serializable`, `\ArrayAccess`, `\SeekableIterator`,
`\Countable` and magic methods. To enable it, use also trait `ArrayAccessTrait`, `IteratorTrait`, `MagicCallTrait` and 
`SerializableTrait` available in the namespace `\Teknoo\States\Proxy`.

All states class must be declared into this proxy class via the static method `statesListDeclaration()`, like here :

    public static function statesListDeclaration(): array
    {
        return [
            StateClassName1::class,
            StateClassName2::class
        ];
    }

### Set the constructor
If your proxy class use directly the trait instead of extends the Standard implementation, you must initialize your 
stated object at is creation by calling the protected method `initializeStateProxy`.

    public function __construct()
    {
        //Call the method of the trait to initialize local attributes of the proxy
        $this->initializeStateProxy();
    }

With Doctrine ORM/ODM implementation, you must also set a callback on the method `postLoadDoctrine` for the `postLoad` 
event.

### Enjoy
Now, you can use your stated class. You can directly instantiate your objects with the operator `new` like this
 `$myObject = new \Your\NameSpace\YourStatedClass();`.

Use your stated class
---------------------

To enable a state, call the method `enableState` with the full qualified name of the state.

    $motherInstance->enableState(StateOne::class);

To disable :

    $motherInstance->disableState(StateOne::class);

To perform this two operations in a single method :

    $motherInstance->switchState(StateOne::class);

To know if a least one state is active or inactive :

    $motherInstance->isInState([StateOne::class], fn () => doAnyThing());
    $motherInstance->isNotInState([StateOne::class], fn () => doAnyThing());

To know if all states ares actives or inactives :

    $motherInstance->isInState([StateOne::class], fn () => doAnyThing(), true);
    $motherInstance->isNotInState([StateOne::class], fn () => doAnyThing(), true);
