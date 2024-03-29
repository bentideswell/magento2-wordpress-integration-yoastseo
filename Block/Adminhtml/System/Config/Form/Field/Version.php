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
     * @auto
     */
    protected $moduleDirReader = null;

    /**
     * @auto
     */
    protected $filesystem = null;

    /**
     * @var string
     */
    private $moduleVersion;

    /**
     *
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\Dir\Reader $moduleDirReader,
        \Magento\Framework\Filesystem $filesystem,
        array $data = []
    ) {
        $this->moduleDirReader = $moduleDirReader;
        $this->filesystem = $filesystem;
        parent::__construct($context, $data);
    }
    
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
            '<span style="%s"><strong style="color:green;">ENABLED</strong> - Version %s</span>',
            'display:inline-block;border:1px solid #ccc;background:#f6f6f6;line-height:1em;padding:10px;'
            . 'font-size:13px;color:#04260d;width:80%;margin-bottom:2px;',
            $this->getModuleVersion()
        );
    }
    
    /**
     * @return string
     */
    private function getModuleVersion()
    {
        if ($this->moduleVersion === null) {
            $this->moduleVersion = 'Error';

            $moduleDirectory = $this->filesystem->getDirectoryReadByPath(
                $this->moduleDirReader->getModuleDir('', 'FishPig_WordPress_Yoast')
            );

            if ($moduleDirectory->isFile('composer.json')) {
                $moduleComposerData = json_decode(
                    $moduleDirectory->readFile($moduleDirectory->getAbsolutePath('composer.json')),
                    true
                );

                $this->moduleVersion = (string)$moduleComposerData['version'];
            }
        }
        
        return $this->moduleVersion;
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
}
