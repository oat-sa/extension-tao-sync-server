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

use core_kernel_classes_Class;
use oat\generis\model\data\Ontology;
use oat\generis\test\TestCase;
use oat\taoSyncServer\export\dataProvider\LtiConsumer;

class LtiConsumerTest extends TestCase
{
    public function testGetResources()
    {
        $classMock = $this->getMockBuilder(core_kernel_classes_Class::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ontologyMock = $this->createMock(Ontology::class);

        $service = new LtiConsumer();
        $service->setServiceLocator($this->getServiceLocatorMock([Ontology::SERVICE_ID => $ontologyMock]));

        $ontologyMock->expects($this->once())->method('getClass')
            ->with('http://www.tao.lu/Ontologies/TAOLTI.rdf#LTIConsumer')
            ->willReturn($classMock);

        $classMock->expects($this->once())->method('searchInstances')->willReturn(['result' => 'value']);

        $this->assertEquals(
            ['result' => 'value'],
            $service->getResources(['param' => 'value'])
        );
    }
}
