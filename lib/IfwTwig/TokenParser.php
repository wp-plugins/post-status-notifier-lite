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
 * Base class for all token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class IfwTwig_TokenParser implements IfwTwig_TokenParserInterface
{
    /**
     * @var IfwTwig_Parser
     */
    protected $parser;

    /**
     * Sets the parser associated with this token parser
     *
     * @param $parser A IfwTwig_Parser instance
     */
    public function setParser(IfwTwig_Parser $parser)
    {
        $this->parser = $parser;
    }
}
