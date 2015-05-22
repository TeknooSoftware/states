#Uni Alteri - States library - Change Log

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
