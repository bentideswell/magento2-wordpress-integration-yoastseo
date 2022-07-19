<?php
namespace FishPig\WordPress_Yoast\Controller\Post\View;

/**
 * Factory class for @see \FishPig\WordPress_Yoast\Controller\Post\View\SeoMetaDataProvider
 */
class SeoMetaDataProviderFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\FishPig\\WordPress_Yoast\\Controller\\Post\\View\\SeoMetaDataProvider')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \FishPig\WordPress_Yoast\Controller\Post\View\SeoMetaDataProvider
     */
    public function create(array $data = [])
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
