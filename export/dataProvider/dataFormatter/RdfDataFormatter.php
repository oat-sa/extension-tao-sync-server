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
use oat\taoSync\model\dataProvider\dataFormatter\AbstractDataFormatter;

class RdfDataFormatter extends AbstractDataFormatter
{
    use OntologyAwareTrait;

    const OPTION_EXCLUDED_FIELDS = 'excluded-fields';
    const OPTION_ROOT_CLASS = 'root-class';

    /**
     * @param core_kernel_classes_Resource[] $resources
     * @return array
     */
    public function formatAll(array $resources)
    {
        $data = [];
        $classesToExport = [];

        foreach ($resources as $resource) {
            $properties = $this->formatResource($resource);
            if ($this->isResourceClassNeed($properties)) {
                $classesToExport[current($properties[OntologyRdf::RDF_TYPE])]
                    = current($properties[OntologyRdf::RDF_TYPE]);
            }
            $data[] = $properties;
        }

        if ($classesToExport) {
            $data[0]['classes'] = $this->getFormattedClasses(
                $classesToExport, $this->getOption(self::OPTION_ROOT_CLASS)
            );
        }

        return $data;
    }

    /**
     * @param array $classesToExport
     * @param string $rootClass
     * @return array
     */
    protected function getFormattedClasses(array $classesToExport, $rootClass)
    {
        return $this->formatClasses(
            $this->getParentClasses($classesToExport, $rootClass)
        );
    }

    /**
     * @param array $classesToExport
     * @param string $rootClass
     * @return array
     */
    protected function getParentClasses(array $classesToExport, $rootClass)
    {
        foreach ($classesToExport as $class) {
            $class = $this->getClass($class);
            foreach ($class->getParentClasses(true) as $parent) {
                if($parent->getUri() == $rootClass || array_key_exists($parent->getUri(), $classesToExport)) {
                    break;
                }
                $classesToExport[$parent->getUri()] = $parent->getUri();
            }
        }
        return $classesToExport;
    }

    /**
     * @param array $ids
     * @return array
     */
    protected function formatClasses(array $ids)
    {
        $result = [];

        foreach ($ids as $id) {
            $result[] = $this->formatResource($this->getClass($id));
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function format($resource)
    {
        return $this->formatResource($resource);
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
    protected function isResourceClassNeed(array $properties)
    {
        return $this->hasOption(self::OPTION_ROOT_CLASS)
            && array_key_exists(OntologyRdf::RDF_TYPE, $properties)
            && current($properties[OntologyRdf::RDF_TYPE]) !== $this->getOption(self::OPTION_ROOT_CLASS);
    }

    /**
     * @param array $triples
     * @return array
     */
    protected function filterProperties(array $triples)
    {
        $excludedProperties = $this->getExcludedProperties();
        $properties = [];

        foreach ($triples as $triple) {
            if (!in_array($triple->predicate, $excludedProperties)) {
                if (!array_key_exists($triple->predicate, $properties)) {
                    $properties[$triple->predicate] = [];
                }
                $properties[$triple->predicate][] = $triple->object;
            }

        }
        return $properties;
    }

    /**
     * @return array
     */
    protected function getExcludedProperties()
    {
        if (is_array($this->getOption(self::OPTION_EXCLUDED_FIELDS))) {
            return $this->getOption(self::OPTION_EXCLUDED_FIELDS);
        }
        return [];
    }
}
