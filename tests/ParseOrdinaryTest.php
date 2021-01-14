<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Language\Tokens;
use BlazonCompiler\Compiler\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParseOrdinaryTest extends TestCase
{

    /**
     * @test
     * @dataProvider simpleOrdinary
     * @param string $blazon
     * @param IR $expected
     * @param bool $errors
     */
    public function checkResult(string $blazon, IR $expected, bool $errors = false): void
    {
        $result = Parser::parse($blazon);
        self::assertEquals($expected->getChildren(), $result->getChildren(), $blazon);
        self::assertEquals($errors, count($result->getMessages()) > 0, implode(', ', $result->getMessages()));
    }

    public function simpleOrdinary(): array
    {
        return [
            ["a bend or",
                new IR([
                    new NonTerm(Tokens::FULL_ORDINARY, [
                        new Term(Tokens::ONE, 'a'),
                        new Term(Tokens::ORDINARY, "bend"),
                        new NonTerm(Tokens::COLOR, [
                            new Term(Tokens::METAL, "or")
                        ])
                    ])
                ]),
                true // there is no field
            ],
            ["azure, a bend or",
                new IR([
                    new NonTerm(Tokens::FIELD, [
                        new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                    ]),
                    new Term(Tokens::COMMA, ','),
                    new NonTerm(Tokens::FULL_ORDINARY, [
                        new Term(Tokens::ONE, 'a'),
                        new Term(Tokens::ORDINARY, "bend"),
                        new NonTerm(Tokens::COLOR, [
                            new Term(Tokens::METAL, "or")
                        ])
                    ])
                ]),
            ],
            ["azure, a chief or",
                new IR([
                    new NonTerm(Tokens::FIELD, [
                        new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                    ]),
                    new Term(Tokens::COMMA, ','),
                    new NonTerm(Tokens::FULL_ORDINARY, [
                        new Term(Tokens::ONE, 'a'),
                        new Term(Tokens::ORDINARY, "chief"),
                        new NonTerm(Tokens::COLOR, [
                            new Term(Tokens::METAL, "or")
                        ])
                    ])
                ]),
            ]
        ];
    }
}
