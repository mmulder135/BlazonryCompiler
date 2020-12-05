<?php


namespace BlazonCompiler\Compiler;

class Language extends LanguageFunctions
{
    const COLOR = 'color';
    const ORDINARY = 'ordinary';
    const PREPOSITION = "preposition";
    const STR = 'string';
    const COMMA = 'comma';

    public function __construct(string $string)
    {
        $this->dictionary = array(
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
            self::COMMA => [
                ',',';'
            ],
            self::STR => [
                '\S*'
            ]
        );

        parent::__construct($string);
    }
}
