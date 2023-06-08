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
     * @auto
     */
    protected $config = null;

    /**
     * @auto
     */
    protected $layout = null;

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
    ): void {
        $bodyHtml = $response->getBody();

        foreach ($this->getPlaceholders() as $key) {
            $placeholder = $this->buildPlaceholder($key);
            if (strpos($bodyHtml, $placeholder) !== false) {
                foreach ($this->getPageData() as $var => $value) {
                    $bodyHtml = str_replace(
                        $placeholder,
                        (string)$value,
                        $bodyHtml
                    );
                }

                $response->setBody($bodyHtml);
                break;
            }
        }
    }

    /**
     * @param  \Magento\Framework\App\RequestInterface $request,
     * @param  \Magento\Framework\App\ResponseInterface $response
     * @return bool
     */
    public function canProvideAssets(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response
    ): bool {
        if (!$this->config->isEnabled()) {
            return false;
        }

        $bodyHtml = $response->getBody();

        foreach ($this->getPlaceholders() as $key) {
            if (strpos($bodyHtml, $this->buildPlaceholder($key)) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    private function getPageData(): array
    {
        $data = array_combine(
            $this->getPlaceholders(),
            array_fill(0, count($this->getPlaceholders()), '')
        );

        if (!($pagerBlock = $this->layout->getBlock('wp.post_list.pager'))) {
            return $data;
        }

        if ($listBlock = $pagerBlock->getParentBlock()->getParentBlock()) {
            if ($pagerBlock->getCollection()) {
                $data[self::PAGE_TOTAL_PLACEHOLDER] = (int)$pagerBlock->getLastPageNum();
                $data[self::PAGE_NUMBER_PLACEHOLDER] = (int)$pagerBlock->getCurrentPage();
                $data[self::PAGE_PLACEHOLDER] = sprintf(
                    'Page %d of %s',
                    $data[self::PAGE_NUMBER_PLACEHOLDER],
                    $data[self::PAGE_TOTAL_PLACEHOLDER]
                );
            }
        }

        return $data;
    }

    /**
     *
     */
    private function getPlaceholders(): array
    {
        return [
            self::PAGE_NUMBER_PLACEHOLDER,
            self::PAGE_TOTAL_PLACEHOLDER,
            self::PAGE_PLACEHOLDER
        ];
    }

    /**
     *
     */
    private function buildPlaceholder(string $key): string
    {
        return StringRewriter::RWTS . $key . StringRewriter::RWTS;
    }
}
