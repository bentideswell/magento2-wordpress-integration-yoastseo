<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Controller;

use FishPig\WordPress\Controller\Action;

class ActionPlugin
{
    /**
     * @param \FishPig\WordPress_Yoast\Model\Config $config
     * @param \Magento\Framework\View\Layout        $layout
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->config = $config;
        $this->layout = $layout;
    }
    
    /**
     * @param  Action $subject
     * @param  $result
     * @return mixed
     */
    public function afterExecute(Action $subject, $result)
    {
        if (($result instanceof \Magento\Framework\View\Result\Page) === false) {
            return $result;
        }

        if (!$this->config->canShowBreacrumbs()) {
            $this->layout->unsetElement('breadcrumbs');
        }

        return $result;        
    }
}
