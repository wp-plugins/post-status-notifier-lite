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
 * Imports blocks defined in another template into the current template.
 *
 * <pre>
 * {% extends "base.html" %}
 *
 * {% use "blocks.html" %}
 *
 * {% block title %}{% endblock %}
 * {% block content %}{% endblock %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/templates.html#horizontal-reuse for details.
 */
class IfwTwig_TokenParser_Use extends IfwTwig_TokenParser
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
        $template = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();

        if (!$template instanceof IfwTwig_Node_Expression_Constant) {
            throw new IfwTwig_Error_Syntax('The template references in a "use" statement must be a string.', $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        $targets = array();
        if ($stream->test('with')) {
            $stream->next();

            do {
                $name = $stream->expect(IfwTwig_Token::NAME_TYPE)->getValue();

                $alias = $name;
                if ($stream->test('as')) {
                    $stream->next();

                    $alias = $stream->expect(IfwTwig_Token::NAME_TYPE)->getValue();
                }

                $targets[$name] = new IfwTwig_Node_Expression_Constant($alias, -1);

                if (!$stream->test(IfwTwig_Token::PUNCTUATION_TYPE, ',')) {
                    break;
                }

                $stream->next();
            } while (true);
        }

        $stream->expect(IfwTwig_Token::BLOCK_END_TYPE);

        $this->parser->addTrait(new IfwTwig_Node(array('template' => $template, 'targets' => new IfwTwig_Node($targets))));
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'use';
    }
}
