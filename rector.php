<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Assign\CombinedAssignRector;
use Rector\CodeQuality\Rector\BooleanNot\SimplifyDeMorganBinaryRector;
use Rector\CodeQuality\Rector\Catch_\ThrowWithPreviousExceptionRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodeQuality\Rector\Concat\JoinStringConcatRector;
use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\Foreach_\ForeachToInArrayRector;
use Rector\CodeQuality\Rector\Foreach_\SimplifyForeachToCoalescingRector;
use Rector\CodeQuality\Rector\FuncCall\FloatvalToTypeCastRector;
use Rector\CodeQuality\Rector\FuncCall\InlineIsAInstanceOfRector;
use Rector\CodeQuality\Rector\FuncCall\IntvalToTypeCastRector;
use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\Identical\SimplifyConditionsRector;
use Rector\CodeQuality\Rector\If_\ConsecutiveNullCompareReturnsToNullCoalesceQueueRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\CodeQuality\Rector\NotEqual\CommonNotEqualRector;
use Rector\CodeQuality\Rector\NullsafeMethodCall\CleanupUnneededNullsafeOperatorRector;
use Rector\CodeQuality\Rector\Ternary\UnnecessaryTernaryExpressionRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\ClassMethod\DataProviderArrayItemsNewlinedRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\FuncCall\StrictArraySearchRector;
use Rector\CodingStyle\Rector\Plus\UseIncrementAssignRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Assign\RemoveDoubleAssignRector;
use Rector\DeadCode\Rector\Assign\RemoveUnusedVariableAssignRector;
use Rector\DeadCode\Rector\ClassConst\RemoveUnusedPrivateClassConstantRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPromotedPropertyRector;
use Rector\DeadCode\Rector\Concat\RemoveConcatAutocastRector;
use Rector\DeadCode\Rector\Foreach_\RemoveUnusedForeachKeyRector;
use Rector\DeadCode\Rector\If_\RemoveUnusedNonEmptyArrayBeforeForeachRector;
use Rector\DeadCode\Rector\Property\RemoveUnusedPrivatePropertyRector;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\DeadCode\Rector\Stmt\RemoveUnreachableStatementRector;
use Rector\DeadCode\Rector\TryCatch\RemoveDeadTryCatchRector;
use Rector\Doctrine\CodeQuality\Rector\Class_\InitializeDefaultEntityCollectionRector;
use Rector\Doctrine\CodeQuality\Rector\ClassMethod\MakeEntitySetterNullabilityInSyncWithPropertyRector;
use Rector\Doctrine\CodeQuality\Rector\Property\ChangeBigIntEntityPropertyToIntTypeRector;
use Rector\Doctrine\CodeQuality\Rector\Property\CorrectDefaultTypesOnEntityPropertyRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromColumnTypeRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromDoctrineCollectionRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromToManyRelationTypeRector;
use Rector\Doctrine\CodeQuality\Rector\Property\TypedPropertyFromToOneRelationTypeRector;
use Rector\EarlyReturn\Rector\If_\RemoveAlwaysElseRector;
use Rector\EarlyReturn\Rector\StmtsAwareInterface\ReturnEarlyIfVariableRector;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php70\Rector\FuncCall\RandomFunctionRector;
use Rector\Php70\Rector\If_\IfToSpaceshipRector;
use Rector\Php70\Rector\MethodCall\ThisCallOnStaticMethodToStaticCallRector;
use Rector\Php70\Rector\StmtsAwareInterface\IfIssetToCoalescingRector;
use Rector\Php70\Rector\Ternary\TernaryToSpaceshipRector;
use Rector\Php71\Rector\ClassConst\PublicConstantVisibilityRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php72\Rector\Unset_\UnsetCastRector;
use Rector\Php73\Rector\FuncCall\ArrayKeyFirstLastRector;
use Rector\Php73\Rector\FuncCall\StringifyStrNeedlesRector;
use Rector\Php74\Rector\Assign\NullCoalescingOperatorRector;
use Rector\Php74\Rector\FuncCall\ArraySpreadInsteadOfArrayMergeRector;
use Rector\Php74\Rector\Ternary\ParenthesizeNestedTernaryRector;
use Rector\Php80\Rector\ClassConstFetch\ClassOnThisVariableObjectRector;
use Rector\Php80\Rector\ClassMethod\AddParamBasedOnParentClassMethodRector;
use Rector\Php80\Rector\FuncCall\ClassOnObjectRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php81\Rector\ClassMethod\NewInInitializerRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Php82\Rector\Class_\ReadOnlyClassRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Privatization\Rector\Property\PrivatizeFinalClassPropertyRector;
use Rector\Symfony\CodeQuality\Rector\BinaryOp\ResponseStatusCodeRector;
use Rector\Symfony\CodeQuality\Rector\ClassMethod\ParamTypeFromRouteRequiredRegexRector;
use Rector\Symfony\CodeQuality\Rector\ClassMethod\RemoveUnusedRequestParamRector;
use Rector\Symfony\CodeQuality\Rector\MethodCall\LiteralGetToRequestClassConstantRector;
use Rector\Symfony\Symfony30\Rector\MethodCall\ChangeStringCollectionOptionToConstantRector;
use Rector\Symfony\Symfony30\Rector\MethodCall\ReadOnlyOptionToAttributeRector;
use Rector\Symfony\Symfony30\Rector\MethodCall\StringFormTypeToClassRector;
use Rector\Symfony\Symfony33\Rector\ClassConstFetch\ConsoleExceptionToErrorEventConstantRector;
use Rector\Symfony\Symfony42\Rector\MethodCall\ContainerGetToConstructorInjectionRector;
use Rector\Symfony\Symfony51\Rector\ClassMethod\CommandConstantReturnCodeRector;
use Rector\Symfony\Symfony61\Rector\Class_\CommandPropertyToAttributeRector;
use Rector\Symfony\Symfony62\Rector\MethodCall\SimplifyFormRenderingRector;
use Rector\Symfony\Twig134\Rector\Return_\SimpleFunctionAndFilterRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);

    // register a single rule
    $rectorConfig->rules([
        // Common rules:
        InlineConstructorDefaultToPropertyRector::class,
        ReadOnlyClassRector::class,
        CleanupUnneededNullsafeOperatorRector::class,
        CombinedAssignRector::class,
        CommonNotEqualRector::class,
        ConsecutiveNullCompareReturnsToNullCoalesceQueueRector::class,
        ExplicitBoolCompareRector::class,
        FloatvalToTypeCastRector::class,
        ForeachToInArrayRector::class,
        InlineIfToExplicitIfRector::class,
        InlineIsAInstanceOfRector::class,
        IntvalToTypeCastRector::class,
        JoinStringConcatRector::class,
        ReturnTypeFromStrictScalarReturnExprRector::class,
        ShortenElseIfRector::class,
        SimplifyConditionsRector::class,
        SimplifyDeMorganBinaryRector::class,
        SimplifyEmptyCheckOnEmptyArrayRector::class,
        SimplifyForeachToCoalescingRector::class,
        SimplifyRegexPatternRector::class,
        ThrowWithPreviousExceptionRector::class,
        UnnecessaryTernaryExpressionRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        DataProviderArrayItemsNewlinedRector::class,
        StaticArrowFunctionRector::class,
        StrictArraySearchRector::class,
        SymplifyQuoteEscapeRector::class,
        UseIncrementAssignRector::class,
        WrapEncapsedVariableInCurlyBracesRector::class,
        RemoveConcatAutocastRector::class,
        RemoveDeadTryCatchRector::class,
        RemoveDoubleAssignRector::class,
        RemoveUnreachableStatementRector::class,
        RemoveUnusedForeachKeyRector::class,
        RemoveUnusedNonEmptyArrayBeforeForeachRector::class,
        RemoveUnusedPrivateClassConstantRector::class,
        RemoveUnusedPrivateMethodRector::class,
        RemoveUnusedPrivatePropertyRector::class,
        RemoveUnusedPromotedPropertyRector::class,
        RemoveUnusedVariableAssignRector::class,
        RemoveUselessVarTagRector::class,
        RemoveAlwaysElseRector::class,
        ReturnEarlyIfVariableRector::class,
        TernaryToElvisRector::class,
        LongArrayToShortArrayRector::class,
        IfIssetToCoalescingRector::class,
        IfToSpaceshipRector::class,
        RandomFunctionRector::class,
        TernaryToSpaceshipRector::class,
        ThisCallOnStaticMethodToStaticCallRector::class,
        PublicConstantVisibilityRector::class,
        RemoveExtraParametersRector::class,
        UnsetCastRector::class,
        ArrayKeyFirstLastRector::class,
        StringifyStrNeedlesRector::class,
        ArraySpreadInsteadOfArrayMergeRector::class,
        NullCoalescingOperatorRector::class,
        ParenthesizeNestedTernaryRector::class,
        AddParamBasedOnParentClassMethodRector::class,
        ChangeSwitchToMatchRector::class,
        ClassOnObjectRector::class,
        ClassOnThisVariableObjectRector::class,
        NewInInitializerRector::class,
        ReadOnlyPropertyRector::class,
        PrivatizeFinalClassMethodRector::class,
        PrivatizeFinalClassPropertyRector::class,
        PrivatizeLocalGetterToPropertyRector::class,
        // Symfony rules:
        ChangeStringCollectionOptionToConstantRector::class,
        CommandConstantReturnCodeRector::class,
        CommandPropertyToAttributeRector::class,
        ConsoleExceptionToErrorEventConstantRector::class,
        ContainerGetToConstructorInjectionRector::class,
        LiteralGetToRequestClassConstantRector::class,
        ParamTypeFromRouteRequiredRegexRector::class,
        ReadOnlyOptionToAttributeRector::class,
        RemoveUnusedRequestParamRector::class,
        ResponseStatusCodeRector::class,
        SimpleFunctionAndFilterRector::class,
        SimplifyFormRenderingRector::class,
        StringFormTypeToClassRector::class,
        // Doctrine:
        ChangeBigIntEntityPropertyToIntTypeRector::class,
        CorrectDefaultTypesOnEntityPropertyRector::class,
        InitializeDefaultEntityCollectionRector::class,
        MakeEntitySetterNullabilityInSyncWithPropertyRector::class,
        TypedPropertyFromColumnTypeRector::class,
        TypedPropertyFromDoctrineCollectionRector::class,
        TypedPropertyFromToManyRelationTypeRector::class,
        TypedPropertyFromToOneRelationTypeRector::class,
    ]);
};
