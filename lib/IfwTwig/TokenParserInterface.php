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
 * Interface implemented by token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface IfwTwig_TokenParserInterface
{
    /**
     * Sets the parser associated with this token parser
     *
     * @param $parser A IfwTwig_Parser instance
     */
    public function setParser(IfwTwig_Parser $parser);

    /**
     * Parses a token and returns a node.
     *
     * @param IfwTwig_Token $token A IfwTwig_Token instance
     *
     * @return IfwTwig_NodeInterface A IfwTwig_NodeInterface instance
     */
    public function parse(IfwTwig_Token $token);

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag();
}
