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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoSyncServer\test\unit\export\service;

use oat\generis\test\TestCase;
use oat\taoSync\model\dataProvider\SyncDataProviderCollection;
use oat\taoSync\package\SyncPackageService;
use oat\taoSyncServer\export\service\ExportPackage;

class ExportPackageTest extends TestCase
{
    public function testGetInvalidProvider()
    {
        $syncDataProviderCollection = $this->createMock(SyncDataProviderCollection::class);
        $syncPackageService = $this->createMock(SyncPackageService::class);

        $syncDataProviderCollection->expects($this->once())
            ->method('getData')
            ->with(['orgID' => 33])
            ->willReturn(['key' => 'data']);

        $syncPackageService->expects($this->once())
            ->method('createPackage')
            ->with(['key' => 'data'], 'syncPackageServer_1.json');


        $exportPackage = (new ExportPackage())->setServiceLocator($this->getServiceLocatorMock(
            [
                'taoSync/SyncDataProviderCollection' => $syncDataProviderCollection,
                'taoSync/SyncPackageService' => $syncPackageService
            ]
        ));
        $exportPackage->createPackage(1, 33);
    }
}
