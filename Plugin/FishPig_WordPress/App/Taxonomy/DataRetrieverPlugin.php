<?php
/**
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/yoast/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress\App\Taxonomy;

use FishPig\WordPress\App\Taxonomy\DataRetriever;

class DataRetrieverPlugin
{
    /**
     * @auto
     */
    protected $config = null;

    /**
     * @param \FishPig\WordPress_Yoast\Model\Config $config
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config
    ) {
        $this->config = $config;
    }
    
    /**
     * @param  \Magento\Framework\View\Result\Page $resultPage,
     * @param  \FishPig\WordPress\Api\Data\ViewableModelInterface $object
     * @return void
     */
    public function afterGetData(DataRetriever $subject, array $data): array
    {
        if (!$this->config->isEnabled() || !$this->config->canStripCategoryUrlBase()) {
            return $data;
        }

        $data['category']['rewrite']['slug'] = '';

        return $data;
    }
}
