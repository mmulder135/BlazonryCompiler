<?php


namespace BlazonCompiler\Compiler\Language;

class Separators extends Dictionary
{
    const WS = 'whitespace';
    const COMMA = 'comma';

    public function __construct()
    {
        $this->dictionary = [
            self::WS => [
                '\s+'
            ],
            self::COMMA => [
                ','
            ]
        ];
        parent::__construct();
    }
}
