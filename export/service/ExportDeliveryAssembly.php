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
 * Copyright (c) 2020  (original work) Open Assessment Technologies SA;
 *
 * @author Yuri Filippovich
 */

namespace oat\taoSyncServer\export\service;

use Exception;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\service\ConfigurableService;
use oat\taoDeliveryRdf\model\assembly\CompiledTestConverterFactory;
use oat\taoDeliveryRdf\model\export\AssemblyExporterService;
use oat\taoEncryption\Service\DeliveryAssembly\EncryptedAssemblyFilesReaderDecorator;
use oat\taoEncryption\Service\EncryptionServiceFactory;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoSync\model\Exception\SyncBaseException;
use tao_helpers_File;

class ExportDeliveryAssembly extends ConfigurableService
{
    use OntologyAwareTrait;

    const ENCRYPTION_ALGORITHM = 'AES';

    /**
     * @param array $deliveryUris
     * @throws SyncBaseException
     */
    public function exportDeliveryAssemblies(array $deliveryUris)
    {
        try {
            $assembler = $this->getAssembler();

            foreach ($deliveryUris as $deliveryUri) {
                $assemblerFile = $this->getDeliveryAssemblyStorageService()->getDeliveryAssemblyFile($deliveryUri);

                if($assemblerFile->exists()) {
                    continue;
                }

                $exportedAssemblyPath = $assembler->exportCompiledDelivery(
                    $this->getResource($deliveryUri),
                    CompiledTestConverterFactory::COMPILED_TEST_FORMAT_XML
                );

                if (!$assemblerFile->write(fopen($exportedAssemblyPath, 'r'))) {
                    throw new Exception(sprintf('CompiledDeliveryPackage for %s not created', $deliveryUri));
                }
                tao_helpers_File::remove($exportedAssemblyPath);
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
        $assemblerService = $this->getServiceLocator()->get(AssemblyExporterService::SERVICE_ID);
        $filesReader = $assemblerService->getOption(AssemblyExporterService::OPTION_ASSEMBLY_FILES_READER);

        $encryptionService = (new EncryptionServiceFactory())->createSymmetricService(
            self::ENCRYPTION_ALGORITHM,
            $this->getServiceLocator()->get(FileKeyProviderService::SERVICE_ID)->getKeyFromFileSystem()
        );

        $encryptedFilesReader = new EncryptedAssemblyFilesReaderDecorator($filesReader, $encryptionService);
        $assemblerService->setOption(AssemblyExporterService::OPTION_ASSEMBLY_FILES_READER, $encryptedFilesReader);

        return $assemblerService;
    }

    /**
     * @return DeliveryAssemblyStorage
     */
    private function getDeliveryAssemblyStorageService()
    {
        return $this->getServiceLocator()->get(DeliveryAssemblyStorage::class);
    }
}
