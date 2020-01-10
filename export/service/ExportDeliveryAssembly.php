<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2019  (original work) Open Assessment Technologies SA;
 *
 * @author Yuri Filippovich
 */

namespace oat\taoSyncServer\export\service;

use Exception;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoDeliveryRdf\model\export\AssemblyExporterService;
use oat\taoEncryption\Service\DeliveryAssembly\EncryptedAssemblyFilesReaderDecorator;
use oat\taoEncryption\Service\EncryptionServiceFactory;
use oat\taoEncryption\Service\EncryptionServiceInterface;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSync\model\Exception\SyncPackageException;
use oat\taoSync\package\SyncPackageService;

class ExportDeliveryAssembly extends ConfigurableService
{
    use OntologyAwareTrait;

    const SERVICE_ID = 'taoSyncServer/ExportDeliveryAssembly';

    const OPTION_ENCRYPTION_ALGORITHM = 'taoSync/encryptionAlgorithm';
    const OPTION_OUTPUT_TEST_FORMAT = 'taoSync/outputTestFormat';

    /**
     * @param array $options
     * @throws SyncPackageException
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->hasOption(self::OPTION_ENCRYPTION_ALGORITHM)) {
            throw new SyncPackageException('encryptionAlgorithm not set for ExportDeliveryAssembly service');
        }

        if (!$this->hasOption(self::OPTION_OUTPUT_TEST_FORMAT)) {
            throw new SyncPackageException('outputTestFormat not set for ExportDeliveryAssembly service');
        }
    }

    /**
     * @param array $deliveryUris
     * @param string $orgId
     * @throws SyncBaseException
     */
    public function createCompiledDeliveryPackage(array $deliveryUris, $orgId)
    {
        try {
            $assembler = $this->getAssembler();

            foreach ($deliveryUris as $deliveryUri) {
                $exportedAssemblyPath = $assembler->exportCompiledDelivery(
                    $this->getResource($deliveryUri),
                    $this->getOption(self::OPTION_OUTPUT_TEST_FORMAT)
                );

                if (!$this->getPackageService()->moveLocalFile($exportedAssemblyPath, $orgId)) {
                    throw new Exception(sprintf('CompiledDeliveryPackage for %s not created', $deliveryUri));
                }
            }
        } catch (Exception $e) {
            throw new SyncBaseException($e->getMessage());
        }
    }

    /**
     * @return AssemblyExporterService
     * @throws Exception
     */
    private function getAssembler()
    {
        return $this->getAssemblyExporter(
            (new EncryptionServiceFactory())->createSymmetricService(
                $this->getOption(self::OPTION_ENCRYPTION_ALGORITHM),
                $this->getServiceManager()->get(FileKeyProviderService::SERVICE_ID)->getKeyFromFileSystem()
            )
        );
    }

    /**
     * @param EncryptionServiceInterface $encryptionService
     * @return AssemblyExporterService
     */
    private function getAssemblyExporter(EncryptionServiceInterface $encryptionService)
    {
        $assemblerService = $this->getServiceLocator()->get(AssemblyExporterService::SERVICE_ID);
        $filesReader = $assemblerService->getOption(AssemblyExporterService::OPTION_ASSEMBLY_FILES_READER);

        $encryptedFilesReader = new EncryptedAssemblyFilesReaderDecorator($filesReader, $encryptionService);
        $assemblerService->setOption(AssemblyExporterService::OPTION_ASSEMBLY_FILES_READER, $encryptedFilesReader);

        return $assemblerService;
    }

    /**
     * @return SyncPackageService
     */
    private function getPackageService()
    {
        return $this->getServiceLocator()->get(SyncPackageService::SERVICE_ID);
    }
}
