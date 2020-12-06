<?php


namespace BlazonCompiler\Compiler;

use Exception;

abstract class LanguageFunctions
{
    protected $dictionary = array();
    protected $regex;
    protected $offsetToToken;
    protected $result = array();
    protected $tokens = array();

    public function __construct(string $string)
    {
        $this->createRegex();
        $this->tokenize($string);
    }

    public function getTokens():array
    {
        return $this->tokens;
    }

    /**
     * @param string $input
     * @throws Exception
     */
    protected function tokenize(string $input)
    {
        $words = array_filter(explode(' ', str_replace(',', ' ,', $input)), 'strlen');
        foreach ($words as $word) {
            $this->tokenizeWord($word);
        }
    }
//    /**
//     * @param string $string
//     * @return array
//     * @throws Exception
//     */
//    protected function tokenize(string $string):void
//    {
//        $offset = 0;
//        while (isset($string[$offset])) {
//            $match = preg_match($this->regex, $string, $matches, null, $offset);
//            if (!$match) {
//                throw new Exception("Unknown character '{$string[$offset]}'");
//            } else {
//                // find the first non-empty element (but skipping $matches[0]) using a quick for loop
//                for ($i = 1; '' === $matches[$i]; ++$i);
//
//                $this->result[] = array($matches[0], $this->offsetToToken[$i - 1]);
//                $this->tokens[] = $this->offsetToToken[$i - 1];
//
//                $offset += strlen($matches[0]);
//            }
//        }
//    }
    /**
     * Goes through regex and finds the first expression that matches.
     * Returns token when given valid token.
     * Returns empty string when given empty string.
     *
     * @param string $word
     * @return string
     * @throws Exception
     */
    protected function tokenizeWord(string $word): string
    {
        if ($word == '') {
            return '';
        }
        $match = preg_match($this->regex, $word, $matches);
        if (!$match) {
            throw new Exception("Unknown character '{$word}'");
        }

        //Quick and dirty forloop to get correct index
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        for ($i = 1; '' === $matches[$i]; ++$i);

        $token = $this->offsetToToken[$i - 1];
        $this->result[] = array($matches[0], $token);
        $this->tokens[] = $token;
        return $token;
    }

    protected function createRegex()
    {
        $tokenMap = array();
        foreach ($this->dictionary as $name => $values) {
            $tokenMap[$name] = implode('|', $values);
        }
        $this->regex = '((^' . implode('$)|(^', array_values($tokenMap)) . '$))A';
        $this->offsetToToken = array_keys($tokenMap);
    }
}
