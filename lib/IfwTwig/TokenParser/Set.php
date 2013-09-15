<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Defines a variable.
 *
 * <pre>
 *  {% set foo = 'foo' %}
 *
 *  {% set foo = [1, 2] %}
 *
 *  {% set foo = {'foo': 'bar'} %}
 *
 *  {% set foo = 'foo' ~ 'bar' %}
 *
 *  {% set foo, bar = 'foo', 'bar' %}
 *
 *  {% set foo %}Some content{% endset %}
 * </pre>
 */
class IfwTwig_TokenParser_Set extends IfwTwig_TokenParser
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
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();
        $names = $this->parser->getExpressionParser()->parseAssignmentExpression();

        $capture = false;
        if ($stream->test(IfwTwig_Token::OPERATOR_TYPE, '=')) {
            $stream->next();
            $values = $this->parser->getExpressionParser()->parseMultitargetExpression();

            $stream->expect(IfwTwig_Token::BLOCK_END_TYPE);

            if (count($names) !== count($values)) {
                throw new IfwTwig_Error_Syntax("When using set, you must have the same number of variables and assignments.", $stream->getCurrent()->getLine(), $stream->getFilename());
            }
        } else {
            $capture = true;

            if (count($names) > 1) {
                throw new IfwTwig_Error_Syntax("When using set with a block, you cannot have a multi-target.", $stream->getCurrent()->getLine(), $stream->getFilename());
            }

            $stream->expect(IfwTwig_Token::BLOCK_END_TYPE);

            $values = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            $stream->expect(IfwTwig_Token::BLOCK_END_TYPE);
        }

        return new IfwTwig_Node_Set($capture, $names, $values, $lineno, $this->getTag());
    }

    public function decideBlockEnd(IfwTwig_Token $token)
    {
        return $token->test('endset');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'set';
    }
}
