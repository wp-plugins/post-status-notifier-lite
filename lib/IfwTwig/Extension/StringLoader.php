<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class IfwTwig_Extension_StringLoader extends IfwTwig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new IfwTwig_SimpleFunction('template_from_string', 'ifw_twig_template_from_string', array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'string_loader';
    }
}

/**
 * Loads a template from a string.
 *
 * <pre>
 * {% include template_from_string("Hello {{ name }}") }}
 * </pre>
 *
 * @param IfwTwig_Environment $env      A IfwTwig_Environment instance
 * @param string           $template A template as a string
 *
 * @return IfwTwig_Template A IfwTwig_Template instance
 */
function ifw_twig_template_from_string(IfwTwig_Environment $env, $template)
{
    static $loader;

    if (null === $loader) {
        $loader = new IfwTwig_Loader_String();
    }

    $current = $env->getLoader();
    $env->setLoader($loader);
    try {
        $template = $env->loadTemplate($template);
    } catch (Exception $e) {
        $env->setLoader($current);

        throw $e;
    }
    $env->setLoader($current);

    return $template;
}
