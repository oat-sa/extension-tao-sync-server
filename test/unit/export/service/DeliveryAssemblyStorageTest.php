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

use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\taoSync\package\SyncPackageService;
use oat\taoSyncServer\export\service\DeliveryAssemblyStorage;

class DeliveryAssemblyStorageTest extends TestCase
{
    /**
     * @var Directory|MockObject
     */
    private $assemblyDirectoryMock;

    /**
     * @var DeliveryAssemblyStorage
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assemblyDirectoryMock = $this->getMockBuilder(Directory::class)
            ->setConstructorArgs(['id', 'assembly'])
            ->getMock();

        $syncDirectoryMock = $this->getMockBuilder(Directory::class)
            ->setConstructorArgs(['id', 'sync'])
            ->getMock();

        $syncDirectoryMock->method('getDirectory')->with('assembly')->willReturn($this->assemblyDirectoryMock);

        $syncPackageServiceMock = $this->createMock(SyncPackageService::class);
        $syncPackageServiceMock->method('getSyncDirectory')->willReturn($syncDirectoryMock);

        $this->service = new DeliveryAssemblyStorage();
        $this->service->setServiceLocator(
            $this->getServiceLocatorMock([SyncPackageService::SERVICE_ID => $syncPackageServiceMock])
        );
    }

    public function testGetDeliveryAssemblyFile()
    {
        $this->assertSame(
            $this->getFileMock(),
            $this->service->getDeliveryAssemblyFile('http://sample/first.rdf#idelivery')
        );
    }

    public function testDeleteDeliveryAssemblyFile()
    {
        $this->getFileMock()->expects($this->once())->method('delete')->willReturn(true);
        $this->assertTrue($this->service->deleteDeliveryAssemblyFile('http://sample/first.rdf#idelivery'));
    }

    /**
     * @return File|MockObject
     */
    private function getFileMock()
    {
        $fileMock = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();

        $this->assemblyDirectoryMock
            ->expects($this->once())
            ->method('getFile')
            ->with('http_2_sample_1_first_0_rdf_3_idelivery.zip')
            ->willReturn($fileMock);

        return $fileMock;
    }
}
