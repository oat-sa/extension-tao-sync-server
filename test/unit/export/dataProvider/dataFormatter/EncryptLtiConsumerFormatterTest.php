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

namespace oat\taoSyncServer\test\unit\export\dataProvider\dataFormatter;

use core_kernel_classes_ContainerCollection;
use core_kernel_classes_Resource;
use core_kernel_classes_Triple;
use oat\generis\test\TestCase;
use oat\taoEncryption\Service\EncryptionSymmetricService;
use oat\taoEncryption\Service\KeyProvider\FileKeyProviderService;
use oat\taoEncryption\Service\KeyProvider\SimpleKeyProviderService;
use oat\taoSyncServer\export\dataProvider\dataFormatter\EncryptLtiConsumerFormatter;
use oat\taoSyncServer\export\dataProvider\dataFormatter\RdfDataFormatter;

class EncryptLtiConsumerFormatterTest extends TestCase
{
    public function testFormat()
    {
        $resourceMock = $this->createMock(core_kernel_classes_Resource::class);
        $triplesMock = $this->createMock(core_kernel_classes_ContainerCollection::class);
        $fileKeyProviderServiceMock = $this->createMock(FileKeyProviderService::class);
        $symmetricEncryptionServiceMock = $this->createMock(EncryptionSymmetricService::class);
        $keyProviderServiceMock = $this->createMock(SimpleKeyProviderService::class);

        $triple1 = new core_kernel_classes_Triple();
        $triple1->predicate = 'predicate';
        $triple1->object = 'value';
        $triple2 = new core_kernel_classes_Triple();
        $triple2->predicate = 'predicate2';
        $triple2->object = 'value2';
        $triple3 = new core_kernel_classes_Triple();
        $triple3->predicate = 'http://www.tao.lu/Ontologies/TAO.rdf#CustomerAppKey';
        $triple3->object = 'key';

        $triplesMock->method('toArray')->willReturn([$triple1, $triple2, $triple3]);

        $resourceMock->method('getUri')->willReturn('uri');
        $resourceMock->method('getRdfTriples')->willReturn($triplesMock);

        $rdfDataFormatter = new EncryptLtiConsumerFormatter(
            [
                RdfDataFormatter::OPTION_EXCLUDED_FIELDS => ['predicate2'],
                'encryptionService' => 'symmetricEncryptionService',
                'keyProviderService' => 'keyProviderService'
            ]
        );

        $symmetricEncryptionServiceMock->expects($this->once())
            ->method('encrypt')->with('keyFromFileSystem')->willReturn('encrypted');

         $fileKeyProviderServiceMock->expects($this->once())
             ->method('getKeyFromFileSystem')->willReturn('keyFromFileSystem');

        $rdfDataFormatter->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    'symmetricEncryptionService' => $symmetricEncryptionServiceMock,
                    'keyProviderService' => $keyProviderServiceMock,
                    FileKeyProviderService::SERVICE_ID => $fileKeyProviderServiceMock,
                ]
            )
        );

        $this->assertEquals(
            [
                'predicate' => 'value',
                'uri' => 'uri',
                'http://www.tao.lu/Ontologies/TAO.rdf#EncryptedApplicationKey' => base64_encode('encrypted')
            ],
            $rdfDataFormatter->format($resourceMock)
        );
    }
}
