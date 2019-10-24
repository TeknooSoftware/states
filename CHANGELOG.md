#Teknoo Software - States library - Change Log

##[3.3.6] - 2019-10-24
###Release
- Maintenance release, QA and update dev vendors requirements

##[3.3.5] - 2019-06-09
###Release
- Maintenance release, upgrade composer dev requirement and libs

##[3.3.4] - 2019-02-10
###Release
- Remove support of PHP 7.1
- Switch to PHPUnit 8.0 

##[3.3.3] - 2019-01-04
###Fix
- Add support to PHP 7.3 

##[3.3.2] - 2019-01-04
###Fix
- QA - check technical debt 

##[3.3.1] - 2018-09-02
###Fix
- Fix behavior of `isInState` and `isNotInState`, had a bug with not firsts states in the required list

###Add
- Add options to `isInState` and `isNotInState` to force the test on all states (all states must be active
 or all states must not be active).

##[3.3.0] - 2018-09-02
###Added
- Add method "isNotInState" into ProxyInterface to check if an object is not in any passed states.

##[3.2.2] - 2018-02-25
###Added
- Add some property assertion for automated : CountsEquals, CountsMore, CountsLess

##[3.2.1] - 2018-02-23
###Added
- Add some property assertion for automated : IsEmpty, IsNotEmpty and Callback

##[3.2.0] - 2018-01-01
###Stable release
- Final release 3.2.0,

###Updated
- Optimise States by using references instead copy for internal methods.
- Optimize visibility const to limit cpu computes.
- Fix bug in implementation for doctrines with automated methods : Missing 's' in name of method "updateStates"
- Improve fix added in 3.2.0-beta3 to use \Closure::call() when scope of the execution is the same of $this
and not the scope of a parent class.
- Use Teknoo/immutable 1.0

###All changes :

###Added
- Import from States Life Cyclable the feature automated
- The feature automated is now in main library
- Redesign feature Automated to follow east programming rules

###Remove
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

###Added
* StateInterface::executeClosure() : To execute, for the proxy instance, the closure and return the result via a callback (not mandatory to allow multiple call).
* ProxyInterface::isInState() : To execute the callable if the object in of a required states passed in first arguments (not mandatory to forbid exposition of state outside the object).
* ProxyTrait::statesListDeclaration() : Replace ProxyInterface::statesListDeclaration(), now is protected

###Updated
* StateTrait::getClosure() is now private
* ProxyTrait::findMethodToCall() is renamed to ProxyTrait::findAndCall()
* ProxyTrait::callInState() foo no longer retrieves the closure from the state then execute them. Now it ask the state to execute them directly and return the result via a callable function.
* ProxyTrait::findMethodToCall() loop directly on each enabled state without test them, the state instance pushs results to proxy if the closure is available and executed.
* ProxyTrait::findMethodToCall() throws the exception AvailableSeveralMethodImplementations only if two enabled states push a results.
* With this new behavior, several methods (one by enabled state) can be called, but only one can push a result.

##[3.2.0-beta6] - 2017-12-23
###Updated
- Optimise States by using references instead copy for internal methods.
- Optimize visibility const to limit cpu computes.

##[3.2.0-beta5] - 2017-11-22
###Updated
- Fix bug in implementation for doctrines with automated methods : Missing 's' in name of method "updateStates"

##[3.2.0-beta4] - 2017-11-12
###Updated
- Improve fix added in 3.2.0-beta3 to use \Closure::call() when scope of the execution is the same of $this
and not the scope of a parent class.
- Use Teknoo/immutable 1.0

##[3.2.0-beta3] - 2017-11-11
###Fixed
- Fix an issue : When a stated object call a inherited private method defined in a state, the scope of the method is
bound on the final class and not of the inherited class. (But $this is correctly bound to stated object instance).

##[3.2.0-beta2] - 2017-11-02
###Added
- Import from States Life Cyclable the feature automated
- The feature automated is now in main library
- Redesign feature Automated to follow east programming rules

###Fixed
- Fix visibility of the method statesListDeclaration in the standard implementation of the proxy
- Documentation

##[3.2.0-beta1] - 2017-10-29
- Redesign the library to follow East Oriented programming rules

###Remove
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

###Added
* StateInterface::executeClosure() : To execute, for the proxy instance, the closure and return the result via a callback (not mandatory to allow multiple call).
* ProxyInterface::isInState() : To execute the callable if the object in of a required states passed in first arguments (not mandatory to forbid exposition of state outside the object).
* ProxyTrait::statesListDeclaration() : Replace ProxyInterface::statesListDeclaration(), now is protected

###Updated
* StateTrait::getClosure() is now private
* ProxyTrait::findMethodToCall() is renamed to ProxyTrait::findAndCall()
* ProxyTrait::callInState() foo no longer retrieves the closure from the state then execute them. Now it ask the state to execute them directly and return the result via a callable function.
* ProxyTrait::findMethodToCall() loop directly on each enabled state without test them, the state instance pushs results to proxy if the closure is available and executed.
* ProxyTrait::findMethodToCall() throws the exception AvailableSeveralMethodImplementations only if two enabled states push a results.
* With this new behavior, several methods (one by enabled state) can be called, but only one can push a result.

##[3.1.0] - 2017-10-29
###Release
- Final release of 3.1.0

###Deprecated
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

##[3.1.0-rc1] - 2017-10-12
###Release
- First RC.
- Update QA Tools

##[3.1.0-beta3] - 2017-10-01
###Updated
- Helper to clone proxy's values, callable easily if the Proxy class implements it's own
  __clone() method without do a conflict traits resolution / renaming.

##[3.1.0-beta2] - 2017-07-25
###Updated
- Update dev libraries used for this project and use now PHPUnit 6.2 for tests.

##[3.1.0-beta1] - 2017-06-30
###Add
- Migrate code from the package `statesBundle` to this package to limit number of packages to require / uses. (Since 3.0, the
 `StatesBundle` is not mandatory and not very usefull.

##[3.0.1] - 2017-02-15
###Fix
- Code style fix
- License file follow Github specs
- Add tools to checks QA, use `make qa` and `make test`, `make` to initalize the project, (or `composer update`).
- Update Travis to use this tool
- Fix QA Errors

###Remove
- Support of PHP 5.4 and PHP 5.5

##[3.0.0] - 2017-01-06
###Release
- Final release

##[3.0.0-beta1] - 2016-12-21
###Release
- First beta

##[3.0.0-alpha4] - 2016-10-31
###Fixed
* Fix code style

##[3.0.0-alpha3] - 2016-10-27
###Fixed
* Documentation
* Can use original full qualified state name for redefined/overloaded state by children stated class.

###Removed
* getMethodDescription() to get a description about a method. Conflict between \ReflecionMethod of closure builder
  and the \ReflectionFunction of the final closure. 

##[3.0.0-alpha2] - 2016-10-07
###Fixed
* Fix a bug in loading service without namespace

##[3.0.0-alpha1] - 2016-10-03
###Added/Changed
* State identifier must be a valid class name or a valid interface name. The state object must implements, 
  instantiates or inherits this class/interface name.
* States must be now directly declared into the proxy via the static method statesListDeclaration.
  
###Changed
* Standard proxies can be directly instantiate by PHP.
* States's method are now builders of closure : They must return a closure, bindable with \Closure::call(). 
  The Reflection API is no longer used to get a closure.
* The library uses \Closure::call() instead of \Closure::rebindTo(), more efficient.  
* The library uses now native array instead of \ArrayObject. Array's performances are good with PHP7+ and 
    using array forbid change in proxy without using API.
* MagicCallTrait forward `__toString()` call to the method `toString()` and `__invoke()` call to the method `invoke()`.

###Removed
* Useless state alias feature.
* Registration of states via theirs short name.
* State's factories, they become useless because states must be directly declared in the proxy.
* Loader feature, they become useless because states must be directly declared in the proxy.
* CLI Command, the States 3.x needs less operations to be started.
* Integrated proxies, Standard proxies can be now directly instantiate by PHP.  
* bootstrap.php file
* Joker "Of[SateName]" to specify the state to use to call a method.

##[2.1.1] - 2016-10-03
###Fixed
- Remove support of PHP 7.1+ of State 2.* because PHP 7.1 introduce a major BC Break on the Reflection API and forbid
rebind $this in closure created from the Reflection API.

##[2.1.0] - 2016-08-23
###Added
- Can use the full qualified state'name (full state' class name, with its namespace) instead its identifier (class name only)
 for proxy's methods:  
    * registerState
    * unregisterState
    * switchState
    * enableState
    * disableState
    * inState
 example $instance->switchState(MyState::class); instead of $instance->switchState('MyState'); 

##[2.0.6] - 2016-08-04
###Fixed
- Improve optimization on call to native function and optimized

##[2.0.5] - 2016-07-26
###Fixed
- Remove legacy reference to Uni Alteri in licences

##[2.0.4] - 2016-07-26
###Updated
- Fix code style with cs-fixer
- Improve documentation and fix documentations

###Add
- Add the API documentation about the 2.x branch.

##[2.0.3] - 2016-04-09
###Updated
- Fix code style with cs-fixer

##[2.0.2] - 2016-02-26
###Updated
- Update minimum requirement about symfony console to be compatible with symfony 3

##[2.0.1] - 2016-02-21
###Fixed
- Fix some mistake in the phpdoc

###Updated
- Prevent mistake on missing startup factory class definition for integrated stated class

##[2.0.0] - 2016-02-11 - Available on the branch "next"
###Updated
- Final Release, 1.x is switched on legacy branch and next is merged with master.

##[2.0.0-rc5] - 2016-02-01 - Available on the branch "next"
###Fixed
- Fix composer minimum requirements

##[1.2.3] - 2016-02-01
###Fixed
- Fix composer minimum requirements
- Fix bootstrap migration

##[1.2.2] - 2016-01-27
###Fixed
- .gitignore clean

##[2.0.0-rc4] - 2016-01-20 - Available on the branch "next"
###Updated
- Clean .gitignore
- Optimizing for inlined internal functions

###Fixed
- Use \Throwable instead of \Exception (new feature of PHP7)
- Fix behavior of magic getter and setter to keep the natural behavior of PHP objects with private, protected and public properties

##[2.0.0-rc3] - 2016-01-19 - Available on the branch "next"
###Updated
- Use ::class instead of class name in string

##[2.0.0-rc2] - 2016-01-12 - Available on the branch "next"
###Updated
- Set minimum stability to stable in composer

###Fixed
- Documentation

###Added
- Api documentation

##[1.2.1] - 2016-01-12
###Fixed
- Documentation

##[2.0.0-rc1] - 2015-10-15 - Available on the branch "next"
###Fixed
- Coverage tests

##[1.2.0] - 2015-12-05
###Added
- Stable release 1.2.0

###Fixed
- Coverage tests

##[2.0.0-beta18] - 2015-10-15 - Available on the branch "next"
###Fixed
- Fix last change with PHP7 RC8 and scalar type must be unqualified

##[1.2.0-rc6] - 2015-11-29
###Remove
- Typo3 class alias loader, replaced by a manual class_alias generated during library bootstrapt

###Fixed
- Fix migration about Uni Alteri to Teknoo Software organization

##[2.0.0-beta17] - 2015-10-15 - Available on the branch "next"
###Fixed
- Fix migration about Uni Alteri to Teknoo Software organization
- Fit git export

##[1.2.0-rc5] - 2015-10-31
###Fixed
- Fix migration about Uni Alteri to Teknoo Software organization
- Fit git export

##[2.0.0-beta16] - 2015-10-15 - Available on the branch "next"
###Changed
- Migrate library from Uni Alteri to Teknoo Software organization

##[1.2.0-rc4] - 2015-10-25
###Fixed
- Clean code to remove code to manage PHP 7

###Changed
- Migrate library from Uni Alteri to Teknoo Software organization

##[2.0.0-beta15] - 2015-10-15 - Available on the branch "next"
###Added
- Add test to support a possible change in behavior with PHP \ Closure :: call ()
- Support of the new PHP7 behavior (since PHP 7.0RC5) with ReflectionFunctionAbstract::getClosure().
    (Their scope can not be change by \Closure::bind(), but $this can be rebound to another object)

##[1.2.0-rc3] - 2015-10-15
###Removed
- Support of PHP7 because of PHP7 behavior has changed since PHP 7.0RC5 with ReflectionFunctionAbstract::getClosure().
    (Their scope can not be change by \Closure::bind(), but $this can be rebound to another object)

##[2.0.0-beta14] - 2015-10-07 - Available on the branch "next"
###Changed
- Change copyright

###Added
- Add test to support a possible change in behavior with PHP \ Closure :: call ()

##[1.2.0-rc2] - 2015-10-07
###Changed
- Second RC released
- Change copyright

##[2.0.0-beta13] - 2015-09-28 - Available on the branch "next"
###Removed
- GPL 3 license, keep only MIT license

###Fixed
- Bootstrap bug to find the composer autoloader file
- CLI issues
- Clean Demo

##[1.2.0-rc1] - 2015-09-13
###Changed
- First RC released

##[2.0.0-beta12] - 2015-09-05 - Available on the branch "next"
###Added
- Some tests to check \TypeError

###Changed
- Change composer restriction to use last phpunit

##[1.2.0-beta7] - 2015-08-28
###Notes
- 1.2.0-RC is planned for september, final version 1st october 2015

###Added
- States in a stated class can has aliases by using inheritance.

###Fixed
- Update Documentation

##[2.0.0-beta11] - 2015-08-16 - Available on the branch "next"
###Notes
- 2.0.0-RC1 is planned for september, last RC for the PHP7 release, stable version when XDebug will be compliant with PHP7.

###Added
- States in a stated class can has aliases by using inheritance.
- Optimize Factory to create only state instance by stated class.


##[2.0.0-beta10] - 2015-08-16 - Available on the branch "next"
###Fixed
- Update Documentation

###Changed
- All non empty string are now granted for state's identifier in the proxy


##[2.0.0-beta9] - 2015-08-16 - Available on the branch "next"
###Removed
- DI/Container : Remove useless DI Container

###Changed
- Dependency are now injected in constructor and not retrieved by the component from the service

##[2.0.0-beta8] - 2015-07-27 - Available on the branch "next"
###Fixed
- Fix fatal error in LoaderComposer to avoid redeclare the factory

##[1.2.0-beta6] - 2015-07-27
###Changed
- Fix fatal error in LoaderComposer to avoid redeclare the factory

##[2.0.0-beta7] - 2015-07-20 - Available on the branch "next"
###Changed
- Behavior of LoaderComposer : Memorize the result about the factory loading to avoid multiple attempts.

##[1.2.0-beta5] - 2015-07-20
###Changed
- Behavior of LoaderComposer : Memorize the result about the factory loading to avoid multiple attempts.

##[1.2.0-beta4] - 2015-07-19
###Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

###Added
- Backport from the next:2.x branch the LoaderComposer and FinderComposer to allow developer to use Composer instead
the library's loader to simplify the use of this library by avoiding multiple autoload mappings.
- New library's bootstrap, available in bootstrap_composer.php to use this library with LoaderComposer

###Changed
- LoaderStandard is marked as deprecated
- FinderStandard is marked as deprecated
- FinderIntegrated is marked as deprecated
- ObjectInterface is marked as deprecated
- InjectionClosure is marked as deprecated

##[2.0.0-beta6] - 2015-07-19 - Available on the branch "next"
###Removed
- FinderStandard : Replaced by FinderComposer
- FinderIntegrated : Replaced by FinderComposerIntegrated

###Changed
- FinderStandard, is now built on Composer and it was renaming to allow a backport to the 1.x branch.
- Fix tests

##[2.0.0-beta5] - 2015-07-03 - Available on the branch "next"
###Added
- PHP7 Scalar Type Hinting on all library's methods : Code more readable and remove manual type checks.
- LoaderComposer : Use composer to detect and load Stated classes.
- FinderComposer is now built on Composer to find and load states and proxies.
- Add PHPDoc tags @api on methods to allow users to distinct usable methods
- Validation states's name use now assert. Can be disable them in production to not launch the preg engine in full tested
environments.

####Changed
- Split PHP's interfaces implementations and PHP's magic methods implementation from the Proxy Trait in several traits
    There are no conflicts with some library who checks magic getters and setters.
- Remove all definitions of these methods from Proxy Interface : To create a proxy is now easier.

###Removed
- LoaderStandard : Replaced by LoaderComposer
- FinderStandard : Replaced by FinderComposer
- FinderIntegrated : Replaced by FinderComposerIntegrated
- IncludePathManager : Useless since switch to Composer

##[2.0.0-beta4] - 2015-06-22 - Available on the branch "next"
###Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

###Added
- Define a new complementary trait to write easier new integrated proxies.

##[1.2.0-beta3] - 2015-06-22
###Changed
- Define a new interface UniAlteri\States\Proxy\IntegratedInterface to define integrated proxies

###Added
- Define a new complementary trait to write easier new integrated proxies.

##[2.0.0-beta3] - 2015-06-10 - Available on the branch "next"
###Removed
- Support of PHP 5.6. PHP 7 provides several new tools about closures to improve performance.
- Remove Injection Closure, not needed with Closure::call(). The code is simpler

###Changed
- Use Close::call instead of Closure::bind

##[1.2.0-beta2] - 2015-06-10
###Added
- Add a new Injection Closure class designed for PHP5.6 and PHP7 to use floc operator instead of tip to avoid call_user_func.

##[2.0.0-beta2] - 2015-06-06 - Available on the branch "next"
###Added
- Support of inheritance of stated class like all standard PHP classes.
- Add demo to illustrate inheritance feature.
- Complete units tests and functional tests about inheritance feature.

###Changed
- Optimize finder behavior to save list of states

##[1.2.0-beta1] - 2015-06-06
###Added
- Support of inheritance of stated class like all standard PHP classes.
- Add demo to illustrate inheritance feature.
- Complete units tests and functional tests about inheritance feature.

###Changed
- Optimize finder behavior to save list of states

##[2.0.0-beta] - 2015-05-30 - Available on the branch "next"
###Removed
- Support of PHP 5.4 (End of life).
- Support of PHP 5.5 ("..." operator needed, available since 5.6).

###Changed
- Use splat operator ("...") instead of the "switch" solution to avoid "call_user_func_array" in injected closures.
- Use "..." operator instead of func_get_args().

###Notes
- Support of PHP 5.4 and PHP 5.5 are always available with 1.x versions.
- EOL of the branch 1.x scheduled for 20 Jun 2017. (One later after 5.5).
- No new features planned for 2.0 compared to 1.x versions, only best performances and use last PHP's features.

##[1.1.2] - 2015-05-24
###Chanced
- Remove useless tests units about PHP's behavior.

###Added
- Support of PHP7 (States is 7x faster than with PHP5.5)
- Add travis file to support IC outside Teknoo Software's server

##[1.1.1] - 2015-05-06
###Fixed
- Code style fix
- Use callable type
- Use (int) cast instead of intval()
- Fix version

##[1.1.0] - 2015-02-15
###Fixed
- Code style fix
- Fix version

###Changed
- Minimize using of call_user_function_array, use direct calling.

###Added
- Add method in InjectionClosure to allow proxy to invoke directly the closure without used
call_user_func_*

###Changed
- Remove call_user_func_array in proxy
- Replace call_user_func_array in Factory by ReflectionMethod
- Minimize impact of call_user_func_array by calling directly the closure with few arguments

##[1.0.2] - 2015-02-09
###Changed
- Source folder is now called `src` instead of `lib`
- Documentation updated

###Added
- Contribution rules

##[1.0.1] - 2015-01-28
###Fixed
- Code style

###Changed
- Documentation updated

##[1.0.0] - 2015-01-17
- First stable of the states library

###Added
- New CLI tool
