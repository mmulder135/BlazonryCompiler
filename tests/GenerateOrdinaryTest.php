<?php
declare(strict_types=1);

namespace BlazonCompiler\Compiler\Tests;

use BlazonCompiler\Compiler\AST\IR;
use BlazonCompiler\Compiler\AST\NonTerm;
use BlazonCompiler\Compiler\AST\Term;
use BlazonCompiler\Compiler\Generator\CodeGenerator;
use BlazonCompiler\Compiler\Language\Tokens;
use DOMDocument;
use PHPUnit\Framework\TestCase;

class GenerateOrdinaryTest extends TestCase
{
    /**
     * @test
     * @dataProvider simpleOrdinaries
     * @dataProvider furOnOrdinary
     */
    public function test(IR $root, string $fileName, bool $errors = false): void
    {
        $g = new CodeGenerator();
        $xml = $g->generate($root)->saveXML();

        $result = new DOMDocument();
        $result->preserveWhiteSpace = false;
        $result->formatOutput = false;
        $result->loadXML($xml);

        $expected = new DOMDocument();
        $expected->preserveWhiteSpace = false;
        $expected->formatOutput = false;
        $path = dirname(__FILE__, 2)."/images/{$fileName}.svg";
        $expected->load($path);

        self::assertEquals($expected, $result);
        self::assertEquals($errors, count($root->getMessages()) > 0, implode(', ', $root->getMessages()));
    }

    public function simpleOrdinaries(): array
    {
        return [
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::PARTITION, [
                        new Term(Tokens::PER, 'per'),
                        new Term(Tokens::ORDINARY, 'pale')
                    ]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::METAL, 'argent')]),
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "or")
                    ]),
                ])
            ]), 'per_pale_argent_azure_bend_or'],
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "or")
                    ]),
                ])
            ]), 'azure_bend_or'],
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::FUR, 'vair')
                    ])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "or")
                    ]),
                ])
            ]), 'vair_bend_or'],
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::TINCTURE, 'azure')
                    ])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "pale"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "or")
                    ]),
                ])
            ]), 'azure_pale_or'],
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new Term(Tokens::SINISTER, "sinister"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "or")
                    ]),
                ])
            ]), 'azure_bend_sinister_or'],
        ];
    }

    public function furOnOrdinary(): array
    {
        return [
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::FUR, "vair")
                    ]),
                ])
            ]), 'azure_bend_error', true],
            [new IR([
                new NonTerm(Tokens::FIELD, [
                    new NonTerm(Tokens::COLOR, [new Term(Tokens::TINCTURE, 'azure')])
                ]),
                new NonTerm(Tokens::FULL_ORDINARY, [
                    new Term(Tokens::ORDINARY, "bend"),
                    new NonTerm(Tokens::COLOR, [
                        new Term(Tokens::METAL, "not_a_color")
                    ]),
                ])
            ]), 'azure_bend_error', true],
        ];
    }
}
