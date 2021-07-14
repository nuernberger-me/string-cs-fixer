<?php

use NuernbergerMe\StringCsFixer\Exceptions\InvalidCode;
use NuernbergerMe\StringCsFixer\StringFixer;

it('takes a string and return itself')
    ->expect(StringFixer::from('<?php echo "Hello World";'))
    ->toBeInstanceOf(StringFixer::class);

it('throws an error for invalid code', function () {
    return StringFixer::from('<?php ech"Hello World";')->fix();
})->throws(InvalidCode::class);

it('returns a formatted string')
    ->expect(StringFixer::from('<?php echo"Hello World";')->fix())
    ->toEqual("<?php echo 'Hello World';".PHP_EOL);
