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

use core_kernel_classes_Resource;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\test\TestCase;
use oat\search\base\SearchGateWayInterface;
use oat\search\Query;
use oat\search\QueryBuilder;
use oat\search\ResultSet;
use oat\taoSync\model\Exception\SyncDataProviderException;
use oat\taoSyncServer\export\dataProvider\TestCenter;

class TestCenterTest extends TestCase
{
    public function testGetResources()
    {
        $searchMock = $this->getMockBuilder(ComplexSearchService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $query = $this->getMock(Query::class);
        $searchGateWay = $this->getMock(SearchGateWayInterface::class);

        $testCenter = new core_kernel_classes_Resource('testCenter');
        $result = new ResultSet([$testCenter], 1);

        $queryBuilder->expects($this->once())->method('setCriteria')->with($query);

        $searchMock->expects($this->once())->method('query')->willReturn($queryBuilder);
        $searchMock->expects($this->once())->method('searchType')->willReturn($query);
        $searchMock->expects($this->once())->method('getGateway')->willReturn($searchGateWay);
        $searchGateWay->expects($this->once())->method('search')->with($queryBuilder)->willReturn($result);

        $query->expects($this->once())->method('__call')->with('equals', ['orgID'])->willReturn($query);

        $query->expects($this->once())->method('add')
            ->with('http://www.taotesting.com/ontologies/synchro.rdf#organisationId')
            ->willReturn($query);

        $service = (new TestCenter())->setServiceLocator(
            $this->getServiceLocatorMock([ComplexSearchService::SERVICE_ID => $searchMock])
        );

        $this->assertEquals(['testCenter' => $testCenter], $service->getResources(['orgID' => 'orgID']));
    }

    public function testGetResourcesParameter()
    {
        $this->expectException(SyncDataProviderException::class);
        (new TestCenter())->getResources([]);
    }
}
