<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitebeb9c828807caa1f79a244b5888c3c0
{
    public static $prefixLengthsPsr4 = array (
        'X' => 
        array (
            'Xpressengine\\Plugins\\Rss\\' => 25,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Xpressengine\\Plugins\\Rss\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitebeb9c828807caa1f79a244b5888c3c0::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitebeb9c828807caa1f79a244b5888c3c0::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}