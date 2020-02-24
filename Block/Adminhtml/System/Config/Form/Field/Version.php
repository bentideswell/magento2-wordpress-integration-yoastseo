<?php
/**
 * @DoNotInject
 */
namespace FishPig\WordPress_Yoast\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param  AbstractElement $element
     * @return string
     */
    public function getElementHtml(AbstractElement $element)
    {
       return $this->_getElementHtml($element);
    }

    /**
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return sprintf(
            '<span style="%s">Version %s</span>',
            'display:inline-block;border:1px solid #ccc;background:#f6f6f6;line-height:1em;padding:10px;font-size:13px;color:#04260d;width:80%;margin-bottom:2px;',
            $this->getModuleVersion()
        );
    }

    /**
     * @param  AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }
    
    /**
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        return str_replace('class="label"', 'style="vertical-align: middle;" class="label"', parent::render($element));
    }

    /**
     * @return string
     */
    public function getModuleVersion()
    {
        $moduleList = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Module\ModuleList');
        
        if ($moduleInfo = $moduleList->getOne('FishPig_WordPress_Yoast')) {
            if (isset($moduleInfo['setup_version'])) {
                return $moduleInfo['setup_version'];
            }
        }

        return '';
    }
}
