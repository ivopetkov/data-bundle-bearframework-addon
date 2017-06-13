<?php

/*
 * Data bundle addon for Bear Framework
 * https://github.com/ivopetkov/data-bundle-bearframework-addon
 * Copyright (c) 2017 Ivo Petkov
 * Free to use under the MIT license.
 */

namespace IvoPetkov\BearFramework\Addons;

use BearFramework\App;

/**
 * 
 */
class DataBundle
{

    public function exists(string $id)
    {
        return $this->mapExists($id);
    }

    public function create(string $id, array $itemKeys = [])
    {
        if ($this->mapExists($id)) {
            throw new \IvoPetkov\BearFramework\Addons\DataBundle\AlreadyExistsException('The data bundle ' . $id . ' already exists');
        } else {
            $map = ['items' => []];
            foreach ($itemKeys as $itemKey) {
                $map['items'][$itemKey] = md5($itemKey . '-' . uniqid()); // version
            }
            $this->setMap($id, $map);
        }
    }

    public function addItem(string $id, string $itemKey)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null) {
            $map['items'][$itemKey] = md5($itemKey . '-' . uniqid()); // version
            $this->setMap($id, $map);
        }
        $this->unlockMap($id);
    }

    public function addItems(string $id, array $itemKeys)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null && !empty($itemKeys)) {
            foreach ($itemKeys as $itemKey) {
                $map['items'][$itemKey] = md5($itemKey . '-' . uniqid()); // version
            }
            $this->setMap($id, $map);
        }
        $this->unlockMap($id);
    }

    public function updateItem(string $id, string $itemKey)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null) {
            if (isset($map['items'][$itemKey])) {
                $map['items'][$itemKey] = md5($itemKey . '-' . uniqid()); // version
                $this->setMap($id, $map);
            }
        }
        $this->unlockMap($id);
    }

    public function updateItems(string $id, array $itemKeys)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null && !empty($itemKeys)) {
            $hasChange = false;
            foreach ($itemKeys as $itemKey) {
                if (isset($map['items'][$itemKey])) {
                    $map['items'][$itemKey] = md5($itemKey . '-' . uniqid()); // version
                    $hasChange = true;
                }
            }
            if ($hasChange) {
                $this->setMap($id, $map);
            }
        }
        $this->unlockMap($id);
    }

    public function removeItem(string $id, string $itemKey)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null) {
            if (isset($map['items'][$itemKey])) {
                unset($map['items'][$itemKey]);
                $this->setMap($id, $map);
            }
        }
        $this->unlockMap($id);
    }

    public function removeItems(string $id, array $itemKeys)
    {
        $this->lockMap($id);
        $map = $this->getMap($id);
        if ($map !== null && !empty($itemKeys)) {
            $hasChange = false;
            foreach ($itemKeys as $itemKey) {
                if (isset($map['items'][$itemKey])) {
                    unset($map['items'][$itemKey]);
                    $hasChange = true;
                }
            }
            if ($hasChange) {
                $this->setMap($id, $map);
            }
        }
        $this->unlockMap($id);
    }

    public function getItemsList(string $id)
    {
        $map = $this->getMap($id);
        if (is_array($map) && isset($map['items']) && !empty($map['items'])) {
            $data = $this->getData($id);
            $hasChange = false;
            if (is_array($data) && isset($data['items'])) {
                foreach ($map['items'] as $itemKey => $itemVersion) {
                    if (!isset($data['items'][$itemKey]) || $data['items'][$itemKey][0] !== $itemVersion) {
                        $prepareResult = $this->prepareData($id);
                        $hasChange = true;
                        break;
                    }
                }
            } else {
                $prepareResult = $this->prepareData($id);
                $hasChange = true;
            }
            if ($hasChange) {
                $data = $prepareResult;
            }
            return new \BearFramework\DataList(function() use ($map, $data) {
                $app = \BearFramework\App::get();
                $appData = $app->data;
                $list = [];
                foreach ($map['items'] as $itemKey => $itemVersion) {
                    $itemData = $data['items'][$itemKey];
                    if ($itemData[1] === 1) {
                        $dataItem = $appData->make($itemKey, $itemData[2]);
                        foreach ($itemData[3] as $name => $value) {
                            $dataItem->metadata->$name = $value;
                        }
                        $list[] = $dataItem;
                    }
                }
                return $list;
            });
        }
        return new \BearFramework\DataList();
    }

    private function prepareData(string $id)
    {
        $app = App::get();
        $this->lockMap($id);
        $map = $this->getMap($id);
        if (is_array($map) && isset($map['items']) && !empty($map['items'])) {
            $appData = $app->data;
            $this->lockData($id);
            $data = $this->getData($id);
            if (!is_array($data)) {
                $data = [];
            }
            if (!isset($data['items'])) {
                $data['items'] = [];
            }
            $hasDataChange = false;
            foreach ($map['items'] as $itemKey => $itemVersion) {
                if (!isset($data['items'][$itemKey]) || $data['items'][$itemKey][0] !== $itemVersion) {
                    $dataItem = $appData->get($itemKey);
                    if ($dataItem === null) {
                        $data['items'][$itemKey] = [$itemVersion, 2];
                    } else {
                        $data['items'][$itemKey] = [$itemVersion, 1, $dataItem->value, $dataItem->metadata->toArray()];
                    }
                    $hasDataChange = true;
                }
            }
            if ($hasDataChange) {
                $this->setData($id, $data);
            }
            $this->unlockData($id);
        }
        $this->unlockMap($id);
        return isset($data) ? $data : [];
    }

    public function prepare(string $id)
    {
        $this->prepareData($id);
    }

    public function getStatus(string $id)
    {
        
    }

    private function mapExists(string $id)
    {
        $app = App::get();
        $mapKey = 'databundle/' . md5($id) . '.map';
        return $app->data->exists($mapKey);
    }

    private function getMap(string $id)
    {
        $app = App::get();
        $mapKey = 'databundle/' . md5($id) . '.map';
        $value = $app->data->getValue($mapKey);
        if ($value === null) {
            return null;
        }
        return json_decode(gzuncompress($value), true);
    }

    private function setMap(string $id, array $data)
    {
        $app = App::get();
        $mapKey = 'databundle/' . md5($id) . '.map';
        $app->data->set($app->data->make($mapKey, gzcompress(json_encode($data))));
    }

    private function lockMap(string $id)
    {
        \IvoPetkov\Lock::acquire('databundle.map.' . md5($id));
    }

    private function unlockMap(string $id)
    {
        \IvoPetkov\Lock::release('databundle.map.' . md5($id));
    }

    private function getData(string $id)
    {
        $app = App::get();
        $dataKey = 'databundle/' . md5($id) . '.data';
        $value = $app->data->getValue($dataKey);
        if ($value === null) {
            return null;
        }
        return json_decode(gzuncompress($value), true);
    }

    private function setData(string $id, array $data)
    {
        $app = App::get();
        $dataKey = 'databundle/' . md5($id) . '.data';
        $app->data->set($app->data->make($dataKey, gzcompress(json_encode($data))));
    }

    private function lockData(string $id)
    {
        \IvoPetkov\Lock::acquire('databundle.data.' . md5($id));
    }

    private function unlockData(string $id)
    {
        \IvoPetkov\Lock::release('databundle.data.' . md5($id));
    }

}
