# AGENTS.md — Operating doctrine for Teknoo/states

This document defines the operating procedures, technical stack, and verification protocols for AI agents working on the
**Teknoo/states** project.

## 1. Project Overview

**Teknoo/states** is a PHP library designed to implement the **State Pattern**. It allows developers to create objects
whose behavior changes depending on its internal state, avoiding complex conditional logic and improving
maintainability.

- **Core Goal**: Provide a robust, clean, and maintainable implementation of the State pattern in PHP.
- **Key Features**: State inheritance, automated state switching based on properties, and compatibility with Doctrine.
- **Core Dependency**: `teknoo/immutable` (the only runtime dependency, always required).

## 2. Technical Stack

- **Language**: PHP 8.4+ (`composer.json`: `^8.4`). The CI (`.gitlab-ci.yml`) runs the suite with PHP 8.4 and 8.5, each
  with the lowest and the latest dependencies.
- **Typing**: Strict typing is enforced throughout the codebase.
- **Testing & Quality**:
    - **PHPUnit** 13: unit and functional tests, PHPUnit attributes (`#[CoversClass]`, `#[CoversTrait]`,
      `#[DataProvider]`).
    - **PHPStan**, level **max**, on `src/`, `infrastructures/` and `tests/fixtures/Phpstan/`.
    - **PHP_CodeSniffer**, standard **PSR-12**, on `src/` and `infrastructures/`.
    - Rector is **not** part of the toolchain (it is not a dependency of this project).
- **PHPStan extension**: this library ships its own PHPStan extension (`infrastructures/PHPStan`, `extension.neon`).
  It relies on PHPStan's internals, not covered by its backward compatibility promise : most of patch releases of this
  library fix a break introduced by a patch release of PHPStan. The project's `phpstan.neon` does **not** load this
  extension: it is only covered by unit tests.

## 3. Repository Layout

| Path                         | Content                                                                                                    |
|:-----------------------------|:-----------------------------------------------------------------------------------------------------------|
| `src/Proxy`                  | `ProxyInterface`, `ProxyTrait` (calls dispatching, visibility, states management), `Standard`, PHP traits. |
| `src/State`                  | `StateInterface`, `StateTrait`, `AbstractState`, `Visibility`, `RuntimeCache` (internal).                  |
| `src/Automated`              | Automated states: `AutomatedTrait`, assertions (`Property`, `Callback`) and property's constraints.        |
| `src/Attributes`             | PHP attributes: `#[StateClass]`, `#[Assertions]`, `#[Assertion\Property]`, `#[Assertion\Callback]`.        |
| `src/Exception`              | Base exceptions. `src/Proxy/Exception` and `src/State/Exception` extend them for each layer.              |
| `infrastructures/doctrine`   | `StandardTrait` and `AbstractStandardObject` for Doctrine ORM/ODM entities and documents.                  |
| `infrastructures/PHPStan`    | PHPStan extension (methods reflection and AST visitor).                                                    |
| `tests/States`               | Unit tests, mirror of `src/`. `tests/States/Functional` contains functional tests, without mock.           |
| `tests/infrastructures`      | Unit tests of `infrastructures/`.                                                                          |
| `tests/fixtures/Support`     | Stated classes used by tests. `*Legacy` classes use the deprecated `statesListDeclaration()`.              |
| `tests/fixtures/Phpstan`     | Classes analysed by PHPStan during `make qa`: they must stay free of errors.                               |
| `demo/`                      | Runnable demos (`php demo/demo_article.php`, ...). Not autoloaded, not exported in the package.            |
| `documentation/`             | User documentation (`howto/`), architecture and workflow diagrams (`*.drawio` are sources of `*.png`).     |

## 4. Agent Workflow

All agents must follow the standard deployment loop: **Understand → Explore → Plan → Implement → Review → Verify →
Report**.

### 4.1. Commands

| Command                | What it really runs                                                                           |
|:-----------------------|:----------------------------------------------------------------------------------------------|
| `make test`            | PHPUnit with code coverage. **Requires Xdebug** and runs with `zend.assertions=0`.            |
| `make qa`              | `lint` + `phpstan` + `phpcs` + `composer audit`. **Requires a network access** (audit).       |
| `make qa-offline`      | `lint` + `phpstan` + `phpcs`, without `composer audit`. To use when there is no network.      |
| `make lint`            | `php -l` on `src/` and `infrastructures/`.                                                    |
| `make phpstan`         | `vendor/bin/phpstan analyse` (configuration in `phpstan.neon`).                               |
| `make phpcs`           | `vendor/bin/phpcs --standard=PSR12` (`vendor/bin/phpcbf` fixes most of reported errors).      |
| `make depend`          | `composer update` (`DEPENDENCIES=lowest make depend` to install lowest versions).             |

> **Never run `make` without target, `make all` or `make clean`**: the default target is `clean depend`, it deletes
> the `vendor/` folder (`rm -rf vendor`) and runs `composer update`.

To run quickly a single test, without code coverage:
`php vendor/bin/phpunit --no-coverage --filter <TestClassOrMethod>`. Another PHP version can be selected with the
variable `PHP` (`make test PHP=php8.4`).

### 4.2. Verification Discipline

No task is considered "done" until the following checks are successfully performed:

| Check Type     | Command                                  | Requirement                                     |
|:---------------|:-----------------------------------------|:------------------------------------------------|
| **Unit Tests** | `make test`                              | All tests must pass without failures or errors. |
| **QA**         | `make qa` (`make qa-offline` if offline) | Zero errors reported.                           |

*Note: If a check fails, the agent must fix the issue and re-run the verification.*

The PHPUnit configuration is strict: any PHP **deprecation, notice or warning**, any risky test and any PHPUnit
deprecation fails the suite. Tests using a deprecated feature of this library on purpose (like
`statesListDeclaration()`) must use the attribute `#[IgnoreDeprecations]`.

## 5. Development Rules

- **Strict Typing**: Always use `declare(strict_types=1);` in all new PHP files.
- **License header**: All PHP files start with the same license header (copy it from any file of `src/`), and all
  classes repeat the `@copyright`, `@license` and `@author` tags in theirs docblocks.
- **Coding standard**: PSR-12, lines of 120 characters at most, functions imported with `use function`.
- **Immutability**: Be mindful of the interaction with `teknoo/immutable` (assertions and constraints are immutable).
- **Documentation**: Any changes to public APIs must be accompanied by documentation updates in `documentation/`
  and in the `README.md` when its quick example or its requirements are concerned.
- **Namespace Compliance**: All new classes must follow the established namespace structure (`Teknoo\States\...`).
- **Complexity**: Avoid monolithic state classes; favor small, single-responsibility state implementations.
- **Changelog**: Each release adds an entry at the top of `CHANGELOG.md`, with the format
  `## [X.Y.Z] - YYYY-MM-DD`, then `### Stable Release`, then a list of changes. This entry is also the message of the
  release's commit. Releases follow SemVer, the version is only defined by the git tag.
- **Git**: Work on a dedicated branch, never on `master`. `composer.lock` is ignored and must not be committed.

### 5.1. How to write a stated class

- A **state's method is a builder**: a method without argument, returning the `Closure` to execute. It is never
  executed as the method of the stated class. The closure is bound to the proxy: inside it, `$this`, `self` and
  `static` are the **stated class instance**, not the state.

      public function sayHello(): Closure
      {
          return function (string $name): string {
              return 'Hello ' . $name . ' from ' . $this->name;
          };
      }

- Builders must declare the return type `Closure` (required by the PHPStan extension to analyse the closure as a
  method of the proxy) and must return a closure or an arrow function. A **static** closure is supported : it does
  not use `$this`, so it is only bound to the scope of the stated class (`self`, `static`), never to the instance.
  PHP forbids to bind a closure created from a method to another class (see
  `documentation/howto/state-methods.md`), this is why builders exist.
- The **visibility of the builder** (public, protected, private) is the visibility of the method of the stated
  class. It is checked by the library from the caller (`debug_backtrace()`), like PHP does for real methods.
- States are declared on the proxy with the attribute `#[StateClass]`. The static method `statesListDeclaration()` is
  deprecated: it triggers an `E_USER_DEPRECATED`.
- A proxy using directly `ProxyTrait`, instead of extending `Proxy\Standard`, must call `initializeStateProxy()` in
  its constructor.
- A state whose short class name is `StateDefault` is automatically enabled. A child stated class overloads a state
  of its parent by declaring a state with the same short class name.
- Do not add methods to `StateTrait` without updating lists `$ignoreMethods` of fixtures
  `tests/fixtures/Support/Extendable/Mother/Mother.php` and `MotherLegacy.php`. The magic method `__serialize()` is
  reserved to a builder called by the proxy's `SerializableTrait`.

### 5.2. How to write tests

- Test classes end with `Test.php`. Shared abstract test cases are named `Abstract*Tests.php` to be ignored by PHPUnit.
- `tests/States/Proxy/AbstractProxyTests.php` is executed for four proxies (`StandardTest`, `StandardTraitTest` and
  the two Doctrine tests, which add a line in the call stack): any new test must pass with all of them.
- Functional tests are inherited by theirs `*LegacyTest` twins, executed with `*Legacy` fixtures: a test added to a
  functional test must only use `build*()` methods, or must be written in a new test class.
- A bug fix starts with a test reproducing the bug, failing before the fix.
- Accepted coverage for new contributions is 90% (`CONTRIBUTING.md`), `src/` is currently fully covered.
