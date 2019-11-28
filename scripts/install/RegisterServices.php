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
 * Copyright (c) 2019  (original work) Open Assessment Technologies SA;
 *
 * @author Oleksandr Zagovorychev <zagovorichev@1pt.com>
 */

namespace oat\taoSyncServer\install;

use oat\oatbox\extension\InstallAction;
use oat\tao\model\TaoOntology;
use oat\taoDeliveryRdf\model\ContainerRuntime;
use oat\taoDeliveryRdf\model\DeliveryAssemblyService;
use oat\taoSync\model\dataProvider\DataProviderCollection;
use oat\taoSync\model\Entity;
use oat\taoSyncServer\export\dataProvider\ByEligibility;
use oat\taoSyncServer\export\dataProvider\ByTestCenter;
use oat\taoSyncServer\export\dataProvider\dataFormatter\DefaultFormatter;
use oat\taoSyncServer\export\dataProvider\dataReader\Administrator;
use oat\taoSyncServer\export\dataProvider\dataReader\Delivery;
use oat\taoSyncServer\export\dataProvider\dataReader\Eligibility;
use oat\taoSyncServer\export\dataProvider\dataReader\Proctor;
use oat\taoSyncServer\export\dataProvider\dataReader\TestTaker;
use oat\taoSyncServer\export\dataProvider\LtiConsumers;
use oat\taoSyncServer\export\dataProvider\TestCenter;
use oat\taoSyncServer\export\service\Export;
use common_Exception;

/**
 * php index.php 'oat\taoSyncServer\install\RegisterServices'
 *
 * Class RegisterSyncQueueRds
 * @package oat\taoSyncClient\scripts\install
 */
class RegisterServices extends InstallAction
{
    /**
     * @param $params
     * @throws common_Exception
     */
    public function __invoke($params)
    {
        $defaultFormatterOptions =  [
            DefaultFormatter::OPTION_EXCLUDED_FIELDS => [
                TaoOntology::PROPERTY_UPDATED_AT,
                Entity::CREATED_AT
            ]
        ];

        $providers = [
            TestCenter::TYPE => new TestCenter([
                ByTestCenter::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions),
            ]),
            Eligibility::TYPE => new ByTestCenter([
                ByTestCenter::OPTION_READER => new Eligibility(),
                ByTestCenter::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions)
            ]),
            Administrator::TYPE => new ByTestCenter([
                ByTestCenter::OPTION_READER => new Administrator(),
                ByTestCenter::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions)
            ]),
            Proctor::TYPE => new ByTestCenter([
                ByTestCenter::OPTION_READER => new Proctor(),
                ByTestCenter::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions)
            ]),
            TestTaker::TYPE => new ByEligibility([
                ByEligibility::OPTION_READER => new TestTaker(),
                ByEligibility::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions)
            ]),
            Delivery::TYPE => new ByEligibility([
                ByEligibility::OPTION_READER => new Delivery(),
                ByEligibility::OPTION_FORMATTER => new DefaultFormatter(
                    [
                        DefaultFormatter::OPTION_EXCLUDED_FIELDS => [
                            TaoOntology::PROPERTY_UPDATED_AT,
                            Entity::CREATED_AT,
                            DeliveryAssemblyService::PROPERTY_ORIGIN,
                            DeliveryAssemblyService::PROPERTY_DELIVERY_DIRECTORY,
                            DeliveryAssemblyService::PROPERTY_DELIVERY_TIME,
                            DeliveryAssemblyService::PROPERTY_DELIVERY_RUNTIME,
                            ContainerRuntime::PROPERTY_CONTAINER,
                        ]
                    ]
                )
            ]),
            LtiConsumers::TYPE => new LtiConsumers([
                ByEligibility::OPTION_FORMATTER => new DefaultFormatter($defaultFormatterOptions)
            ]),
        ];
        $dataProviders = new DataProviderCollection([
            DataProviderCollection::OPTION_DATA_PROVIDERS => $providers
        ]);

        $this->getServiceManager()->register(DataProviderCollection::SERVICE_ID, $dataProviders);
        $this->getServiceManager()->register(Export::SERVICE_ID, new Export());
    }
}
