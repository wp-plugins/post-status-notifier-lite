<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Default parser implementation.
 *
 * @package twig
 * @author  Fabien Potencier <fabien@symfony.com>
 */
class IfwTwig_Parser implements IfwTwig_ParserInterface
{
    protected $stack = array();
    protected $stream;
    protected $parent;
    protected $handlers;
    protected $visitors;
    protected $expressionParser;
    protected $blocks;
    protected $blockStack;
    protected $macros;
    protected $env;
    protected $reservedMacroNames;
    protected $importedSymbols;
    protected $traits;
    protected $embeddedTemplates = array();

    /**
     * Constructor.
     *
     * @param IfwTwig_Environment $env A IfwTwig_Environment instance
     */
    public function __construct(IfwTwig_Environment $env)
    {
        $this->env = $env;
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    public function getVarName()
    {
        return sprintf('__internal_%s', hash('sha1', uniqid(mt_rand(), true), false));
    }

    public function getFilename()
    {
        return $this->stream->getFilename();
    }

    /**
     * Converts a token stream to a node tree.
     *
     * @param IfwTwig_TokenStream $stream A token stream instance
     *
     * @return IfwTwig_Node_Module A node tree
     */
    public function parse(IfwTwig_TokenStream $stream, $test = null, $dropNeedle = false)
    {
        // push all variables into the stack to keep the current state of the parser
        $vars = get_object_vars($this);
        unset($vars['stack'], $vars['env'], $vars['handlers'], $vars['visitors'], $vars['expressionParser']);
        $this->stack[] = $vars;

        // tag handlers
        if (null === $this->handlers) {
            $this->handlers = $this->env->getTokenParsers();
            $this->handlers->setParser($this);
        }

        // node visitors
        if (null === $this->visitors) {
            $this->visitors = $this->env->getNodeVisitors();
        }

        if (null === $this->expressionParser) {
            $this->expressionParser = new IfwTwig_ExpressionParser($this, $this->env->getUnaryOperators(), $this->env->getBinaryOperators());
        }

        $this->stream = $stream;
        $this->parent = null;
        $this->blocks = array();
        $this->macros = array();
        $this->traits = array();
        $this->blockStack = array();
        $this->importedSymbols = array(array());
        $this->embeddedTemplates = array();

        try {
            $body = $this->subparse($test, $dropNeedle);

            if (null !== $this->parent) {
                if (null === $body = $this->filterBodyNodes($body)) {
                    $body = new IfwTwig_Node();
                }
            }
        } catch (IfwTwig_Error_Syntax $e) {
            if (!$e->getTemplateFile()) {
                $e->setTemplateFile($this->getFilename());
            }

            if (!$e->getTemplateLine()) {
                $e->setTemplateLine($this->stream->getCurrent()->getLine());
            }

            throw $e;
        }

        $node = new IfwTwig_Node_Module(new IfwTwig_Node_Body(array($body)), $this->parent, new IfwTwig_Node($this->blocks), new IfwTwig_Node($this->macros), new IfwTwig_Node($this->traits), $this->embeddedTemplates, $this->getFilename());

        $traverser = new IfwTwig_NodeTraverser($this->env, $this->visitors);

        $node = $traverser->traverse($node);

        // restore previous stack so previous parse() call can resume working
        foreach (array_pop($this->stack) as $key => $val) {
            $this->$key = $val;
        }

        return $node;
    }

    public function subparse($test, $dropNeedle = false)
    {
        $lineno = $this->getCurrentToken()->getLine();
        $rv = array();
        while (!$this->stream->isEOF()) {
            switch ($this->getCurrentToken()->getType()) {
                case IfwTwig_Token::TEXT_TYPE:
                    $token = $this->stream->next();
                    $rv[] = new IfwTwig_Node_Text($token->getValue(), $token->getLine());
                    break;

                case IfwTwig_Token::VAR_START_TYPE:
                    $token = $this->stream->next();
                    $expr = $this->expressionParser->parseExpression();
                    $this->stream->expect(IfwTwig_Token::VAR_END_TYPE);
                    $rv[] = new IfwTwig_Node_Print($expr, $token->getLine());
                    break;

                case IfwTwig_Token::BLOCK_START_TYPE:
                    $this->stream->next();
                    $token = $this->getCurrentToken();

                    if ($token->getType() !== IfwTwig_Token::NAME_TYPE) {
                        throw new IfwTwig_Error_Syntax('A block must start with a tag name', $token->getLine(), $this->getFilename());
                    }

                    if (null !== $test && call_user_func($test, $token)) {
                        if ($dropNeedle) {
                            $this->stream->next();
                        }

                        if (1 === count($rv)) {
                            return $rv[0];
                        }

                        return new IfwTwig_Node($rv, array(), $lineno);
                    }

                    $subparser = $this->handlers->getTokenParser($token->getValue());
                    if (null === $subparser) {
                        if (null !== $test) {
                            $error = sprintf('Unexpected tag name "%s"', $token->getValue());
                            if (is_array($test) && isset($test[0]) && $test[0] instanceof IfwTwig_TokenParserInterface) {
                                $error .= sprintf(' (expecting closing tag for the "%s" tag defined near line %s)', $test[0]->getTag(), $lineno);
                            }

                            throw new IfwTwig_Error_Syntax($error, $token->getLine(), $this->getFilename());
                        }

                        $message = sprintf('Unknown tag name "%s"', $token->getValue());
                        if ($alternatives = $this->env->computeAlternatives($token->getValue(), array_keys($this->env->getTags()))) {
                            $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
                        }

                        throw new IfwTwig_Error_Syntax($message, $token->getLine(), $this->getFilename());
                    }

                    $this->stream->next();

                    $node = $subparser->parse($token);
                    if (null !== $node) {
                        $rv[] = $node;
                    }
                    break;

                default:
                    throw new IfwTwig_Error_Syntax('Lexer or parser ended up in unsupported state.', 0, $this->getFilename());
            }
        }

        if (1 === count($rv)) {
            return $rv[0];
        }

        return new IfwTwig_Node($rv, array(), $lineno);
    }

    public function addHandler($name, $class)
    {
        $this->handlers[$name] = $class;
    }

    public function addNodeVisitor(IfwTwig_NodeVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    public function getBlockStack()
    {
        return $this->blockStack;
    }

    public function peekBlockStack()
    {
        return $this->blockStack[count($this->blockStack) - 1];
    }

    public function popBlockStack()
    {
        array_pop($this->blockStack);
    }

    public function pushBlockStack($name)
    {
        $this->blockStack[] = $name;
    }

    public function hasBlock($name)
    {
        return isset($this->blocks[$name]);
    }

    public function getBlock($name)
    {
        return $this->blocks[$name];
    }

    public function setBlock($name, $value)
    {
        $this->blocks[$name] = new IfwTwig_Node_Body(array($value), array(), $value->getLine());
    }

    public function hasMacro($name)
    {
        return isset($this->macros[$name]);
    }

    public function setMacro($name, IfwTwig_Node_Macro $node)
    {
        if (null === $this->reservedMacroNames) {
            $this->reservedMacroNames = array();
            $r = new ReflectionClass($this->env->getBaseTemplateClass());
            foreach ($r->getMethods() as $method) {
                $this->reservedMacroNames[] = $method->getName();
            }
        }

        if (in_array($name, $this->reservedMacroNames)) {
            throw new IfwTwig_Error_Syntax(sprintf('"%s" cannot be used as a macro name as it is a reserved keyword', $name), $node->getLine(), $this->getFilename());
        }

        $this->macros[$name] = $node;
    }

    public function addTrait($trait)
    {
        $this->traits[] = $trait;
    }

    public function hasTraits()
    {
        return count($this->traits) > 0;
    }

    public function embedTemplate(IfwTwig_Node_Module $template)
    {
        $template->setIndex(mt_rand());

        $this->embeddedTemplates[] = $template;
    }

    public function addImportedSymbol($type, $alias, $name = null, IfwTwig_Node_Expression $node = null)
    {
        $this->importedSymbols[0][$type][$alias] = array('name' => $name, 'node' => $node);
    }

    public function getImportedSymbol($type, $alias)
    {
        foreach ($this->importedSymbols as $functions) {
            if (isset($functions[$type][$alias])) {
                return $functions[$type][$alias];
            }
        }
    }

    public function isMainScope()
    {
        return 1 === count($this->importedSymbols);
    }

    public function pushLocalScope()
    {
        array_unshift($this->importedSymbols, array());
    }

    public function popLocalScope()
    {
        array_shift($this->importedSymbols);
    }

    /**
     * Gets the expression parser.
     *
     * @return IfwTwig_ExpressionParser The expression parser
     */
    public function getExpressionParser()
    {
        return $this->expressionParser;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Gets the token stream.
     *
     * @return IfwTwig_TokenStream The token stream
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Gets the current token.
     *
     * @return IfwTwig_Token The current token
     */
    public function getCurrentToken()
    {
        return $this->stream->getCurrent();
    }

    protected function filterBodyNodes(IfwTwig_NodeInterface $node)
    {
        // check that the body does not contain non-empty output nodes
        if (
            ($node instanceof IfwTwig_Node_Text && !ctype_space($node->getAttribute('data')))
            ||
            (!$node instanceof IfwTwig_Node_Text && !$node instanceof IfwTwig_Node_BlockReference && $node instanceof IfwTwig_NodeOutputInterface)
        ) {
            if (false !== strpos((string) $node, chr(0xEF).chr(0xBB).chr(0xBF))) {
                throw new IfwTwig_Error_Syntax('A template that extends another one cannot have a body but a byte order mark (BOM) has been detected; it must be removed.', $node->getLine(), $this->getFilename());
            }

            throw new IfwTwig_Error_Syntax('A template that extends another one cannot have a body.', $node->getLine(), $this->getFilename());
        }

        // bypass "set" nodes as they "capture" the output
        if ($node instanceof IfwTwig_Node_Set) {
            return $node;
        }

        if ($node instanceof IfwTwig_NodeOutputInterface) {
            return;
        }

        foreach ($node as $k => $n) {
            if (null !== $n && null === $n = $this->filterBodyNodes($n)) {
                $node->removeNode($k);
            }
        }

        return $node;
    }
}
