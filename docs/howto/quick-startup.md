#Teknoo Software - States library - Quick Startup

##Presentation

###Requirements
This library works with PHP 7.4 and newer. It does not require external library except a Composer.

##Write your first stated class
**All following instructions are illustrated in the demo available in the folder `demo` at the root of this library.**

###Create states and the default state
The states must be declared in separate files. A state is represented by PHP class and must implement the interface
`\Teknoo\States\State\StateInterface`. To help you, you can use the trait `\Teknoo\States\State\StateTrait` or
directly extend the class `\Teknoo\States\State\AbstractState`.

If the classname of the state is `StateDefault`, it will be automatically enabled at proxy's instantiating.

###Create the proxy
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

###Enjoy
Now, you can use your stated class. You can directly instantiate your objects with the operator `new` like this
 `$myObject = new \Your\NameSpace\YourStatedClass();`.
