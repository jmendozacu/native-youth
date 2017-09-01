<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */
namespace Amasty\CustomerAttributes\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class Validation
{
    /**
     * @var \Magento\Framework\App\ObjectManager $objectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Filesystem $filesystem
     */
    protected $rootDirectory;
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $dirReader;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Module\Dir\Reader $dirReader
    )
    {
        $this->objectManager = $objectManager;
        $this->rootDirectory = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->dirReader = $dirReader;
    }

    /**
     * Retrieve additional validation types
     *
     * @return array
     */
    public function getAdditionalValidation()
    {
        $addon = [];
        $files = $this->_getValidationFiles();
        foreach ($files as $file) {
            if (false !== strpos($file, '.php')) {
                $addon[] = $this->objectManager->create(
                    'Amasty\CustomerAttributes\Model\Validation\\' . str_replace(
                        '.php', '', $file
                    )
                )->getValues();
            }
        }
        return $addon;
    }

    protected function _getValidationFiles()
    {
        $path = $this->dirReader->getModuleDir('', 'Amasty_CustomerAttributes') . DIRECTORY_SEPARATOR . 'Model'
            . DIRECTORY_SEPARATOR . 'Validation';
        $files = scandir($path);
        return $files;
    }

    /**
     * Retrieve JS code
     *
     * @return string
     */
    public function getJS()
    {
        $js = '';
        $files = $this->_getValidationFiles();
        foreach ($files as $file) {
            if (false !== strpos($file, '.php')) {
                $js .= $this->objectManager->create(
                    'Amasty\CustomerAttributes\Model\Validation\\' . str_replace(
                        '.php', '', $file
                    )
                )->getJS();
            }
        }
        return $js;
    }
}
