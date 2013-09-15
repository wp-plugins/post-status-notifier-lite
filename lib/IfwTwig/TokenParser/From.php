<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Imports macros.
 *
 * <pre>
 *   {% from 'forms.html' import forms %}
 * </pre>
 */
class IfwTwig_TokenParser_From extends IfwTwig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param IfwTwig_Token $token A IfwTwig_Token instance
     *
     * @return IfwTwig_NodeInterface A IfwTwig_NodeInterface instance
     */
    public function parse(IfwTwig_Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect('import');

        $targets = array();
        do {
            $name = $stream->expect(IfwTwig_Token::NAME_TYPE)->getValue();

            $alias = $name;
            if ($stream->test('as')) {
                $stream->next();

                $alias = $stream->expect(IfwTwig_Token::NAME_TYPE)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->test(IfwTwig_Token::PUNCTUATION_TYPE, ',')) {
                break;
            }

            $stream->next();
        } while (true);

        $stream->expect(IfwTwig_Token::BLOCK_END_TYPE);

        $node = new IfwTwig_Node_Import($macro, new IfwTwig_Node_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('macro', $alias, $name, $node->getNode('var'));
        }

        return $node;
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'from';
    }
}
