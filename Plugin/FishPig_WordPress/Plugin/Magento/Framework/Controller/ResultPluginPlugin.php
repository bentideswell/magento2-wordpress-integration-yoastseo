<?php
/**
 *
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\Plugin\Magento\Framework\Controller;

use FishPig\WordPress\Plugin\Magento\Framework\Controller\ResultPlugin as Subject;
use Magento\Framework\View\Layout;

class ResultPluginPlugin
{
    /**
     *
     */
    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     *
     */
    public function aroundTransformHtml(Subject $resultPlugin, $callback, $html)
    {
        $find = '%%pagetotal%%';

        if (strpos($html, $find) === false) {
            return $callback($html);
        }

        if (!($pagerBlock = $this->layout->getBlock('wp.post_list.pager'))) {
            return $callback($html);
        }

        $lastPageNumber = 1;

        if ($listBlock = $pagerBlock->getParentBlock()->getParentBlock()) {
            if ($pagerBlock->getCollection()) {
                $lastPageNumber = (int)$pagerBlock->getLastPageNum();
            }
        }

        $html = str_replace($find, $lastPageNumber, $html);

        return $callback($html);
    }
}
