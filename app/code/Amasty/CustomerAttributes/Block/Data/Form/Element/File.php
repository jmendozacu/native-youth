<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Block\Data\Form\Element;
use Magento\Framework\App\Filesystem\DirectoryList;

class File extends \Magento\Customer\Block\Adminhtml\Form\Element\File
{
    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * File constructor.
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param array $data
     * @param \Magento\Framework\Url\DecoderInterface $urlDecoder
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data,
        \Magento\Framework\ObjectManagerInterface $objectManager
) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $adminhtmlData, $assetRepo, $urlEncoder, $data);
        $this->objectManager = $objectManager;
        $this->_storeManager = $storeManager;
    }
    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue()) && strpos($this->getValue(), ".") !== false) {
            $image = [
                'alt' => __('Download'),
                'title' => __('Download'),
                'src'   => $this->_assetRepo->getUrl('Amasty_CustomerAttributes::images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            ];

            $path = $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            $url =  $path . 'amasty/amcustomerattr/files/'. $this->getEscapedValue();

            $html .= '<span>';
            $html .= '<a href="' . $url . '"  target="_blank">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '"  target="_blank">' . __('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    public function getElementHtml()
    {
        if ($this->getValue() && strpos($this->getValue(), ".") !== false) {
            return $this->_getPreviewHtml()  . '   ' . $this->_getHiddenInput() . $this->_getDeleteCheckboxHtml();
        }

        return parent::getElementHtml();
    }
}
