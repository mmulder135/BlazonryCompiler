<?php


namespace BlazonCompiler\Compiler;

class Language extends LanguageFunctions
{
    const COLOR = 'color';
    const ORDINARY = 'ordinary';
    const PREPOSITION = "preposition";
    const STR = 'string';
    const WS = 'whitespace';

    public function __construct(string $string)
    {
        $this->dictionary = array(
            self::WS => [
                ' '
            ],
            self::COLOR => [
                'argent', 'or',
                'azure', 'purpure', 'sable', 'vert', 'gules'
            ],
            self::ORDINARY => [
                'bend', 'bar'
            ],
            self::PREPOSITION => [
                'a'
            ],
            self::STR => [
                '\w'
            ]
        );

        parent::__construct($string);
    }
}
