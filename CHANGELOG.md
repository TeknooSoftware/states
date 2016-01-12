#Teknoo Software - States library - Change Log

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
    (Their scope can not be change by \Closure::bind(), but $this can be rebinded to another object)

##[1.2.0-rc3] - 2015-10-15
###Removed
- Support of PHP7 because of PHP7 behavior has changed since PHP 7.0RC5 with ReflectionFunctionAbstract::getClosure(). 
    (Their scope can not be change by \Closure::bind(), but $this can be rebinded to another object)

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
