<?php
/*
 * @package FishPig_WordPress_Yoast
 * @author  Ben Tideswell (ben@fishpig.co.uk)
 */
namespace FishPig\WordPress_Yoast\Model;

use FishPig\WordPress\Model\OptionManager;
use FishPig\WordPress_Yoast\Helper\Data as YoastHelper;
use FishPig\WordPress\Helper\Date as DateHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Layout;
use FishPig\WordPress\Model\PostFactory;
use FishPig\WordPress\Model\TermFactory;
use FishPig\WordPress\Model\SearchFactory;

class Context
{	
	/**
	 * @var
	 */
	protected $optionManager;

	/**
	 * @var
	 */
	protected $yoastHelper;
	
	/**
	 * @var
	 */
	protected $dateHelper;
	
	/**
	 * @var
	 */
	protected $registry;
	
	/**
	 * @var
	 */
	protected $layout;
	
	/**
   * @var PostFactory
   */
  protected $postFactory;
	
	/**
   * @var TermFactory
   */
  protected $termFactory;
  
	/**
   * @var SearchFactory
   */
  protected $searchFactory;
	
	/*
	 *
	 *
	 */
	public function __construct(
    OptionManager $optionManager,
    YoastHelper $yoastHelper,
    DateHelper $dateHelper,
    Registry $registry,
    Layout $layout,
    PostFactory $postFactory,
    TermFactory $termFactory,
    SearchFactory $searchFactory
  )
	{
		$this->optionManager = $optionManager;
		$this->yoastHelper   = $yoastHelper;
		$this->dateHelper    = $dateHelper;
		$this->registry      = $registry;
		$this->layout        = $layout;
		$this->termFactory   = $termFactory;
		$this->searchFactory = $searchFactory;
		$this->postFactory   = $postFactory;
	}

  /**
   * @return
   */
  public function getOptionManager()
  {
    return $this->optionManager;
  }
  
  /**
   * @return
   */
  public function getYoastHelper()
  {
    return $this->yoastHelper;
  }
  
  /**
   * @return
   */
  public function getDateHelper()
  {
    return $this->dateHelper;
  }
  
  /**
   * @return
   */
  public function getRegistry()
  {
    return $this->registry;
  }
  
  /**
   * @return
   */
  public function getLayout()
  {
    return $this->layout;
  }
  
  /**
   * @return
   */
  public function getSearchFactory()
  {
    return $this->searchFactory;
  }
  
  /**
   * @return
   */
  public function getPostFactory()
  {
    return $this->postFactory;
  }
  
  /**
   * @return
   */
  public function getTermFactory()
  {
    return $this->termFactory;
  }
}
