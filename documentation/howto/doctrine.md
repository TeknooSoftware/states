Teknoo Software - States library - Doctrine
===========================================

Presentation
------------
Doctrine ORM and ODM create your entities and documents without calling theirs constructors : states of a stated class
are then not loaded. This library provides, in the namespace `Teknoo\States\Doctrine` :

* the trait `StandardTrait`, a `ProxyTrait` for Doctrine's objects,
* the abstract class `AbstractStandardObject`, implementing `ProxyInterface` with this trait.

They require `doctrine/orm` or `doctrine/mongodb-odm` in your project, they are not dependencies of this library.

Write your entity or your document
----------------------------------
Extend `AbstractStandardObject`, or implement `ProxyInterface` with the `StandardTrait` and call the method
`postLoadDoctrine()` in your constructor. This method initializes the proxy, then calls `updateStates()`.

Doctrine must call this same method after loading an object : map it to the `postLoad` lifecycle event.

    use Doctrine\ORM\Mapping as ORM;
    use Teknoo\States\Attributes\StateClass;
    use Teknoo\States\Doctrine\AbstractStandardObject;

    #[ORM\Entity]
    #[ORM\HasLifecycleCallbacks]
    #[StateClass([Draft::class, Published::class])]
    class Article extends AbstractStandardObject
    {
        #[ORM\PostLoad]
        public function onPostLoad(): void
        {
            $this->postLoadDoctrine();
        }

        //....
    }

With XML mapping, for ORM and ODM :

    <lifecycle-callbacks>
        <lifecycle-callback type="postLoad" method="postLoadDoctrine"/>
    </lifecycle-callbacks>

Without this callback, no state is loaded for objects fetched from the database, and all calls to methods of states
throw an exception `MethodNotImplemented`.

Select states of a loaded object
--------------------------------
`postLoadDoctrine()` calls `updateStates()` to select states according to loaded values. In the `StandardTrait`,
this method does nothing : override it to enable your states

    public function updateStates(): ProxyInterface
    {
        if ($this->publishedAt instanceof DateTimeInterface) {
            $this->switchState(Published::class);
        } else {
            $this->switchState(Draft::class);
        }

        return $this;
    }

or use the [automation](automation.md). Both traits provide a method `updateStates()` : you must select the method of
the `AutomatedTrait`.

    class Article implements AutomatedInterface
    {
        use StandardTrait, AutomatedTrait {
            AutomatedTrait::updateStates insteadof StandardTrait;
        }

        //....
    }

In the constructor of a new object, `updateStates()` is called before your properties are set : call it again when
they are set.

Lazy objects
------------
Objects lazily loaded by Doctrine (native lazy objects of PHP 8.4, or proxies of `ProxyManager`) are initialized by
the `StandardTrait` at the first call to a method of a state.

Serialization
-------------
Stated objects can be serialized, like others stated class instances : enabled states are restored with the object.
Assertions of automated objects are not kept by objects themselves : they stay serializable, even when theirs
assertions use closures.

Credits
-------
EIRL Richard Déloge - <https://deloge.io> - Lead developer.
SASU Teknoo Software - <https://teknoo.software>

License
-------
States is licensed under the 3-Clause BSD License - see the [LICENSE](../../LICENSE) file for details.
