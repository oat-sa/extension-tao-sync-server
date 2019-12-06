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
 * @author Yuri Filippovich
 */

namespace oat\taoSyncServer\export\dataProvider\dataFormatter;

use core_kernel_classes_Resource;
use oat\generis\model\OntologyAwareTrait;
use oat\generis\model\OntologyRdf;
use oat\taoSync\model\export\dataProvider\dataFormatter\AbstractDataFormatter;

class RdfDataFormatter extends AbstractDataFormatter
{
    use OntologyAwareTrait;

    const OPTION_EXCLUDED_FIELDS = 'excluded-fields';
    const OPTION_ROOT_CLASS = 'root-class';

    /**
     * @inheritDoc
     */
    public function format($resource)
    {
        $properties = $this->formatResource($resource);
        if ($this->isResourceClassNeed($properties)) {
            $properties['classes'] = $this->getResourceClasses($properties[OntologyRdf::RDF_TYPE]);
        }
        return $properties;
    }

    /**
     * @param core_kernel_classes_Resource $resource
     * @return array
     */
    public function formatResource(core_kernel_classes_Resource $resource)
    {
        $properties = $this->filterProperties($resource->getRdfTriples()->toArray());
        $properties['id'] = $resource->getUri();
        return $properties;
    }

    /**
     * @param array $properties
     * @return bool
     */
    private function isResourceClassNeed(array $properties)
    {
        return $this->hasOption(self::OPTION_ROOT_CLASS)
            && array_key_exists(OntologyRdf::RDF_TYPE, $properties)
            && $properties[OntologyRdf::RDF_TYPE] !== $this->getOption(self::OPTION_ROOT_CLASS);
    }

    /**
     * @param string $uri
     * @return array
     */
    private function getResourceClasses($uri)
    {
       $class = $this->getClass($uri);

       $parentClasses = $class->getParentClasses(true);
       $result = [$this->formatResource($class)];

       foreach ($parentClasses as $parentClass) {
           $result[] = $this->formatResource($parentClass);
       }
       return $result;
    }

    /**
     * @param array $triples
     * @return array
     */
    private function filterProperties(array $triples)
    {
        $excludedProperties = $this->getExcludedProperties();
        $properties = [];

        foreach ($triples as $triple) {
            if (!in_array($triple->predicate, $excludedProperties)) {
                $properties[$triple->predicate] = $triple->object;
            }

        }
        return $properties;
    }

    /**
     * @return array
     */
    private function getExcludedProperties()
    {
        if (is_array($this->getOption(self::OPTION_EXCLUDED_FIELDS))) {
            return $this->getOption(self::OPTION_EXCLUDED_FIELDS);
        }
        return [];
    }
}
