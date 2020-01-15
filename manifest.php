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
 *
 */

use oat\taoSyncServer\scripts\install\RegisterDataProviders;
use oat\taoSyncServer\scripts\install\RegisterExportDeliveryAssembly;
use oat\taoSyncServer\scripts\update\Updater;
use oat\taoSync\model\SyncService;

/**
 * Generated using taoDevTools 6.1.0
 */
return [
    'name' => 'taoSyncServer',
    'label' => 'Tao Sync Central Server',
    'description' => 'TAO central server for synchronisation.',
    'license' => 'GPL-2.0',
    'version' => '0.3.0',
    'author' => 'Open Assessment Technologies SA',
    'requires' => array(
        'taoSync' => '>=7.2.0',
    ),
    'managementRole' => SyncService::TAO_SYNC_ROLE,
    'acl' => [
        ['grant', SyncService::TAO_SYNC_ROLE, ['ext' => 'taoSyncServer']],
    ],
    'install'        => [
        'php' => [
            RegisterDataProviders::class,
        ]
    ],
    'uninstall' => [],
    'update' => Updater::class,
    'routes' => [],
    'constants' => [
        # views directory
        "DIR_VIEWS" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR,

        #BASE URL (usually the domain root)
        'BASE_URL' => ROOT_URL . 'taoSyncServer/',
    ]
];