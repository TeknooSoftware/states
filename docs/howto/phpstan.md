#Teknoo Software - States library - PHPStan

To enable support of PHpStan in your project, create or edit your `phpstan.neon` and add

    services:
        -
            class: Teknoo\States\PHPStan\MethodsClassExtension
            arguments:
                parser: @defaultAnalysisParser
            tags:
                - phpstan.broker.methodsClassReflectionExtension
    
        -
            class: Teknoo\States\PHPStan\Analyser\ASTVisitor
            arguments:
                parser: @defaultAnalysisParser
    
        -
            class: Teknoo\States\PHPStan\Analyser\NodeScopeResolver
            autowired: PHPStan\Analyser\NodeScopeResolver
            arguments:
                parser: @defaultAnalysisParser
                classReflector: @nodeScopeResolverClassReflector
                polluteScopeWithLoopInitialAssignments: %polluteScopeWithLoopInitialAssignments%
                polluteScopeWithAlwaysIterableForeach: %polluteScopeWithAlwaysIterableForeach%
                earlyTerminatingMethodCalls: %earlyTerminatingMethodCalls%
                earlyTerminatingFunctionCalls: %earlyTerminatingFunctionCalls%
                implicitThrows: %exceptions.implicitThrows%

If you had already PHPStan support with PHPStan pre 1.0 version, you must remove the entry `scopeClass` 
under `parameters` key.