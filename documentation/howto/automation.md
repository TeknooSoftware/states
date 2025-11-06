Teknoo Software - States library - Automation
=============================================

Presentation
------------
The automation is a bundle extension of States to enable and disable automatically, on a 
method call, several states according to a properties configuration or thanks callbacks.
You can also write your own assertion objects implementing the `AssertionInterface`

States provides only assertions to check a value of a property or call a function 
for a list of property.

The list of available properties assertions are :

* count of a collection (equals, less or more)
* empty / not empty
* is null / is not null
* has key / has not key
* is equal / greater / less / or equals
* same / not same
* instance of / not instance of
* is scalar / string / bool / array

Implementation
--------------
Your proxy class must implements the interface `AutomatedInterface` and use the trait
`AutomatedTrait`.

Your rules must be defined in the method `listAssertions` like this

        protected function listAssertions(): array
        {
            return [
                (new Property([English::class]))
                    ->with('country', new IsEqual('en')),
                (new Property([French::class]))
                    ->with('country', new IsEqual('fr')),
            ];
        }

Each assertion is associated to a set of states to enable (all other states will be disabled) and
several assertions (one assertion per call to `with`). All assertions must be valid.

Run assertions
--------------
To avoid a performance leak, State is not able to run assertions at each property update.
They must be explicit call to `updateStates`, without any arguments. This method can be called
in a state, in the proxy or outside the stated class.

Example
-------

    #[StateClass(English::class)]
    #[StateClass(French::class)]
    #[PropertyAssertion(English::class, ['country', IsEqual::class, 'en'])]
    #[PropertyAssertion(French::class, ['country', IsEqual::class, 'fr'])]
    class Person implements ProxyInterface, AutomatedInterface
    {
        use ProxyTrait;
        use AutomatedTrait;
    
        private string $name;
    
        private string $country;
    
        public function __construct()
        {
            $this->initializeStateProxy();
        }
    //....
    }
