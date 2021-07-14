<?php

namespace NuernbergerMe\StringCsFixer;

use NuernbergerMe\StringCsFixer\Exceptions\InvalidCode;
use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerFactory;
use PhpCsFixer\Linter\LintingException;
use PhpCsFixer\Linter\TokenizerLinter;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;

class StringFixer
{
    public static function from(string $string): self
    {
        return new self($string);
    }

    protected function __construct(protected string $string) {}

    public function fix(): string
    {
        $this->validate();

        $tokens = Tokens::fromCode($this->string);

        $filename = tempnam(sys_get_temp_dir(), 'string-cs-fixer').'.php';
        file_put_contents($filename,$this->string);
        $file = new SplFileInfo($filename);

        foreach ($this->fixers() as $fixer) {
            if (! $fixer instanceof AbstractFixer && ! $fixer->isCandidate($tokens)) {
                continue;
            }

            $fixer->fix($file, $tokens);

            if ($tokens->isChanged()) {
                $tokens->clearEmptyTokens();
                $tokens->clearChanged();
            }
        }

        @unlink($filename);

        return $tokens->generateCode();
    }

    protected function validate(): void
    {
        try {
            (new TokenizerLinter)->lintSource($this->string)->check();
        } catch (LintingException $exception) {
            throw InvalidCode::linter($exception);
        }
    }

    protected function fixers(): array
    {
        $fixerFactory = new FixerFactory();
        $fixerFactory->registerBuiltInFixers();

        return $fixerFactory
            ->useRuleSet(new RuleSet([
                '@Symfony' => true,
            ]))
            ->setWhitespacesConfig(new WhitespacesFixerConfig(lineEnding: PHP_EOL))
            ->getFixers();
    }
}
