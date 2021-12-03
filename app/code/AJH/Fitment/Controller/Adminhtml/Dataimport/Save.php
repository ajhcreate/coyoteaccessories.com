<?php

namespace AJH\Fitment\Controller\Adminhtml\Dataimport;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\ScopeInterface;

use AJH\Fitment\Model\Fitment\VehiclePartsFactory;

class Save extends \Magento\Backend\App\Action {

    protected $fileSystem;
    protected $uploaderFactory;
    protected $request;
    protected $adapterFactory;
    protected $vehicleParts;

    public function __construct(
    \Magento\Backend\App\Action\Context $context,
            \Magento\Framework\Filesystem $fileSystem,
            \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
            \Magento\Framework\App\RequestInterface $request,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
            AdapterFactory $adapterFactory, VehiclePartsFactory $vehiclePartsFactory
    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        
        $this->vehicleParts = $vehiclePartsFactory;
    }

    public function execute() {

        if ((isset($_FILES['importdata']['name'])) && ($_FILES['importdata']['name'] != '')) {
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'importdata']);
                $uploaderFactory->setAllowedExtensions(['csv', 'xls']);
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);

                $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('ajh_fitment_import');

                $result = $uploaderFactory->save($destinationPath);

                if (!$result) {
                    throw new LocalizedException(
                    __('File cannot be saved to path: $1', $destinationPath)
                    );
                } else {
                    $imagePath = 'ajh_fitment_import' . $result['file'];

                    $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);

                    $destinationfilePath = $mediaDirectory->getAbsolutePath($imagePath);

                    /* file read operation */
                    $f_object = fopen($destinationfilePath, "r");

                    $column = fgetcsv($f_object);                                        
                    
                    // column name must be same as the Sample file name 
                    if ($f_object) {
                        if (in_array('PartMasterID', $column) && in_array('YearID', $column) && in_array('MakeID', $column) && in_array('ModelID', $column) && in_array('SubModelID', $column)) {

                            $count = 0;
                            $data = [];

                            while (($columns = fgetcsv($f_object)) !== FALSE) {

//                                $rowData = $this->_objectManager->create('AJH\Fitment\Model\Fitment\VehicleParts');
                                $model = $this->vehicleParts->create();

//                                    if($columns[0] != 'BaseVehicleID')// unique Name like Primary key
//                                    {   
                                $count++;
                                
                                foreach ($column as $key=>$col){
                                    $data[$col] = $columns[$key];
                                }
                                
                                

                                /// here this are all the Getter Setter Method which are call to set value 
                                // the auto increment column name not used to set value 

                                $model->setData($data);
                                $model->save();
                                
//                                $model->setName($data['name']);

//                                $rowData->setCol_name_1($columns[1]);
//                                $rowData->setCol_name_2($columns[2]);
//                                $rowData->setCol_name_3($columns[3]);
//                                $rowData->setCol_name_4($columns[4]);
//                                $rowData->setCol_name_5($columns[5]);
//                                $rowData->setCol_name_6($columns[6]);
//                                $rowData->setCol_name_7($columns[7]);
//                                $rowData->save();

//                                    }
                            }

                            $this->messageManager->addSuccess(__('A total of %1 record(s) have been Added.', $count));
                            $this->_redirect('fitment/dashboard/index');
                        } else {
                            $this->messageManager->addError(__("Invalid Formated File"));
                            $this->_redirect('fitment/dataimport/importdata');
                        }
                    } else {
                        $this->messageManager->addError(__("File hase been empty"));
                        $this->_redirect('fitment/dataimport/importdata');
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('fitment/dataimport/importdata');
            }
        } else {
            $this->messageManager->addError(__("Please try again."));
            $this->_redirect('fitment/dataimport/importdata');
        }
    }

}
