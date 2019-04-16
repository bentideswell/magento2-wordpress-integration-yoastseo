<?php
/*
 *
 */
namespace FishPig\WordPress_Yoast\Block\Adminhtml\System\Config\Form\Field;

/* Parent Class */
use Magento\Config\Block\System\Config\Form\Field;

/* Misc */
use Magento\Framework\Data\Form\Element\AbstractElement;

class Addon extends Field
{
	/**
	 *
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		return '<span style="display: block; border: 1px solid #ccc; background: #f6f6f6; line-height:1em; padding:10px; font-size:13px; color:#04260d;">Version ' . $this->getModuleVersion() . '</span>';
	}

	/**
	 *
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element)
	{
		return $this->_getElementHtml($element);
	}
}
