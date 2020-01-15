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
use oat\taoEncryption\Service\EncryptionServiceInterface;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSync\package\SyncPackageService;

class ExportDeliveryAssembly extends ConfigurableService
{
    use OntologyAwareTrait;

    const ENCRYPTION_ALGORITHM = 'AES';
    const OUTPUT_TEST_FORMAT = CompiledTestConverterFactory::COMPILED_TEST_FORMAT_XML;
    const ASSEMBLY_STORAGE_NAME = 'assembly';

    /**
     * @param array $deliveryUris
     * @throws SyncBaseException
     */
    public function createCompiledDeliveryPackage(array $deliveryUris)
    {
        try {
            $assembler = $this->getAssembler();
            $directory = $this->getPackageService()->getSyncDirectory()->getDirectory(self::ASSEMBLY_STORAGE_NAME);

            foreach ($deliveryUris as $deliveryUri) {
                $assemblerFile = $directory->getFile($this->getAssemblerFileName($deliveryUri));

                if($assemblerFile->exists()) {
                    continue;
                }

                $exportedAssemblyPath = $assembler->exportCompiledDelivery(
                    $this->getResource($deliveryUri),
                    self::OUTPUT_TEST_FORMAT
                );

                if (!$assemblerFile->write(file_get_contents($exportedAssemblyPath))) {
                    throw new Exception(sprintf('CompiledDeliveryPackage for %s not created', $deliveryUri));
                }
                unlink($exportedAssemblyPath);
            }
        } catch (Exception $e) {
            throw new SyncBaseException($e->getMessage());
        }
    }

    /**
     * @param string $deliveryUri
     * @return string
     */
    private function getAssemblerFileName($deliveryUri)
    {
        return str_replace('/', '_', $deliveryUri) . '.zip';
    }

    /**
     * @return AssemblyExporterService
     * @throws Exception
     */
    private function getAssembler()
    {
        return $this->getAssemblyExporter(
            (new EncryptionServiceFactory())->createSymmetricService(
                self::ENCRYPTION_ALGORITHM,
                $this->getServiceLocator()->get(FileKeyProviderService::SERVICE_ID)->getKeyFromFileSystem()
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
