#Uni Alteri - States library - Change Log

##[2.0.0-beta3] - 2015-06-22 - Available on the branch "next"
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
- Add travis file to support IC outside Uni Alteri's server

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
