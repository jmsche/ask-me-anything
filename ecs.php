<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ConstantNotation\NativeConstantInvocationFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\ConcatSpaceFixer;
use PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Semicolon\SemicolonAfterInstructionFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/bin/console',
        __DIR__ . '/config',
        __DIR__ . '/database/migrations',
        __DIR__ . '/public/index.php',
        __DIR__ . '/src',
        __DIR__ . '/tests',
        __DIR__ . '/ecs.php',
    ]);
    $containerConfigurator->import(SetList::SYMFONY);
    $containerConfigurator->import(SetList::SYMFONY_RISKY);

    $services = $containerConfigurator->services();
    $services->set(DirConstantFixer::class);
    $services->set(ConcatSpaceFixer::class)
        ->call('configure', [[
            'spacing' => 'one',
        ]]);
    $services->set(ModernizeTypesCastingFixer::class);
    $services->set(EchoTagSyntaxFixer::class)
        ->call('configure', [[
            'format' => 'long',
        ]]);
    $services->set(NoUselessElseFixer::class);
    $services->set(NoUselessReturnFixer::class);
    $services->set(NativeFunctionInvocationFixer::class)
        ->call('configure', [[
            'include' => ['@compiler_optimized'],
        ]]);
    $services->set(OrderedClassElementsFixer::class);
    $services->set(OrderedImportsFixer::class);
    $services->set(PhpUnitConstructFixer::class);
    $services->set(ArraySyntaxFixer::class)
        ->call('configure', [[
            'syntax' => 'short',
        ]]);
    $services->set(BinaryOperatorSpacesFixer::class)
        ->call('configure', [[
            'operators' => [
                '=>' => 'align_single_space',
                '|'  => null,
            ],
        ]]);
    $services->set(SemicolonAfterInstructionFixer::class);
    $services->set(StrictComparisonFixer::class);
    $services->set(StrictParamFixer::class);
    $services->set(YodaStyleFixer::class)
        ->call('configure', [[
            'always_move_variable' => true,
            'equal'                => true,
            'identical'            => true,
            'less_and_greater'     => true,
        ]]);
    $services->set(DeclareStrictTypesFixer::class);
    $services->set(NativeConstantInvocationFixer::class)
        ->call('configure', [[
            'fix_built_in' => true,
            'include'      => ['DIRECTORY_SEPARATOR', 'PHP_SAPI', 'PHP_VERSION_ID'],
            'scope'        => 'namespaced',
        ]]);
};
