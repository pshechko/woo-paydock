<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc43548558c68d2f38f8f6aea1732f7b5
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Abraham\\TwitterOAuth\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Abraham\\TwitterOAuth\\' => 
        array (
            0 => __DIR__ . '/..' . '/abraham/twitteroauth/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc43548558c68d2f38f8f6aea1732f7b5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc43548558c68d2f38f8f6aea1732f7b5::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
