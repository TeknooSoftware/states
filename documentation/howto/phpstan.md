Teknoo Software - States library - PHPStan
==========================================

To enable support of PHPStan in your project, add `phpstan/extension-installer` to your dev requirements, 
or create or edit your `phpstan.neon` and add

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
        tags:
            - 'phpstan.parser.richParserNodeVisitor'
