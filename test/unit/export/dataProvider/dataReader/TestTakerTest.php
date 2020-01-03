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

use oat\generis\model\data\Ontology;
use oat\generis\test\TestCase;
use oat\taoSync\model\Exception\SyncDataProviderException;
use core_kernel_persistence_smoothsql_SmoothModel;
use core_kernel_classes_Resource;
use oat\taoSyncServer\export\dataProvider\dataReader\TestTaker;

class TestTakerTest extends TestCase
{
    /**
     * @var TestTaker
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $this->service = (new TestTaker())->setServiceLocator(
            $this->getServiceLocatorMock([Ontology::SERVICE_ID => new core_kernel_persistence_smoothsql_SmoothModel()])
        );
    }

    public function testGetData()
    {
        $data = [
            ['http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestTaker' => 'uri1'],
            ['http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestTaker' => 'uri2'],
        ];

        $resource1 = new core_kernel_classes_Resource('uri1');
        $resource1->setModel(new core_kernel_persistence_smoothsql_SmoothModel());
        $resource2 = new core_kernel_classes_Resource('uri2');
        $resource2->setModel(new core_kernel_persistence_smoothsql_SmoothModel());

        $this->assertEquals(
            [
                'uri1' => $resource1,
                'uri2' => $resource2
            ],
            $this->service->getData($data));
    }

    public function testGetDataMultiply()
    {
        $data = [
            ['http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestTaker' => ['uri1', 'uri3']],
            ['http://www.tao.lu/Ontologies/TAOProctor.rdf#EligibileTestTaker' => 'uri2'],
        ];

        $resource1 = new core_kernel_classes_Resource('uri1');
        $resource1->setModel(new core_kernel_persistence_smoothsql_SmoothModel());
        $resource2 = new core_kernel_classes_Resource('uri2');
        $resource2->setModel(new core_kernel_persistence_smoothsql_SmoothModel());
        $resource3 = new core_kernel_classes_Resource('uri3');
        $resource3->setModel(new core_kernel_persistence_smoothsql_SmoothModel());

        $this->assertEquals(
            [
                'uri1' => $resource1,
                'uri3' => $resource3,
                'uri2' => $resource2
            ],
            $this->service->getData($data));
    }

    public function testGetDataWithInvalidParameter()
    {
        $this->expectException(SyncDataProviderException::class);
        (new TestTaker())->getData(['data']);
    }

    public function testGetDataWithInvalidParameters()
    {
        $this->expectException(SyncDataProviderException::class);
        (new TestTaker())->getData([['key'=> 'value']]);
    }
}
