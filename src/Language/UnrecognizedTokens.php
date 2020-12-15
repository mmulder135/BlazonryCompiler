<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Language;

class UnrecognizedTokens extends Dictionary
{
    const STR = ':STR:';

    public function __construct()
    {
        $this->dictionary = [
            self::STR => [
                '\w+'
            ]
        ];
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function createRegex(): void
    {
        $this->regex = "((" . $this->dictionary[self::STR][0] . "))";
        $this->tokensArray = [self::STR];
    }
}
