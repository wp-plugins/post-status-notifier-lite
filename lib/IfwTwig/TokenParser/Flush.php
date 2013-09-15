<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Flushes the output to the client.
 *
 * @see flush()
 */
class IfwTwig_TokenParser_Flush extends IfwTwig_TokenParser
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
        $this->parser->getStream()->expect(IfwTwig_Token::BLOCK_END_TYPE);

        return new IfwTwig_Node_Flush($token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'flush';
    }
}
