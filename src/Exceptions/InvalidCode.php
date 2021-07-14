<?php

namespace NuernbergerMe\StringCsFixer\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use PhpCsFixer\Linter\LintingException;

class InvalidCode extends Exception
{
    public static function linter(LintingException $exception): static
    {
        return new static($exception->getMessage(), $exception->getCode());
    }
}
