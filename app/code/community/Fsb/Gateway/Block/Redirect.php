<?php
/*
 *
 * @category   Community
 * @package    MagentoCenter_Wm
 * @copyright  http://Magentocenter.org
 * @license    Open Software License (OSL 3.0)
 *
 */

/*
 * Webmoney Transfer payment module
 *
 * @author     Magentocenter.org    -   Magento Store Setup, data migration, upgrades and much more!
 *
 */

class Fsb_Gateway_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $wm = Mage::getModel('gateway/checkout');

        $form = new Varien_Data_Form();
        $form->setAction($wm->getWebmoneyUrl())
            ->setId('pay')
            ->setName('pay')
            ->setMethod('POST')
            ->setUseContainer(true);
        $wm->getWebmoneyCheckoutFormFields();
        foreach ($wm->getWebmoneyCheckoutFormFields() as $field=>$value) {
            //echo $field.' - '.$value.'<br>';

           $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('You will be redirected to Brac Bank in a few seconds.');
        $html.= '<br>';
        $html.= $form->toHtml();
        $html.= '<br>';
        $html.= '<script type="text/javascript">document.getElementById("pay").submit();</script>';
        $html.= '</body></html>';
        

        return $html;
    }
}
