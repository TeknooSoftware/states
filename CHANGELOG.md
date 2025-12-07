# Teknoo Software - States library - Change Log

## [7.1.2] - 2025-12-08
### Stable Release
- Fix bc break introduced into patch of phpstan and phpunit
- Support PHPStan 2.1.33+
- Support PHPUnit 12.5.1+

## [7.1.1] - 2025-12-02
### Stable Release
- Update dev libraries

## [7.1.0] - 2025-11-06
### Stable Release
- Added the attribute `#[Teknoo\States\Attributes\StateClass()]` to define the state classes of a proxy class, as a
  replacement for the `statesListDeclaration()` method.
- Marked the static method `statesListDeclaration()` as deprecated when it returns a non-empty array. 
  This method will be removed in the next major release.
- Added the attribute interface `Teknoo\States\Attributes\AssertionInterface` to define assertions via attributes 
  instead of using `listAssertions()`.  
  However, unlike `statesListDeclaration()`, the `listAssertions()` method is **not** deprecated, because attributes 
  cannot cover all use cases.
  - Added the attribute `#[Teknoo\States\Attributes\Assertion\Property([StateClassName::class], [with], ...)]`
    - The `[with]` argument must follow the structure
      `['propertyName', ConstraintClass::class, <optional constructor arguments for the constraint>]`.
  - Added the attribute `#[Teknoo\States\Attributes\Assertion\Callback([StateClassName::class], [callable])]`
    - The `[callable]` argument must be either an array referencing a static method, or the name of a method to be 
      called on the proxy.
  - Add the attribute `#[Teknoo\States\Attributes\Assertions]` to configure behavior of automations on inheritance

## [7.0.1] - 2025-09-19
### Stable Release
- Fix some BC break introduced by PHPStan bugfix version.

## [7.0.0] - 2025-07-27
### Stable Release
- Drop support of PHP 8.3
- Upgrade code to PHP 8.4
- Upgrade to PHPUnit 12
- Upgrade to PHPStan 2.1
- Prepare States to PHP 8.5 and Closure in constant
- Switch license from MIT to 3-Clause BSD

## [6.4.3] - 2025-02-07
### Stable Release
- Update dev lib requirements
  - Require Symfony libraries 6.4 or 7.2
- Drop support of PHP 8.2
  - The library stay usable with PHP 8.2, without any waranties and tests
  - In the next major release, Support of PHP 8.2 will be dropped

## [6.4.2] - 2025-07-01
### Stable Release
Fix issue with last Doctrine Proxy behavior, entities and objects's events postLoad is only initialized when a property
is readed

## [6.4.1] - 2024-19-11
### Stable Release
Fixed wrong behavior with the new cache about calls when states are updated from a stated method.

## [6.4.0] - 2024-19-11
### Stable Release
- Add caches in `ProxyTrait` and `StateTrait` about found state for a method name and visibility check when a call
  to not reperform all operations when the context stay unchanged.
    - The visibility cache life is as long as the state object life (because the visibility is immutable at runtime,
      according to caller class.
    - Te call cache is refresh at each state change (enabling, disabling state or cloning object) because the answer
      is not immutable at runtime

## [6.3.2] - 2024-08-27
### Stable Release
- Fix nth BC break (bis in a minor version of PHPStan 1.12+! Fix units tests about PHPStan extension, but there are no
  longer "unit" because PHPStan's teams decided to set all "internal classes" as final (Why ???), even on classes needed
  for extensions...

## [6.3.1] - 2024-05-13
### Stable Release
- Fix nth BC break in a minor version of PHPStan !

## [6.3.0] - 2024-05-07
### Stable Release
- Drop support of PHP 8.1
- Add sensitive parameter attribute on Proxies' `__call` methods and states' `executeClosure` methods to prevent leaks.

## [6.2.0] - 2024-03-22
### Stable Release
- Fix support of last PHPStan 1.10.64
- Remove PHPStan extension NodeScopeResolver
- Add tag `phpstan.parser.richParserNodeVisitor` to ASTVisitor, to limit PHPStan alteration
- Rework `ASTVisitor` to :
  - simplify its behavior
  - avoid infinity loop
  - update migrated statements with type from the closure
  - Fix some bug when a state was analyzed before the proxy
- Support `phpstan/extension-installer`

## [6.2.0-beta1] - 2024-03-22
### Stable Release
- Fix support of last PHPStan 1.10.64
- Remove PHPStan extension NodeScopeResolver
- Add tag `phpstan.parser.richParserNodeVisitor` to ASTVisitor, to limit PHPStan alteration
- Rework `ASTVisitor` to :
  - simplify its behavior
  - avoid infinity loop
  - update migrated statements with type from the closure
  - Fix some bug when a state was analyzed before the proxy
- Support `phpstan/extension-installer`

## [6.1.7] - 2024-03-07
### Stable Release
- Fix support of last PHPStan 1.10.60 (PHPStan team does not respect Semver and like to break codes !).
  You must add `paramOutType: %featureToggles.paramOutType%` to your Phpstan.neon configuration

## [6.1.6] - 2024-02-03
### Stable Release
- Support Doctrine ORM 3

## [6.1.5] - 2023-11-29
### Stable Release
- Update dev lib requirements

## [6.1.4] - 2023-11-20
### Stable Release
- Fix support of last PHPStan 1.10.43 (PHPStan team does not respect Semver !)

## [6.1.3] - 2023-11-18
### Stable Release
- Fix support of last PHPStan 1.10.42

## [6.1.2] - 2023-07-04
### Stable Release
- Fix support of last PHPStan 1.10.23

## [6.1.1] - 2023-06-05
### Stable Release
- Fix support of last PHPStan 

## [6.1.0] - 2023-06-03
### Stable Release
- Optimize initialiation with a cache 
- Improve the documentation

## [6.0.19] - 2023-05-15
### Stable Release
- Update dev lib requirements
- Update copyrights

## [6.0.18] - 2023-04-16
### Stable Release
- Update dev lib requirements
- Support PHPUnit 10.1+
- Migrate phpunit.xml

## [6.0.17] - 2023-04-02
### Stable Release
- Q/A (PHPStan)

## [6.0.16] - 2023-03-10
### Stable Release
- Q/A

## [6.0.15] - 2023-02-11
### Stable Release
- PHPStan 1.10+

## [6.0.14] - 2023-02-11
### Stable Release
- Remove phpcpd and upgrade phpunit.xml

## [6.0.13] - 2023-02-03
### Stable Release
- Update dev libs to support PHPUnit 10 and remove unused phploc

## [6.0.12] - 2022-12-15
### Stable Release
- Many QA fixes

## [6.0.11] - 2022-11-03
### Stable Release
- Support PHPStan 1.9+

## [6.0.10] - 2022-10-04
### Stable Release
- Support PHPStan 1.8.7+

## [6.0.9] - 2022-07-12
### Stable Release
- Prevent issue in PHPStan AST Visitor, replace parent in migrated class method statement
  with proxy statement.

## [6.0.8] - 2022-07-12
### Stable Release
- Support PHPStan 1.8.1+ (BC break in bug fix release)
- Warning: PHPStan support will be dropped in 7.0+

## [6.0.7] - 2022-06-17
### Stable Release
- Clean code and tests with rector

## [6.0.6] - 2022-06-14
### Stable Release
- Fix error from an another BC Break in a patch release v1.7.14 of PHPStan 

## [6.0.5] - 2022-05-26
### Stable Release
- Improve support PHPSan 1.7.1+

## [6.0.4] - 2022-05-25
### Stable Release
- Improve support PHPSan 1.7.1+
- Warning : support of PHPStan may be removed in future to be replaced by another 
  tools more extendable. PHPStan's extensions become not really usable in lasts versions, since 1.0+ because
  all reflections tools are replaced by internal non extendable tool.
  Also Semver is not respected by PHPStan, external interfaces is often broken by minors or fixs updates.

## [6.0.3] - 2022-05-25
### Stable Release
- Support PHPSan 1.7.1+

## [6.0.2] - 2022-05-14
### Stable Release
- Detect (in phpstan extension) when a static closure is returned to throw an error to prevent it 
   (static closure are not bindable)

## [6.0.1] - 2022-03-08
### Stable Release
- Require Immutable 3.0.1 or later

## [6.0.0] - 2022-02-09
### Stable Release
- Remove support of PHP 8.0.
- Remove test skipped for PHP 8.1.
- Replace StateInterface::VISIBILITY_* by Enum Visibility in same namespace.
- Use readonly behavior on immutables objects' classes. 
- Prevent bug of mutability on automated features with Property and ConstraintsSet.
- ProxyInterface::DEFAULT_STATE_NAME is now final

## [5.1.9] - 2022-02-08
### Stable Release
- Support Immutable 3.0+

## [5.1.8] - 2022-01-31
### Stable Release
- Prevent a bug in `ProxyTrait` if `debug_backtrace` does not return a class.

## [5.1.7] - 2022-01-16
### Stable Release
- Prevent a bug with `ProxyTrait::validateName` if `$this->statesAliasesList` return a non string value.

## [5.1.6] - 2021-12-29
### Stable Release
- Update QA tools
- Switch to PHPStan 1.3

## [5.1.5] - 2021-12-12
### Stable Release
- Remove unused QA tool
- Remove support of Symfony 5.3
- Support Symfony 5.4 and 6.0+

## [5.1.4] - 2021-12-03
### Stable Release
- Fix some deprecated with PHP 8.1

## [5.1.3] - 2021-11-24
### Stable Release
- Fix deprecation for PHP 8.1
- Remove Serializable interface support
- Replace serialize method by __serialize
- Remove unserialize method, this method must be implemented in the proxy if the serialization is used
  *(Need to init the proxy' via `initializeStateProxya()`: `__unserialize` is like `__construct` to enable good 
  states at startup).

## [5.1.2] - 2021-11-16
### Stable Release
- QA
- Prevent potential bug with static closure returned in states

## [5.1.1] - 2021-11-14
### Stable Release
- Update ASTVisitor to rename methods with same name in differents states to avoir false positive about duplication
  of method in PHPStan's rules. Theses methods will be virtually flagged as public to avoid dead code error 

## [5.1.0] - 2021-11-10
### Stable Release
- Support PHPStan !.!+
- Remove State's PHPStan Scope. (Minor BC break, not impact State in production, only on QA. Need to update PHPStan 
  configuration to fix it [How to](documentation/howto/phpstan.md) )
- Add ASTVisitor to plug into PHPStan to alter proxies's AST with states's AST.

## [5.0.6] - 2021-08-22
### Stable Release
- Classes factorizations ORM and ODM supports to unique class :
  - `Teknoo\States\Doctrine\AbstractStandardObject`
  - `Teknoo\States\Doctrine\StandardTrait`
- Classes deprecations
  - `Teknoo\States\Doctrine\Entity\AbstractStandardEntity`
  - `Teknoo\States\Doctrine\Entity\StandardTrait`
  - `Teknoo\States\Doctrine\Document\AbstractStandardDocument`
  - `Teknoo\States\Doctrine\Document\StandardTrait`

## [5.0.5] - 2021-08-19
### Stable Release
- Fix PHPDoc

## [5.0.4] - 2021-06-27
### Stable Release
- Update documents and dev libs requirements

## [5.0.3] - 2021-05-31
### Stable Release
- Minor version about libs requirements

## [5.0.2] - 2021-04-28
### Stable Release
- Some optimisations on array functions to limit O(n)

## [5.0.1] - 2021-03-24
### Stable Release
- Constructor Property Promotion
- Non-capturing catches

## [5.0.0] - 2021-03-19
### Stable Release
- Migrate to PHP 8.0
- QA
- Fix license header

## [4.1.9] - 2021-02-25
### Stable Release
- Support PHPStan 0.12.79

## [4.1.8] - 2021-01-31
### Stable Release
- Remove deprecated aliases (since 1.2) and Typo3.

## [4.1.7] - 2021-01-19
### Stable Release
- simplify __call stack to remove two internals calls in the stacks

## [4.1.6] - 2021-01-18
### Stable Release
- Update to PHPStan 0.12.68
- Update to PHPunit 9.5

## [4.1.5] - 2020-12-03
### Stable Release
- Update to PHPStan 0.12.64

## [4.1.4] - 2020-12-03
### Stable Release
- Official Support of PHP8

## [4.1.3] - 2020-10-25
### Stable Release
- QA Fixe
- Remove ProxyInterface::__clone useless method in the interface. 
  (Clone behavior must not be defined here).

## [4.1.2] - 2020-10-12
### Stable Release
- Prepare library to support also PHP8.
- Remove deprecations from PHP8.

## [4.1.1] - 2020-09-18
### Stable Release
- Update QA and CI tools 

## [4.1.0] - 2020-09-15
### Stable Release
### Add
- Add more assertions checks for Automated states :
    * HasEmptyValueForKey
    * HasKey
    * HasNotEmptyValueForKey
    * IsArray
    * IsScalar
    * IsString

## [4.0.15] - 2020-08-25
### Stable Release
### Update
- Update libs and dev libs requirements

## [4.0.14] - 2020-08-20
### Stable Release
### Fix
- Replace catch in ProxyTrait::callMethod by a finally block

## [4.0.13] - 2020-07-26
### Stable Release
### Fix
- Fix Count* assertions to return self eager.

## [4.0.12] - 2020-07-24
### Stable Release
### Fix
- Fix issue when a public method in a parent class call a private method in a state available 
  by this same parent class nut not by the child class.

## [4.0.11] - 2020-07-21
### Stable Release
### Change
- Prevent error with ocramius/proxy-manager that does not properly initialize private properties

## [4.0.10] - 2020-07-17
### Stable Release
### Change
- Add travis run also with lowest dependencies.

## [4.0.9] - 2020-06-08
### Stable Release
### Change
- To use the original \ReflectionClass api and not "BetterReflectionClass" whome not implements all the api 
  in MethodClassExtension about PHPStan. 

## [4.0.8] - 2020-06-08
### Stable Release
### Change
- Support changes in PHPStan 0.12.26

## [4.0.7] - 2020-05-28
### Stable Release
### Change
- Replace initializeProxy by initializeStateProxy to avoid collision with other libs

## [4.0.6] - 2020-04-20
### Stable Release
### Change
- Update Scope class dedicated to PHPStan 0.12.19.

## [4.0.5] - 2020-03-11
### Stable Release
### Change
- Fix issue in `ProxyTrait::loadStates()` to manage proxies without owns definitions of `statesListDeclaration` and 
  avoid issues in inheritances (States and methods of parent's classes was loaded as method of final or current 
  checked class). 

## [4.0.4] - 2020-03-03
### Stable Release
### Change
- Remove PHP Mapping about Doctrine in `infrastructures/doctrine`.

## [4.0.3] - 2020-03-02
### Stable Release
### Change
- Fix issue into Class alias mapping to avoid BC Error about `Teknoo\UniversalPackage\States`.

## [4.0.2] - 2020-03-01
### Stable Release
### Change
- Update dev tools, migrate to PHPUnit 9.0, phploc 6.0, phpcpd 5.0
- Migrate "Universal Package" (dedicated to doctrine) to infrastructures folder.
  `Teknoo\UniversalPackage\States` migrate to `Teknoo\States\Doctrine\Entity` and `Teknoo\States\Doctrine\Document`
- Migrate PHPStan extension from src folder to infrastructures folder (namespace stay unchanged)

## [4.0.1] - 2020-01-29
### Stable Release
### Change
- QA Fix
- Update requirement for dev tools
- Update Scope class dedicated to PHPStan 0.12.8.

## [4.0.0] - 2020-01-14
### Stable Release

## [4.0.0-beta7] - 2020-01-07
### Change
- Change to support PHPStan 0.12.4

## [4.0.0-beta6] - 2019-12-30
### Change
- Update copyright

## [4.0.0-beta5] - 2019-12-23
### Change
- Fix Make definitions tools

## [4.0.0-beta4] - 2019-12-23
### Change
- Fix QA issues spotted by PHPStan
- Add PHPStan extension dedicated to support Stated classes analyze and avoid false positive.

## [4.0.0-beta3] - 2019-11-28
### Change
- Enable PHPStan in QA Tools
- Fix QA issues spotted by PHPStan

## [4.0.0-beta2] - 2019-11-28
### Change
- Most methods have been updated to include type hints where applicable. Please check your extension points to make sure the function signatures are correct.
_ All files use strict typing. Please make sure to not rely on type coercion.

## [4.0.0-beta1] - 2019-11-27
### Change
- PHP 7.4 is the minimum required
- Switch to typed properties
- Remove some PHP useless DockBlocks
- Replace array_merge by "..." operators

## [3.3.7] - 2019-10-24
### Release
- Add support of Doctrine MongoDB 2.0

## [3.3.6] - 2019-10-24
### Release
- Maintenance release, QA and update dev vendors requirements

## [3.3.5] - 2019-06-09
### Release
- Maintenance release, upgrade composer dev requirement and libs

## [3.3.4] - 2019-02-10
### Release
- Remove support of PHP 7.1
- Switch to PHPUnit 8.0 

## [3.3.3] - 2019-01-04
### Fix
- Add support to PHP 7.3 

## [3.3.2] - 2019-01-04
### Fix
- QA - check technical debt 

## [3.3.1] - 2018-09-02
### Fix
- Fix behavior of `isInState` and `isNotInState`, had a bug with not firsts states in the required list

### Add
- Add options to `isInState` and `isNotInState` to force the test on all states (all states must be active
 or all states must not be active).

## [3.3.0] - 2018-09-02
### Added
- Add method "isNotInState" into ProxyInterface to check if an object is not in any passed states.

## [3.2.2] - 2018-02-25
### Added
- Add some property assertion for automated : CountsEquals, CountsMore, CountsLess

## [3.2.1] - 2018-02-23
### Added
- Add some property assertion for automated : IsEmpty, IsNotEmpty and Callback

## [3.2.0] - 2018-01-01
### Stable release
- Final release 3.2.0,

### Updated
- Optimise States by using references instead copy for internal methods.
- Optimize visibility const to limit cpu computes.
- Fix bug in implementation for doctrines with automated methods : Missing 's' in name of method "updateStates"
- Improve fix added in 3.2.0-beta3 to use \Closure::call() when scope of the execution is the same of $this
and not the scope of a parent class.
- Use Teknoo/immutable 1.0

### All changes :

### Added
- Import from States Life Cyclable the feature automated
- The feature automated is now in main library
- Redesign feature Automated to follow east programming rules

### Remove
* ProxyInterface::listAvailableStates()
* ProxyInterface::listEnabledStates()
* ProxyInterface::getStatesList()
* ProxyInterface::inState()
* StateInterface::getStatedClassName()
* StateInterface::setStatedClassName()
* StateInterface::isPrivateMode()
* StateInterface::setPrivateMode()
* StateInterface::listMethods()
* StateInterface::testMethod()
* StateInterface::getClosure()
* ProxyInterface::statesListDeclaration() (only mandatory with the ProxyTrait implementation).

### Added
* StateInterface::executeClosure() : To execute, for the proxy instance, the closure and return the result via a callback (not mandatory to allow multiple call).
* ProxyInterface::isInState() : To execute the callable if the object in of a required states passed in first arguments (not mandatory to forbid exposition of state outside the object).
* ProxyTrait::statesListDeclaration() : Replace ProxyInterface::statesListDeclaration(), now is protected

### Updated
* StateTrait::getClosure() is now private
* ProxyTrait::findMethodToCall() is renamed to ProxyTrait::findAndCall()
* ProxyTrait::callInState() foo no longer retrieves the closure from the state then execute them. Now it ask the state to execute them directly and return the result via a callable function.
* ProxyTrait::findMethodToCall() loop directly on each enabled state without test them, the state instance pushs results to proxy if the closure is available and executed.
* ProxyTrait::findMethodToCall() throws the exception AvailableSeveralMethodImplementations only if two enabled states push a results.
* With this new behavior, several methods (one by enabled state) can be called, but only one can push a result.

## [3.2.0-beta6] - 2017-12-23
### Updated
- Optimise States by using references instead copy for internal methods.
- Optimize visibility const to limit cpu computes.

## [3.2.0-beta5] - 2017-11-22
### Updated
- Fix bug in implementation for doctrines with automated methods : Missing 's' in name of method "updateStates"

## [3.2.0-beta4] - 2017-11-12
### Updated
- Improve fix added in 3.2.0-beta3 to use \Closure::call() when scope of the execution is the same of $this
and not the scope of a parent class.
- Use Teknoo/immutable 1.0

## [3.2.0-beta3] - 2017-11-11
### Fixed
- Fix an issue : When a stated object call a inherited private method defined in a state, the scope of the method is
bound on the final class and not of the inherited class. (But $this is correctly bound to stated object instance).

## [3.2.0-beta2] - 2017-11-02
### Added
- Import from States Life Cyclable the feature automated
- The feature automated is now in main library
- Redesign feature Automated to follow east programming rules

### Fixed
- Fix visibility of the method statesListDeclaration in the standard implementation of the proxy
- Documentation

## [3.2.0-beta1] - 2017-10-29
- Redesign the library to follow East Oriented programming rules

### Remove
* ProxyInterface::listAvailableStates()
* ProxyInterface::listEnabledStates()
* ProxyInterface::getStatesList()
* ProxyInterface::inState()
* StateInterface::getStatedClassName()
* StateInterface::setStatedClassName()
* StateInterface::isPrivateMode()
* StateInterface::setPrivateMode()
* StateInterface::listMethods()
* StateInterface::testMethod()
* StateInterface::getClosure()
* ProxyInterface::statesListDeclaration() (only mandatory with the ProxyTrait implementation).

### Added
* StateInterface::executeClosure() : To execute, for the proxy instance, the closure and return the result via a callback (not mandatory to allow multiple call).
* ProxyInterface::isInState() : To execute the callable if the object in of a required states passed in first arguments (not mandatory to forbid exposition of state outside the object).
* ProxyTrait::statesListDeclaration() : Replace ProxyInterface::statesListDeclaration(), now is protected

### Updated
* StateTrait::getClosure() is now private
* ProxyTrait::findMethodToCall() is renamed to ProxyTrait::findAndCall()
* ProxyTrait::callInState() foo no longer retrieves the closure from the state then execute them. Now it ask the state to execute them directly and return the result via a callable function.
* ProxyTrait::findMethodToCall() loop directly on each enabled state without test them, the state instance pushs results to proxy if the closure is available and executed.
* ProxyTrait::findMethodToCall() throws the exception AvailableSeveralMethodImplementations only if two enabled states push a results.
* With this new behavior, several methods (one by enabled state) can be called, but only one can push a result.

## [3.1.0] - 2017-10-29
### Release
- Final release of 3.1.0

### Deprecated
- To prepare redesign of the library to follow East Oriented programming rules, and forbid information about states of object outside them:
    * ProxyInterface::listAvailableStates()
    * ProxyInterface::listEnabledStates()
    * ProxyInterface::getStatesList()
    * ProxyInterface::inState()
    * StateInterface::getStatedClassName()
    * StateInterface::setStatedClassName()
    * StateInterface::isPrivateMode()
    * StateInterface::setPrivateMode()
    * StateInterface::listMethods()
    * StateInterface::testMethod()
    * StateInterface::getClosure()

**There are only BC Break for lib interacting with the internal behavior of this lib, not with project using this lib.**

## [3.1.0-rc1] - 2017-10-12
### Release
- First RC.
- Update QA Tools

## [3.1.0-beta3] - 2017-10-01
### Updated
- Helper to clone proxy's values, callable easily if the Proxy class implements it's own
  __clone() method without do a conflict traits resolution / renaming.

## [3.1.0-beta2] - 2017-07-25
### Updated
- Update dev libraries used for this project and use now PHPUnit 6.2 for tests.

## [3.1.0-beta1] - 2017-06-30
### Add
- Migrate code from the package `statesBundle` to this package to limit number of packages to require / uses. (Since 3.0, the
 `StatesBundle` is not mandatory and not very usefull.

## [3.0.1] - 2017-02-15
### Fix
- Code style fix
- License file follow Github specs
- Add tools to checks QA, use `make qa` and `make test`, `make` to initalize the project, (or `composer update`).
- Update Travis to use this tool
- Fix QA Errors

### Remove
- Support of PHP 5.4 and PHP 5.5

## [3.0.0] - 2017-01-06
### Release
- Final release

## [3.0.0-beta1] - 2016-12-21
### Release
- First beta

## [3.0.0-alpha4] - 2016-10-31
### Fixed
* Fix code style

## [3.0.0-alpha3] - 2016-10-27
### Fixed
* Documentation
* Can use original full qualified state name for redefined/overloaded state by children stated class.

### Removed
* getMethodDescription() to get a description about a method. Conflict between \ReflecionMethod of closure builder
  and the \ReflectionFunction of the final closure. 

## [3.0.0-alpha2] - 2016-10-07
### Fixed
* Fix a bug in loading service without namespace

## [3.0.0-alpha1] - 2016-10-03
### Added/Changed
* State identifier must be a valid class name or a valid interface name. The state object must implements, 
  instantiates or inherits this class/interface name.
* States must be now directly declared into the proxy via the static method statesListDeclaration.
  
### Changed
* Standard proxies can be directly instantiate by PHP.
* States's method are now builders of closure : They must return a closure, bindable with \Closure::call(). 
  The Reflection API is no longer used to get a closure.
* The library uses \Closure::call() instead of \Closure::rebindTo(), more efficient.  
* The library uses now native array instead of \ArrayObject. Array's performances are good with PHP7+ and 
    using array forbid change in proxy without using API.
* MagicCallTrait forward `__toString()` call to the method `toString()` and `__invoke()` call to the method `invoke()`.

### Removed
* Useless state alias feature.
* Registration of states via theirs short name.
* State's factories, they become useless because states must be directly declared in the proxy.
* Loader feature, they become useless because states must be directly declared in the proxy.
* CLI Command, the States 3.x needs less operations to be started.
* Integrated proxies, Standard proxies can be now directly instantiate by PHP.  
* bootstrap.php file
* Joker "Of[SateName]" to specify the state to use to call a method.

## [2.1.1] - 2016-10-03
### Fixed
- Remove support of PHP 7.1+ of State 2.* because PHP 7.1 introduce a major BC Break on the Reflection API and forbid
rebind $this in closure created from the Reflection API.

## [2.1.0] - 2016-08-23
### Added
- Can use the full qualified state'name (full state' class name, with its namespace) instead its identifier (class name only)
 for proxy's methods:  
    * registerState
    * unregisterState
    * switchState
    * enableState
    * disableState
    * inState
 example $instance->switchState(MyState::class); instead of $instance->switchState('MyState'); 

## [2.0.6] - 2016-08-04
### Fixed
- Improve optimization on call to native function and optimized

## [2.0.5] - 2016-07-26
### Fixed
- Remove legacy reference to Uni Alteri in licences

## [2.0.4] - 2016-07-26
### Updated
- Fix code style with cs-fixer
- Improve documentation and fix documentations

### Add
- Add the API documentation about the 2.x branch.

## [2.0.3] - 2016-04-09
### Updated
- Fix code style with cs-fixer

## [2.0.2] - 2016-02-26
### Updated
- Update minimum requirement about symfony console to be compatible with symfony 3

## [2.0.1] - 2016-02-21
### Fixed
- Fix some mistake in the phpdoc

### Updated
- Prevent mistake on missing startup factory class definition for integrated stated class

## [2.0.0] - 2016-02-11 - Available on the branch "next"
### Updated
- Final Release, 1.x is switched on legacy branch and next is merged with master.

## [2.0.0-rc5] - 2016-02-01 - Available on the branch "next"
### Fixed
- Fix composer minimum requirements

## [1.2.3] - 2016-02-01
### Fixed
- Fix composer minimum requirements
- Fix bootstrap migration

## [1.2.2] - 2016-01-27
### Fixed
- .gitignore clean

## [2.0.0-rc4] - 2016-01-20 - Available on the branch "next"
### Updated
- Clean .gitignore
- Optimizing for inlined internal functions

### Fixed
- Use \Throwable instead of \Exception (new feature of PHP7)
- Fix behavior of magic getter and setter to keep the natural behavior of PHP objects with private, protected and public properties

## [2.0.0-rc3] - 2016-01-19 - Available on the branch "next"
### Updated
- Use ::class instead of class name in string

## [2.0.0-rc2] - 2016-01-12 - Available on the branch "next"
### Updated
- Set minimum stability to stable in composer

### Fixed
- Documentation

### Added
- Api documentation

## [1.2.1] - 2016-01-12
### Fixed
- Documentation

## [2.0.0-rc1] - 2015-10-15 - Available on the branch "next"
### Fixed
- Coverage tests

## [1.2.0] - 2015-12-05
### Added
- Stable release 1.2.0

### Fixed
- Coverage tests

## [2.0.0-beta18] - 2015-10-15 - Available on the branch "next"
### Fixed
- Fix last change with PHP7 RC8 and scalar type must be unqualified

## [1.2.0-rc6] - 2015-11-29
### Remove
- Typo3 class alias loader, replaced by a manual class_alias generated during library bootstrapt

### Fixed
- Fix migration about Uni Alteri to Teknoo Software organization

## [2.0.0-beta17] - 2015-10-15 - Available on the branch "next"
### Fixed
- Fix migration about Uni Alteri to Teknoo Software organization
- Fit git export

## [1.2.0-rc5] - 2015-10-31
### Fixed
- Fix migration about Uni Alteri to Teknoo Software organization
- Fit git export

## [2.0.0-beta16] - 2015-10-15 - Available on the branch "next"
### Changed
- Migrate library from Uni Alteri to Teknoo Software organization

## [1.2.0-rc4] - 2015-10-25
### Fixed
- Clean code to remove code to manage PHP 7

### Changed
- Migrate library from Uni Alteri to Teknoo Software organization

## [2.0.0-beta15] - 2015-10-15 - Available on the branch "next"
### Added
- Add test to support a possible change in behavior with PHP \ Closure :: call ()
- Support of the new PHP7 behavior (since PHP 7.0RC5) with ReflectionFunctionAbstract::getClosure().
    (Their scope can not be change by \Closure::bind(), but $this can be rebound to another object)

## [1.2.0-rc3] - 2015-10-15
### Removed
- Support of PHP7 because of PHP7 behavior has changed since PHP 7.0RC5 with ReflectionFunctionAbstract::getClosure().
    (Their scope can not be change by \Closure::bind(), but $this can be rebound to another object)

## [2.0.0-beta14] - 2015-10-07 - Available on the branch "next"
### Changed
- Change copyright

### Added
- Add test to support a possible change in behavior with PHP \ Closure :: call ()

## [1.2.0-rc2] - 2015-10-07
### Changed
- Second RC released
- Change copyright

## [2.0.0-beta13] - 2015-09-28 - Available on the branch "next"
### Removed
- GPL 3 license, keep only MIT license

### Fixed
- Bootstrap bug to find the composer autoloader file
- CLI issues
- Clean Demo

## [1.2.0-rc1] - 2015-09-13
### Changed
- First RC released

## [2.0.0-beta12] - 2015-09-05 - Available on the branch "next"
### Added
- Some tests to check \TypeError

### Changed
- Change composer restriction to use last phpunit

## [1.2.0-beta7] - 2015-08-28
### Notes
- 1.2.0-RC is planned for september, final version 1st october 2015

### Added
- States in a stated class can has aliases by using inheritance.

### Fixed
- Update Documentation

## [2.0.0-beta11] - 2015-08-16 - Available on the branch "next"
### Notes
- 2.0.0-RC1 is planned for september, last RC for the PHP7 release, stable version when XDebug will be compliant with PHP7.

### Added
- States in a stated class can has aliases by using inheritance.
- Optimize Factory to create only state instance by stated class.


## [2.0.0-beta10] - 2015-08-16 - Available on the branch "next"
### Fixed
- Update Documentation

### Changed
- All non empty string are now granted for state's identifier in the proxy


## [2.0.0-beta9] - 2015-08-16 - Available on the branch "next"
### Removed
- DI/Container : Remove useless DI Container

### Changed
- Dependency are now injected in constructor and not retrieved by the component from the service

## [2.0.0-beta8] - 2015-07-27 - Available on the branch "next"
### Fixed
- Fix fatal error in LoaderComposer to avoid redeclare the factory

## [1.2.0-beta6] - 2015-07-27
### Changed
- Fix fatal error in LoaderComposer to avoid redeclare the factory

## [2.0.0-beta7] - 2015-07-20 - Available on the branch "next"
### Changed
- Behavior of LoaderComposer : Memorize the result about the factory loading to avoid multiple attempts.

## [1.2.0-beta5] - 2015-07-20
### Changed
- Behavior of LoaderComposer : Memorize the result about the factory loading to avoid multiple attempts.

## [1.2.0-beta4] - 2015-07-19
### Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

### Added
- Backport from the next:2.x branch the LoaderComposer and FinderComposer to allow developer to use Composer instead
the library's loader to simplify the use of this library by avoiding multiple autoload mappings.
- New library's bootstrap, available in bootstrap_composer.php to use this library with LoaderComposer

### Changed
- LoaderStandard is marked as deprecated
- FinderStandard is marked as deprecated
- FinderIntegrated is marked as deprecated
- ObjectInterface is marked as deprecated
- InjectionClosure is marked as deprecated

## [2.0.0-beta6] - 2015-07-19 - Available on the branch "next"
### Removed
- FinderStandard : Replaced by FinderComposer
- FinderIntegrated : Replaced by FinderComposerIntegrated

### Changed
- FinderStandard, is now built on Composer and it was renaming to allow a backport to the 1.x branch.
- Fix tests

## [2.0.0-beta5] - 2015-07-03 - Available on the branch "next"
### Added
- PHP7 Scalar Type Hinting on all library's methods : Code more readable and remove manual type checks.
- LoaderComposer : Use composer to detect and load Stated classes.
- FinderComposer is now built on Composer to find and load states and proxies.
- Add PHPDoc tags @api on methods to allow users to distinct usable methods
- Validation states's name use now assert. Can be disable them in production to not launch the preg engine in full tested
environments.

### #Changed
- Split PHP's interfaces implementations and PHP's magic methods implementation from the Proxy Trait in several traits
    There are no conflicts with some library who checks magic getters and setters.
- Remove all definitions of these methods from Proxy Interface : To create a proxy is now easier.

### Removed
- LoaderStandard : Replaced by LoaderComposer
- FinderStandard : Replaced by FinderComposer
- FinderIntegrated : Replaced by FinderComposerIntegrated
- IncludePathManager : Useless since switch to Composer

## [2.0.0-beta4] - 2015-06-22 - Available on the branch "next"
### Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

### Added
- Define a new complementary trait to write easier new integrated proxies.

## [1.2.0-beta3] - 2015-06-22
### Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

### Added
- Define a new complementary trait to write easier new integrated proxies.

## [2.0.0-beta3] - 2015-06-10 - Available on the branch "next"
### Removed
- Support of PHP 5.6. PHP 7 provides several new tools about closures to improve performance.
- Remove Injection Closure, not needed with Closure::call(). The code is simpler

### Changed
- Use Close::call instead of Closure::bind

## [1.2.0-beta2] - 2015-06-10
### Added
- Add a new Injection Closure class designed for PHP5.6 and PHP7 to use floc operator instead of tip to avoid call_user_func.

## [2.0.0-beta2] - 2015-06-06 - Available on the branch "next"
### Added
- Support of inheritance of stated class like all standard PHP classes.
- Add demo to illustrate inheritance feature.
- Complete units tests and functional tests about inheritance feature.

### Changed
- Optimize finder behavior to save list of states

## [1.2.0-beta1] - 2015-06-06
### Added
- Support of inheritance of stated class like all standard PHP classes.
- Add demo to illustrate inheritance feature.
- Complete units tests and functional tests about inheritance feature.

### Changed
- Optimize finder behavior to save list of states

## [2.0.0-beta] - 2015-05-30 - Available on the branch "next"
### Removed
- Support of PHP 5.4 (End of life).
- Support of PHP 5.5 ("..." operator needed, available since 5.6).

### Changed
- Use splat operator ("...") instead of the "switch" solution to avoid "call_user_func_array" in injected closures.
- Use "..." operator instead of func_get_args().

### Notes
- Support of PHP 5.4 and PHP 5.5 are always available with 1.x versions.
- EOL of the branch 1.x scheduled for 20 Jun 2017. (One later after 5.5).
- No new features planned for 2.0 compared to 1.x versions, only best performances and use last PHP's features.

## [1.1.2] - 2015-05-24
### Chanced
- Remove useless tests units about PHP's behavior.

### Added
- Support of PHP7 (States is 7x faster than with PHP5.5)
- Add travis file to support IC outside Teknoo Software's server

## [1.1.1] - 2015-05-06
### Fixed
- Code style fix
- Use callable type
- Use (int) cast instead of intval()
- Fix version

## [1.1.0] - 2015-02-15
### Fixed
- Code style fix
- Fix version

### Changed
- Minimize using of call_user_function_array, use direct calling.

### Added
- Add method in InjectionClosure to allow proxy to invoke directly the closure without used
call_user_func_*

### Changed
- Remove call_user_func_array in proxy
- Replace call_user_func_array in Factory by ReflectionMethod
- Minimize impact of call_user_func_array by calling directly the closure with few arguments

## [1.0.2] - 2015-02-09
### Changed
- Source folder is now called `src` instead of `lib`
- Documentation updated

### Added
- Contribution rules

## [1.0.1] - 2015-01-28
### Fixed
- Code style

### Changed
- Documentation updated

## [1.0.0] - 2015-01-17
- First stable of the states library

### Added
- New CLI tool
