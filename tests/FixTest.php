<?php

use NuernbergerMe\StringCsFixer\Exceptions\InvalidCode;
use NuernbergerMe\StringCsFixer\StringFixer;

it('takes a string and return itself')
    ->expect(StringFixer::from('<?php echo "Hello World";'))
    ->toBeInstanceOf(StringFixer::class);

it('throws an error for invalid code', function () {
    return StringFixer::from('ech"Hello World";')->fix();
})->throws(InvalidCode::class);

it('returns a formatted string')
    ->expect(StringFixer::from('namespace PhpCsFixer\Runner; function x() {echo "Hello World";}')->fix())
    ->appliedFixers()
    ->toHaveCount(5)
    ->__toString()
    ->toEqual('namespace PhpCsFixer\Runner;'.PHP_EOL.PHP_EOL.'function x()'.PHP_EOL.'{'.PHP_EOL."    echo 'Hello World';".PHP_EOL.'}'.PHP_EOL);
