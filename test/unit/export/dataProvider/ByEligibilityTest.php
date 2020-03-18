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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA ;
 */

namespace oat\taoSyncServer\test\unit\export\dataProvider;

use oat\generis\test\TestCase;
use oat\oatbox\log\LoggerService;
use oat\taoSyncServer\export\dataProvider\ByEligibility;
use oat\taoSyncServer\export\dataProvider\dataReader\Eligibility;

class ByEligibilityTest extends TestCase
{
    public function testGetResources()
    {
        $eligibilityReaderMock = $this->createMock(Eligibility::class);
        $service = new ByEligibility(['reader' => $eligibilityReaderMock]);
        $service->setServiceLocator($this->getServiceLocatorMock(
            [LoggerService::SERVICE_ID => $this->createMock(LoggerService::class)]
        ));

        $eligibilityReaderMock->expects($this->once())
            ->method('getData')
            ->with(['uri' => 'uri'])
            ->willReturn(['key' => 'value']);

        $this->assertEquals(
            ['key' => 'value'],
            $service->getResources(['Eligibility' => ['resources' => ['uri' => 'uri']]])
        );
    }

    public function testGetResourcesWithEmptyData()
    {
        $this->assertEquals([], (new ByEligibility())->getResources([]));
    }
}
