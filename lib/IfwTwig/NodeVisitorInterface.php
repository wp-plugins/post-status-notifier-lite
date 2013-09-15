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
 * IfwTwig_NodeVisitorInterface is the interface the all node visitor classes must implement.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface IfwTwig_NodeVisitorInterface
{
    /**
     * Called before child nodes are visited.
     *
     * @param IfwTwig_NodeInterface $node The node to visit
     * @param IfwTwig_Environment   $env  The Twig environment instance
     *
     * @return IfwTwig_NodeInterface The modified node
     */
    public function enterNode(IfwTwig_NodeInterface $node, IfwTwig_Environment $env);

    /**
     * Called after child nodes are visited.
     *
     * @param IfwTwig_NodeInterface $node The node to visit
     * @param IfwTwig_Environment   $env  The Twig environment instance
     *
     * @return IfwTwig_NodeInterface|false The modified node or false if the node must be removed
     */
    public function leaveNode(IfwTwig_NodeInterface $node, IfwTwig_Environment $env);

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return integer The priority level
     */
    public function getPriority();
}
