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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoSyncServer\test\unit\export\service;

use oat\generis\model\data\Ontology;
use oat\generis\test\TestCase;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\taoDeliveryRdf\model\assembly\AssemblyFilesReaderInterface;
use oat\taoDeliveryRdf\model\export\AssemblyExporterService;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoSync\model\Exception\SyncBaseException;
use oat\taoSync\package\SyncPackageService;
use oat\taoSyncServer\export\service\ExportDeliveryAssembly;

class ExportDeliveryAssemblyTest extends TestCase
{
    public function testCreateCompiledDeliveryPackage()
    {
        $assemblyExporterServiceMock = $this->createMock(AssemblyExporterService::class);
        $assemblyExporterServiceMock
            ->method('getOption')
            ->with('assembly_files_reader')
            ->willReturn($this->createMock(AssemblyFilesReaderInterface::class));

        $directoryMock = $this->createMock(Directory::class);
        $directoryMock
            ->expects($this->once())
            ->method('getDirectory')
            ->with('assembly')
            ->willReturn($directoryMock);

        $file1 = $this->getMockBuilder(File::class)->setConstructorArgs(['id', 'pref1'])->getMock();
        $file2 = $this->getMockBuilder(File::class)->setConstructorArgs(['id', 'pref2'])->getMock();

        $file1->method('exists')->willReturn(true);
        $file2->method('exists')->willReturn(false);

        $directoryMock
            ->expects($this->exactly(2))
            ->method('getFile')
            ->withConsecutive(['uri1.zip'], ['uri2.zip'])
            ->willReturnOnConsecutiveCalls($file1, $file2);

        $syncPackageServiceMock = $this->createMock(SyncPackageService::class);
        $syncPackageServiceMock
            ->expects($this->once())
            ->method('getSyncDirectory')
            ->willReturn($directoryMock);

        $fileKeyProviderService = $this->createMock(FileKeyProviderService::class);
        $fileKeyProviderService->method('getKeyFromFileSystem')->willReturn('key');

        $ontologyMock = $this->createMock(Ontology::class);
        $ontologyMock->expects($this->once())->method('getResource')->with('uri2')
            ->willReturn($this->createMock(\core_kernel_classes_Resource::class));

        $assemblyExporterServiceMock->expects($this->once())
            ->method('exportCompiledDelivery')
            ->willReturn('php://memory');

        $file2->expects($this->once())->method('write')->willReturn(false);

        $service = new ExportDeliveryAssembly();
        $service->setServiceLocator($this->getServiceLocatorMock(
            [
                AssemblyExporterService::SERVICE_ID => $assemblyExporterServiceMock,
                SyncPackageService::SERVICE_ID => $syncPackageServiceMock,
                FileKeyProviderService::SERVICE_ID => $fileKeyProviderService,
                Ontology::SERVICE_ID => $ontologyMock,
            ]
        ));

        $this->expectException(SyncBaseException::class);
        $service->createCompiledDeliveryPackage(['uri1', 'uri2']);
    }
}
