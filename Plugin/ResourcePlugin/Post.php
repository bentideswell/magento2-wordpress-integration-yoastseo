<?php

namespace FishPig\WordPress_Yoast\Plugin\ResourcePlugin;

use Magento\Framework\DB\Select;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use FishPig\WordPress\Model\Factory;
use FishPig\WordPress\Model\OptionManager;
use FishPig\WordPress\Helper\Date as DateHelper;
use FishPig\WordPress_Yoast\Plugin\AbstractPlugin;
use FishPig\WordPress\Api\Data\Plugin\SeoInterface;
use FishPig\WordPress_Yoast\Helper\Data as YoastHelper;
use FishPig\WordPress\Api\Data\Entity\ViewableInterface;
use FishPig\WordPress\Model\Post\Factory as PostFactory;
use FishPig\WordPress\Model\ResourceModel\Post as PostResource;

class Post extends AbstractPlugin
{
    protected $postfactory;

    public function __construct(
        OptionManager $optionManager,
        YoastHelper $yoastHelper,
        DateHelper $dateHelper,
        Registry $registry,
        Layout $layout,
        PostFactory $postfactory,
        Factory $factory,
        $data = []
    ) {
        $this->optionManager = $optionManager;
        $this->yoastHelper   = $yoastHelper;
        $this->dateHelper    = $dateHelper;
        $this->_registry     = $registry;     // Remove
        $this->registry      = $registry;
        $this->layout        = $layout;
        $this->factory       = $factory;
        $this->postFactory   = $postfactory;

        parent::__construct($optionManager, $yoastHelper, $dateHelper, $registry, $layout, $factory, $data);
    }


    /**
     * Add the primary category to a select object
     *
     * @param PostResource $postResource
     * @param \Closure $callback
     * @param Magento\Framework\DB\Select
     * @param array|int $postId
     * @return $this
    **/
    public function aroundAddPrimaryCategoryToSelect(PostResource $postResource, \Closure $callback, Select $select, $postId)
    {
        if (!$this->isEnabled()) {
            return $this;
        }

        if (is_object($postId)) {
            $post = $postId->getId();
        } elseif (is_array($postId)) {
            $postId = array_shift($postId);
        }

        $tempPostModel = $this->postFactory->create()->setId($postId);

        if ($categoryId = $tempPostModel->getMetaValue('_yoast_wpseo_primary_category')) {
            $select->reset(\Zend_Db_Select::ORDER)->where('_term.term_id=?', $categoryId);
        }

        return $this;
    }
}
