<?php
/**
 * @package WordPress_PostTypeTaxonomy
 * @author  Ben Tideswell (ben@fishpig.com)
 * @url     https://fishpig.co.uk/magento/wordpress-integration/
 */
declare(strict_types=1);

namespace FishPig\WordPress_Yoast\Controller\Router;

class RedirectRouter implements \Magento\Framework\App\RouterInterface
{
    /**
     * @auto
     */
    protected $requestDispatcher = null;

    /**
     * @auto
     */
    protected $routerUrlHelper = null;

    /**
     * @auto
     */
    protected $optionRepository = null;

    /**
     * @auto
     */
    protected $url = null;

    /**
     *
     */
    public function __construct(
        \FishPig\WordPress\Controller\Router\RequestDispatcher $requestDispatcher,
        \FishPig\WordPress\Controller\Router\UrlHelper $routerUrlHelper,
        \FishPig\WordPress\Model\OptionRepository $optionRepository,
        \FishPig\WordPress\Model\UrlInterface $url
    ) {
        $this->requestDispatcher = $requestDispatcher;
        $this->routerUrlHelper = $routerUrlHelper;
        $this->optionRepository = $optionRepository;
        $this->url = $url;
    }

    /**
     * @param  \Magento\Framework\App\RequestInterface $request
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if ($request->getMethod() !== 'GET') {
            return false;
        }

        $pathInfo = $this->routerUrlHelper->getRelativePathInfo($request);
        foreach ($this->getRedirects() as $redirect) {
            if (isset($redirect['origin']) && $redirect['origin'] === $pathInfo) {
                return $this->requestDispatcher->redirect(
                    $request,
                    $this->url->getUrl($redirect['url']),
                    isset($redirect['type']) && (int)$redirect['type'] > 0 ? (int)$redirect['type'] :  301
                );
            }
        }

        return false;
    }

    /**
     *
     */
    private function getRedirects(): array
    {
        return $this->optionRepository->getUnserialized(
            'wpseo-premium-redirects-base'
        );
    }
}
