<?php

/**
 * LICENSE
 *
 * This source file is subject to the new BSD license
 * It is  available through the world-wide-web at this URL:
 * http://www.petala-azul.com/bsd.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to geral@petala-azul.com so we can send you a copy immediately.
 *
 * @package   Bvb_Grid
 * @author    Bento Vilas Boas <geral@petala-azul.com>
 * @copyright 2010 ZFDatagrid
 * @license   http://www.petala-azul.com/bsd.txt   New BSD License
 * @version   $Id: Date.php,v 1.1 2011-05-06 14:42:09 bcom-dev Exp $
 * @link      http://zfdatagrid.com
 */

class Bvb_Grid_Formatter_Date2 implements Bvb_Grid_Formatter_FormatterInterface
{

    /**
     * Locale to be applied
     * @var mixed
     */
    protected $locale = null;

    /**
     * Date formay
     * @var mixed
     */
    protected $date_format = null;

    /**
     * Format Type to be aplied
     * @var mixed
     */
    protected $type = null;


    /**
     * Constructor
     * @param array $options
     */
    public function __construct ($options = array())
    {
        if ( $options instanceof Zend_Locale ) {
            $this->locale = $options;
        } elseif ( is_string($options) ) {
            $this->date_format = $options;
        } elseif ( is_array($options) ) {
            foreach ( $options as $k => $v ) {
                switch ($k) {
                    case 'locale':
                        $this->locale = $v;
                        break;
                    case 'date_format':
                        $this->date_format = $v;
                        break;
                    case 'type':
                    case 'format_type':
                        $this->type = $v;
                        break;
                    default:
                        throw new Bvb_Grid_Exception(Bvb_Grid_Translator::getInstance()->__("Unknown option '$k'."));
                }
            }
        } elseif ( Zend_Registry::isRegistered('Zend_Locale') ) {
            $this->locale = Zend_Registry::get('Zend_Locale');
        }
    }


    /**
     * Formats a given value
     * @see library/Bvb/Grid/Formatter/Bvb_Grid_Formatter_FormatterInterface::format()
     */
    public function format ($value)
    {
        try {
            $year= substr($value, 0 , 4);
            $month= substr($value, 5 , 2);
            $day= substr($value, 8 , 2);
        }
        catch (Exception $e) {
            return $value;
        }

        return $day.'-'.$month.'-'.$year;
    }
}