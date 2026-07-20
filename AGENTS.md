# AGENTS.md — Operating doctrine for Teknoo/states

This document defines the operating procedures, technical stack, and verification protocols for AI agents working on the
**Teknoo/states** project.

## 1. Project Overview

**Teknoo/states** is a PHP library designed to implement the **State Pattern**. It allows developers to create objects
whose behavior changes depending on its internal state, avoiding complex conditional logic and improving
maintainability.

- **Core Goal**: Provide a robust, clean, and maintainable implementation of the State pattern in PHP.
- **Key Features**: State inheritance, automated state switching based on properties, and compatibility with Doctrine.
- **Core Dependency**: `teknoo/immutable`.

## 2. Technical Stack

- **Language**: PHP 8.4+
- **Typing**: Strict typing is enforced throughout the codebase.
- **Architecture**:
    - `src/State`: Core state interfaces and abstract classes.
    - `src/Proxy`: Proxy implementations for managing state transitions.
    - `src/Automated`: Automated assertion and transition logic.
    - `infrastructures/`: Implementations for Doctrine and PHPStan.
- **Testing & Quality**:
    - **PHPUnit**: Unit and integration testing.
    - **PHPStan**: Static analysis for type safety.
    - **Rector**: Automated refactoring.
    - **PHP_CodeSniffer**: Code style and linting.

## 3. Agent Workflow

All agents must follow the standard deployment loop: **Understand $\rightarrow$ Explore $\rightarrow$ Plan $\rightarrow$
Implement $\rightarrow$ Review $\rightarrow$ Verify $\rightarrow$ Report**.

### 3.1. Verification Discipline

No task is considered "done" until the following checks are successfully performed:

| Check Type          | Command                      | Requirement                                     |
|:--------------------|:-----------------------------|:------------------------------------------------|
| **Unit Tests**      | `vendor/bin/phpunit`         | All tests must pass without failures or errors. |
| **Static Analysis** | `vendor/bin/phpstan analyse` | Zero errors reported.                           |
| **Linting**         | `vendor/bin/phpcs`           | Code must adhere to project style guidelines.   |
| **Refactoring**     | `vendor/bin/rector process`  | Must not introduce regressions in behavior.     |

*Note: If a check fails, the agent must fix the issue and re-run the verification.*

## 4. Development Rules

- **Strict Typing**: Always use `declare(strict_types=1);` in all new PHP files.
- **Immutability**: Be mindful of the interaction with `teknoo/immutable`.
- **Documentation**: Any changes to public APIs must be accompanied by documentation updates in `documentation/`.
- **Namespace Compliance**: All new classes must follow the established namespace structure (`Teknoo\States\...`).
- **Complexity**: Avoid monolithic state classes; favor small, single-responsibility state implementations.
