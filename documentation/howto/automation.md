Teknoo Software - States library - Automation
=============================================

Presentation
------------
The automation is an optional feature of States to automatically enable and disable states of an object, according
to values of its properties or thanks to callbacks.

Your rules are defined with assertions : each assertion is associated with a list of states, enabled when the assertion
is valid. States provides two assertions :

* `Property` to check values of properties of the object with constraints,
* `Callback` to delegate the check to a callable.

You can also write your own assertions by implementing the interface
`Teknoo\States\Automated\Assertion\AssertionInterface`.

Implementation
--------------
Your proxy class must implement the interface `Teknoo\States\Automated\AutomatedInterface` and use the trait
`Teknoo\States\Automated\AutomatedTrait`.

### With attributes

Assertions are declared on the proxy class with attributes of the namespace `Teknoo\States\Attributes\Assertion`.

    use Teknoo\States\Attributes\Assertion\Callback as CallbackAssertion;
    use Teknoo\States\Attributes\Assertion\Property as PropertyAssertion;
    use Teknoo\States\Attributes\StateClass;
    use Teknoo\States\Automated\Assertion\AssertionInterface;
    use Teknoo\States\Automated\Assertion\Property\IsEqual;
    use Teknoo\States\Automated\Assertion\Property\IsNotNull;
    use Teknoo\States\Automated\AutomatedInterface;
    use Teknoo\States\Automated\AutomatedTrait;
    use Teknoo\States\Proxy\ProxyInterface;
    use Teknoo\States\Proxy\ProxyTrait;

    #[StateClass([English::class, French::class, Adult::class])]
    #[PropertyAssertion(English::class, ['country', IsEqual::class, 'en'])]
    #[PropertyAssertion(French::class, ['country', IsNotNull::class], ['country', IsEqual::class, 'fr'])]
    #[CallbackAssertion(Adult::class, 'checkIsAdult')]
    class Person implements ProxyInterface, AutomatedInterface
    {
        use ProxyTrait;
        use AutomatedTrait;

        private ?string $country = null;

        private int $age = 0;

        public function __construct()
        {
            $this->initializeStateProxy();
        }

        public function checkIsAdult(ProxyInterface $person, AssertionInterface $assertion): void
        {
            if ($this->age >= 18) {
                $assertion->isValid();
            }
        }
        //....
    }

* `#[PropertyAssertion(states, [property, constraint class, ...arguments of the constraint], ...)]` : states can be a
  class name or a list of class names. Each following array is a constraint on a property. All constraints must be
  valid.
* `#[CallbackAssertion(states, callback)]` : the callback is the name of a **public** method of the proxy, or a
  callable usable in an attribute (name of a function, `[ClassName::class, 'staticMethod']`). The callable gets the
  proxy and the assertion, and must call `$assertion->isValid()` to enable states. A method of the proxy is always
  preferred to a PHP function with the same name.
* `#[Teknoo\States\Attributes\Assertions(inheritsFromParent: false)]` : by default, assertions declared on parent
  classes are inherited. This attribute disables this behavior for a class.

### With the method `listAssertions`

Assertions can also be returned by a protected method `listAssertions`, when they can not be written in attributes
(closures, values computed at runtime). Both ways can be used together.

    use Teknoo\States\Automated\Assertion\Callback;
    use Teknoo\States\Automated\Assertion\Property;
    use Teknoo\States\Automated\Assertion\Property\IsEqual;

    protected function listAssertions(): array
    {
        return [
            (new Property([English::class]))
                ->with('country', new IsEqual('en')),
            (new Property([French::class]))
                ->with('country', new IsEqual('fr')),
            (new Callback([Adult::class]))
                ->call(function (Person $person, AssertionInterface $assertion): void {
                    if ($this->age >= 18) {
                        $assertion->isValid();
                    }
                }),
        ];
    }

Each assertion is associated with a set of states to enable and several constraints (one constraint per call to `with`).

Constraints on properties
-------------------------
All constraints are available in the namespace `Teknoo\States\Automated\Assertion\Property`.

| Constraint                                                        | Valid when the value of the property                                                         |
|:------------------------------------------------------------------|:---------------------------------------------------------------------------------------------|
| `IsNull`, `IsNotNull`                                             | is null / is not null                                                                        |
| `IsEmpty`, `IsNotEmpty`                                           | is empty / is not empty, like the PHP's function `empty()`                                   |
| `IsEqual($value)`, `IsNotEqual($value)`                           | is equal (`==`) / not equal (`!=`) to the expected value                                     |
| `IsSame($value)`, `IsNotSame($value)`                             | is identical (`===`) / not identical (`!==`)                                                 |
| `IsGreaterThan($value)`, `IsGreaterOrEqualThan($value)`           | is greater than / greater than or equal to the expected value                                |
| `IsLessThan($value)`, `IsLessOrEqualThan($value)`                 | is less than / less than or equal to the expected value                                      |
| `IsInstanceOf($class)`, `IsNotInstanceOf($class)`                 | is an instance of the class / is not an instance                                             |
| `IsArray`, `IsScalar`, `IsString`                                 | is an array / a scalar / a string                                                            |
| `HasKey($key)`                                                    | is an array owning this key                                                                  |
| `HasEmptyValueForKey($key)`                                       | is an array owning this key, with an empty value                                             |
| `HasNotEmptyValueForKey($key)`                                    | is an array owning this key, with a non empty value                                          |
| `CountsEqual($count)`, `CountsLess($count)`, `CountsMore($count)` | is an array or a `\Countable` with exactly / less than / more than `$count` elements         |
| `Callback($callable)`                                             | the callable, called with the value and the constraint, calls `$constraint->isValid($value)` |

You can write your own constraints by implementing the interface `ConstraintInterface`, or by extending the class
`AbstractConstraint`.

* Constraints of a property are checked in theirs declaration order, and the check is stopped at the first invalid
  constraint : first constraints can be guards for the next ones (`IsArray` then `HasKey`, `IsNotNull` then `Callback`).
* A property not defined, or not yet initialized, has the value `null`. There is no error if the name of the property
  is wrong : the constraint `IsNull` will be valid.
* Properties are read by the proxy itself : private properties of a child class are not readable when the trait
  `AutomatedTrait` is used by its parent class.

Run assertions
--------------
To avoid a performance cost at each update of a property, States never runs assertions by itself.
There must be an explicit call to `updateStates()`, without any argument. This method can be called in a state, in the
proxy or outside the stated class. It disables all states, then enables states of all valid assertions.

Assertions are compiled once by instance, at the first call to `updateStates()`. A clone compiles its own assertions.
They are not kept by the instance itself : two instances with same values stay equal (`==`, `assertEquals()`), and an
automated instance stays serializable.
