<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 * (c) 2010 Arnaud Le Blanc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface implemented by token parser brokers.
 *
 * Token parser brokers allows to implement custom logic in the process of resolving a token parser for a given tag name.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface IfwTwig_TokenParserBrokerInterface
{
    /**
     * Gets a TokenParser suitable for a tag.
     *
     * @param string $tag A tag name
     *
     * @return null|IfwTwig_TokenParserInterface A IfwTwig_TokenParserInterface or null if no suitable TokenParser was found
     */
    public function getTokenParser($tag);

    /**
     * Calls IfwTwig_TokenParserInterface::setParser on all parsers the implementation knows of.
     *
     * @param IfwTwig_ParserInterface $parser A IfwTwig_ParserInterface interface
     */
    public function setParser(IfwTwig_ParserInterface $parser);

    /**
     * Gets the IfwTwig_ParserInterface.
     *
     * @return null|IfwTwig_ParserInterface A IfwTwig_ParserInterface instance or null
     */
    public function getParser();
}
