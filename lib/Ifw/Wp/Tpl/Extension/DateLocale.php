<?php
/**
 * ifeelweb.de WordPress Plugin Framework
 * For more information see http://www.ifeelweb.de/wp-plugin-framework
 *
 * Twig extension for localized date filter
 * Uses strftime (http://www.php.net/manual/de/function.strftime.php) format syntax
 *
 * @author   Timo Reith <timo@ifeelweb.de>
 * @version  $Id$
 */
class Ifw_Wp_Tpl_Extension_DateLocale extends IfwTwig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'date_locale';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'date_locale' => new IfwTwig_Filter_Method($this, 'dateLocale'),
        );
    }

    /**
     * @param string $date
     * @param string $format
     * @param null $locale
     * @return string
     */
    public function dateLocale($date, $format, $locale=null)
    {
        if ($locale === null && defined('WPLANG')) {
            $locale = WPLANG;
        }

        if (!empty($locale)) {
            setlocale(LC_TIME, $locale);
        }

        return strftime($format, strtotime($date));
    }
}
