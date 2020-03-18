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

namespace oat\taoSyncServer\test\unit\export\dataProvider\dataReader;

use core_kernel_classes_Class;
use oat\generis\model\data\Ontology;
use oat\generis\test\MockObject;
use oat\generis\test\TestCase;
use oat\taoSync\model\Exception\SyncDataProviderException;
use oat\taoSyncServer\export\dataProvider\dataReader\TestCenterAdministrator;

class TestCenterAdministratorTest extends TestCase
{
    /**
     * @var Ontology|MockObject
     */
    private $ontologyMock;

    /**
     * @var core_kernel_classes_Class|MockObject
     */
    private $classMock;

    /**
     * @var TestCenterAdministrator
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ontologyMock = $this->createMock(Ontology::class);
        $this->classMock = $this->getMockBuilder(core_kernel_classes_Class::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ontologyMock->method('getClass')->willReturn($this->classMock);

        $this->service = (new TestCenterAdministrator())->setServiceLocator(
            $this->getServiceLocatorMock([Ontology::SERVICE_ID => $this->ontologyMock])
        );
    }

    public function testGetData()
    {
        $this->ontologyMock->expects($this->once())
            ->method('getClass')->with('http://www.tao.lu/Ontologies/TAO.rdf#User');

        $this->classMock->expects($this->once())
            ->method('searchInstances')
            ->with(
                [
                    'http://www.tao.lu/Ontologies/TAOTestCenter.rdf#administrator' => 'uri',
                    'http://www.tao.lu/Ontologies/generis.rdf#userRoles'
                    => 'http://www.tao.lu/Ontologies/TAOProctor.rdf#TestCenterAdministratorRole'
                ],
                ['recursive' => false, 'like' => false]
            )
            ->willReturn(['key' => 'value']);

        $this->assertEquals(['key' => 'value'], $this->service->getData(['uri' => 'uri']));
    }

    public function testGetDataWithInvalidParameter()
    {
        $this->expectException(SyncDataProviderException::class);
        $this->service->getData([]);
    }
}
