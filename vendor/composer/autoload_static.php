<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit500c7281507ba9a1cccdd9c47f70cfbe
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'App\\Actions\\AbstractAction' => __DIR__ . '/../..' . '/app/Actions/AbstractAction.php',
        'App\\Actions\\AvailableIf' => __DIR__ . '/../..' . '/app/Actions/AvailableIf.php',
        'App\\Contracts\\Action' => __DIR__ . '/../..' . '/app/Contracts/Action.php',
        'App\\Contracts\\Selector' => __DIR__ . '/../..' . '/app/Contracts/Selector.php',
        'App\\Models\\PaymentSystem' => __DIR__ . '/../..' . '/app/Models/PaymentSystem.php',
        'App\\Models\\PaymentType' => __DIR__ . '/../..' . '/app/Models/PaymentType.php',
        'App\\Models\\PaymentTypeSelector' => __DIR__ . '/../..' . '/app/Models/PaymentTypeSelector.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit500c7281507ba9a1cccdd9c47f70cfbe::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit500c7281507ba9a1cccdd9c47f70cfbe::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit500c7281507ba9a1cccdd9c47f70cfbe::$classMap;

        }, null, ClassLoader::class);
    }
}
