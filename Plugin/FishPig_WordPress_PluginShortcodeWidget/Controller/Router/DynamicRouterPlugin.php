<?php
/**
 *
 */
namespace FishPig\WordPress_Yoast\Plugin\FishPig_WordPress_PluginShortcodeWidget\Controller\Router;

use FishPig\WordPress_PluginShortcodeWidget\Controller\Router\DynamicRouter;

class DynamicRouterPlugin
{
    /**
     *
     */
    private $config = null;

    /**
     *
     */
    private $routerUrlHelper = null;

    /**
     *
     */
    public function __construct(
        \FishPig\WordPress_Yoast\Model\Config $config,
        \FishPig\WordPress\Controller\Router\UrlHelper $routerUrlHelper
    ) {
        $this->config = $config;
        $this->routerUrlHelper = $routerUrlHelper;
    }

    /**
     *
     */
    public function aroundCanMatchRequest(
        DynamicRouter $subject,
        \Closure $proceed,
        \Magento\Framework\App\RequestInterface $request
    ): bool {
        if (($result = $proceed($request)) === false) {
            return false;
        }

        if (!$this->config->isEnabled() || !$this->config->canStripCategoryUrlBase()) {
            return $result;
        }

        $relativePathInfo = $this->routerUrlHelper->getRelativePathInfo($request);

        if (!$relativePathInfo) {
            return $result;
        }

        if (strpos($relativePathInfo, 'category/') === 0) {
            // Relative path info starts with /category/ but Yoast config forbids this
            // so do not allow this to be requested via PSW's dynamic routing system
            return false;
        }

        return $result;
    }
}
