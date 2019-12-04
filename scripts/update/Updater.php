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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\taoSyncServer\update;

use oat\tao\model\TaoOntology;
use oat\taoDeliveryRdf\model\ContainerRuntime;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoSync\model\dataProvider\SyncDataProviderCollection;
use oat\taoSync\model\Entity;
use oat\taoSyncServer\export\dataProvider\ByEligibility;
use oat\taoSyncServer\export\dataProvider\ByTestCenter;
use oat\taoSyncServer\export\dataProvider\dataFormatter\RdfDataFormatter;
use oat\taoSyncServer\export\dataProvider\dataReader\Administrator;
use oat\taoSyncServer\export\dataProvider\dataReader\Delivery;
use oat\taoSyncServer\export\dataProvider\dataReader\Eligibility;
use oat\taoSyncServer\export\dataProvider\dataReader\Proctor;
use oat\taoSyncServer\export\dataProvider\dataReader\TestTaker;
use oat\taoSyncServer\export\dataProvider\LtiConsumers;
use oat\taoSyncServer\export\dataProvider\TestCenter;
use oat\taoSyncServer\export\service\ExportPackage;
use oat\taoTestCenter\model\TestCenterService;
use oat\taoTestTaker\models\TestTakerService;

class Updater extends \common_ext_ExtensionUpdater
{
    public function update($initialVersion)
    {
        if ($this->isVersion('0.1.0')) {
            $defaultFormatterOptions =  [
                RdfDataFormatter::OPTION_EXCLUDED_FIELDS => [
                    TaoOntology::PROPERTY_UPDATED_AT,
                    Entity::CREATED_AT
                ]
            ];

            $providers = [
                TestCenter::TYPE => new TestCenter([
                    ByTestCenter::OPTION_FORMATTER => new RdfDataFormatter(
                        array_merge(
                            $defaultFormatterOptions,
                            [RdfDataFormatter::OPTION_ROOT_CLASS => TestCenterService::CLASS_URI]
                        )
                    ),
                ]),
                Eligibility::TYPE => new ByTestCenter([
                    ByTestCenter::OPTION_READER => new Eligibility(),
                    ByTestCenter::OPTION_FORMATTER => new RdfDataFormatter($defaultFormatterOptions)
                ]),
                Administrator::TYPE => new ByTestCenter([
                    ByTestCenter::OPTION_READER => new Administrator(),
                    ByTestCenter::OPTION_FORMATTER => new RdfDataFormatter($defaultFormatterOptions)
                ]),
                Proctor::TYPE => new ByTestCenter([
                    ByTestCenter::OPTION_READER => new Proctor(),
                    ByTestCenter::OPTION_FORMATTER => new RdfDataFormatter($defaultFormatterOptions)
                ]),
                TestTaker::TYPE => new ByEligibility([
                    ByEligibility::OPTION_READER => new TestTaker(),
                    ByEligibility::OPTION_FORMATTER => new RdfDataFormatter(
                        array_merge(
                            $defaultFormatterOptions,
                            [RdfDataFormatter::OPTION_ROOT_CLASS => TestTakerService::CLASS_URI_SUBJECT]
                        )
                    ),
                ]),
                Delivery::TYPE => new ByEligibility([
                    ByEligibility::OPTION_READER => new Delivery(),
                    ByEligibility::OPTION_FORMATTER => new RdfDataFormatter(
                        [
                            RdfDataFormatter::OPTION_EXCLUDED_FIELDS => [
                                TaoOntology::PROPERTY_UPDATED_AT,
                                Entity::CREATED_AT,
                                DeliveryAssemblyService::PROPERTY_ORIGIN,
                                DeliveryAssemblyService::PROPERTY_DELIVERY_DIRECTORY,
                                DeliveryAssemblyService::PROPERTY_DELIVERY_TIME,
                                DeliveryAssemblyService::PROPERTY_DELIVERY_RUNTIME,
                                ContainerRuntime::PROPERTY_CONTAINER,
                            ],
                            RdfDataFormatter::OPTION_ROOT_CLASS => DeliveryAssemblyService::CLASS_URI
                        ]
                    )
                ]),
                LtiConsumers::TYPE => new LtiConsumers([
                    ByEligibility::OPTION_FORMATTER => new RdfDataFormatter($defaultFormatterOptions)
                ]),
            ];
            $dataProviders = new SyncDataProviderCollection([
                SyncDataProviderCollection::OPTION_DATA_PROVIDERS => $providers
            ]);

            $this->getServiceManager()->register(SyncDataProviderCollection::SERVICE_ID, $dataProviders);
            $this->getServiceManager()->register(ExportPackage::SERVICE_ID, new ExportPackage());
            $this->setVersion('0.2.0');
        }
    }
}
