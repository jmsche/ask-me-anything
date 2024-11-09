<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitConstructFixer;
use PhpCsFixer\Fixer\ReturnNotation\NoUselessReturnFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/database/migrations',
        __DIR__ . '/public/index.php',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()
    ->withCache('var/ecs-cache')
    ->withRules([
        DirConstantFixer::class,
        NoUselessElseFixer::class,
        NoUselessReturnFixer::class,
        PhpUnitConstructFixer::class,
        StrictComparisonFixer::class,
        StrictParamFixer::class,
        DeclareStrictTypesFixer::class,
    ])
    ->withPhpCsFixerSets(perCS20: true, symfonyRisky: true);
