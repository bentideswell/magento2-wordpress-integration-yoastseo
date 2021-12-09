<?php
/**
 * @package FishPig_WordPress
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\App\View;

use FishPig\WordPress_Yoast\Model\StringRewriter;

class AssetProvider implements \FishPig\WordPress\Api\App\View\AssetProviderInterface
{
    /**
     * @const string
     */
    const PAGE_PLACEHOLDER = 'page';
    const PAGE_TOTAL_PLACEHOLDER = 'pagetotal';
    const PAGE_NUMBER_PLACEHOLDER = 'pagenumber';
  
    /**
     *
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \Magento\Framework\View\Layout $layout
    ) {
        $this->config = $config;
        $this->layout = $layout;
    }

    /**
     * @param  \Magento\Framework\App\RequestInterface $request,
     * @param  \Magento\Framework\App\ResponseInterface $response
     * @return void
     */
    public function provideAssets(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response
    ): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }
        
        $bodyHtml = $response->getBody();
        
        foreach ([
            self::PAGE_NUMBER_PLACEHOLDER,
            self::PAGE_TOTAL_PLACEHOLDER,
            self::PAGE_PLACEHOLDER] as $key) {
            if (strpos($bodyHtml, StringRewriter::RWTS . $key . StringRewriter::RWTS) !== false) {
                foreach ($this->getPageData() as $var => $value) {
                    $bodyHtml = str_replace(
                        StringRewriter::RWTS . $var . StringRewriter::RWTS,
                        $value,
                        $bodyHtml
                    );
                }

                $response->setBody($bodyHtml);
                break;
            }
        }
    }

    /**
     * @return array
     */
    private function getPageData(): array
    {   
        $data = [
            self::PAGE_NUMBER_PLACEHOLDER => '',
            self::PAGE_TOTAL_PLACEHOLDER => '',
            self::PAGE_PLACEHOLDER => ''
        ];

        if (!($pagerBlock = $this->layout->getBlock('wp.post_list.pager'))) {
            return $data;
        }

        if ($listBlock = $pagerBlock->getParentBlock()->getParentBlock()) {
            if ($pagerBlock->getCollection()) {
                $data[self::PAGE_TOTAL_PLACEHOLDER] = (int)$pagerBlock->getLastPageNum();
                $data[self::PAGE_NUMBER_PLACEHOLDER] = (int)$pagerBlock->getCurrentPage();
                $data[self::PAGE_PLACEHOLDER] = sprintf('Page %d of %s', $data[self::PAGE_NUMBER_PLACEHOLDER], $data[self::PAGE_TOTAL_PLACEHOLDER]);
            }
        }

        return $data;
    }
}
