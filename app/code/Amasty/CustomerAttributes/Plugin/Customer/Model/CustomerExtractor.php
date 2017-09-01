<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerAttributes
 */

namespace Amasty\CustomerAttributes\Plugin\Customer\Model;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class CustomerExtractor
{
    protected $objectManager;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;
    /**
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    )
    {
        $this->objectManager = $objectManager;
        $this->filesystem = $filesystem;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->ioFile = $ioFile;
    }

    /**
     * set magento data model for checkxoxes and radios
     * @param $subject
     * @param $result
     * @return mixed
     */
    public function beforeExtract($subject, $formCode, RequestInterface $request){
        $files = $request->getFiles();
        if ($files) {
            foreach ($files as $name => $file) {
                if (UPLOAD_ERR_OK == $file['error']) {
                    $this->uploadImage($name);
                }
            }
        }
        return [$formCode, $request];
    }

    protected function uploadImage($name)
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'amasty/amcustomerattr/files'
        );

        $this->ioFile->checkAndCreateFolder($path);

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->fileUploaderFactory->create(
                ['fileId' => $name]
            );
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($path);
        } catch (\Exception $e) {
            if ($e->getCode() != \Magento\MediaStorage\Model\File\Uploader::TMP_NAME_EMPTY) {
                $this->_logger->critical($e);
            }
        }
        return $result;
    }
}
