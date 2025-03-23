<?php

namespace Faerber\PdfToZpl;

use ReflectionClass;
use Imagick;
use ImagickPixel;
use Stringable;

/**
* Forward methods onto a class
* This is used to tell the LSP that I know the class is present
*/
abstract class Stub implements Stringable
{
    private mixed $inner;

    public function __construct(...$args)
    {
        $klass = static::className();
        $this->inner = new $klass(...$args);
    }

    /** @return class-string */
    abstract public static function className(): string;

    /** Look up a constant */
    public static function constant(string $name)
    {
        $klass = static::className();
        $reflector = new ReflectionClass(
            new $klass()
        );
        return $reflector->getConstant($name);
    }

    public function __call($name, $args)
    {
        return $this->inner->{$name}(...$args);
    }

    public static function __callStatic($name, $args)
    {
        $klass = static::className();
        return $klass::{$name}(...$args);
    }

    public function __get($name)
    {
        return $this->inner->{$name};
    }

    public function __set($name, $value)
    {
        $this->inner->{$name} = $value;
    }

    public function __toString(): string
    {
        return $this->inner->__toString();
    }

    public function inner()
    {
        return $this->inner;
    }
}

class ImagickStub extends Stub
{
    public static function className(): string
    {
        /** @disregard intelephense(P1009) */
        return Imagick::class;
    }
}

class ImagickPixelStub extends Stub
{
    public static function className(): string
    {
        /** @disregard intelephense(P1009) */
        return ImagickPixel::class;
    }
}
