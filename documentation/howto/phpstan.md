Teknoo Software - States library - PHPStan
==========================================

Presentation
------------
Methods of a stated class are provided by its states and called through `__call()`, and closures returned by builders
are bound to the proxy : without help, PHPStan reports calls to undefined methods on the proxy, and accesses to
undefined properties in states. This library provides a PHPStan extension :

* `Teknoo\States\PHPStan\MethodsClassExtension` declares methods of states as methods of the proxy, with arguments and
  return type of theirs closures,
* `Teknoo\States\PHPStan\Analyser\ASTVisitor` moves the closure of each builder into the proxy, to be analysed as a
  method of the proxy.

Installation
------------
With [phpstan/extension-installer](https://github.com/phpstan/extension-installer), the extension is automatically
enabled. Else, include the file `extension.neon` provided by this library in your `phpstan.neon` :

    includes:
        - vendor/teknoo/states/extension.neon

Do not declare these services yourself : theirs arguments are internal services of PHPStan and can change.

Requirements on your stated classes
-----------------------------------

* Builders must declare the return type `Closure` (or `callable`), and must directly return the closure or the arrow
  function : `return function () {...};` or `return fn () => ...;`. Others builders are not analysed as methods
  of the proxy.
* Static closures are supported : like the library does, they are only bound to the scope of the proxy. Theirs
  methods are analysed as static methods of the proxy, so any usage of `$this` in a static closure is reported.
* The AST visitor only detects states and proxies **directly implementing** `StateInterface` and `ProxyInterface`
  (with the traits `StateTrait` and `ProxyTrait`). States extending `AbstractState` and proxies extending `Standard`
  are not yet moved into the proxy : accesses to properties of the proxy in theirs closures can be reported. You can
  add the tag `@mixin YourProxyClass` on these states to help PHPStan and your IDE.

This extension relies on internals of PHPStan, not covered by its backward compatibility promise : when a new release
of PHPStan breaks it, update this library.

Credits
-------
EIRL Richard Déloge - <https://deloge.io> - Lead developer.
SASU Teknoo Software - <https://teknoo.software>

License
-------
States is licensed under the 3-Clause BSD License - see the [LICENSE](../../LICENSE) file for details.
