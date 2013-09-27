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
 * Filters a section of a template by applying filters.
 *
 * <pre>
 * {% filter upper %}
 *  This text becomes uppercase
 * {% endfilter %}
 * </pre>
 */
class IfwTwig_TokenParser_Filter extends IfwTwig_TokenParser
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
        $name = $this->parser->getVarName();
        $ref = new IfwTwig_Node_Expression_BlockReference(new IfwTwig_Node_Expression_Constant($name, $token->getLine()), true, $token->getLine(), $this->getTag());

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(IfwTwig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(IfwTwig_Token::BLOCK_END_TYPE);

        $block = new IfwTwig_Node_Block($name, $body, $token->getLine());
        $this->parser->setBlock($name, $block);

        return new IfwTwig_Node_Print($filter, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(IfwTwig_Token $token)
    {
        return $token->test('endfilter');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'filter';
    }
}
