<?php

/*
 * Data bundle addon for Bear Framework
 * https://github.com/ivopetkov/data-bundle-bearframework-addon
 * Copyright (c) 2017 Ivo Petkov
 * Free to use under the MIT license.
 */

/**
 * @runTestsInSeparateProcesses
 */
class ComponentsTest extends BearFrameworkAddonTestCase
{

    /**
     * 
     */
    public function testAll()
    {
        $app = $this->getApp();

        $list = $app->dataBundles->getItemsList('test1');
        $this->assertTrue($list->length === 0);

        $item = $app->data->make('example/1.json', '1');
        $item->metadata->meta1 = '11';
        $app->data->set($item);
        $app->data->set($app->data->make('example/2.json', '2'));
        $app->data->set($app->data->make('example/3.json', '3'));

        $app->dataBundles->create('test1', [
            'example/1.json',
            'example/2.json'
        ]);

        $app->dataBundles->addItem('test1', 'example/3.json');
        $app->dataBundles->addItem('test1', 'example/4.json');

        $list = $app->dataBundles->getItemsList('test1');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1.json');
        $this->assertTrue($list[0]->value === '1');
        $this->assertTrue($list[0]->metadata->meta1 === '11');
        $this->assertTrue($list[1]->key === 'example/2.json');
        $this->assertTrue($list[1]->value === '2');
        $this->assertTrue($list[2]->key === 'example/3.json');
        $this->assertTrue($list[2]->value === '3');

        $app->data->set($app->data->make('example/2.json', '22'));

        $list = $app->dataBundles->getItemsList('test1');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[1]->key === 'example/2.json');
        $this->assertTrue($list[1]->value === '2');

        $app->dataBundles->updateItem('test1', 'example/2.json');

        $list = $app->dataBundles->getItemsList('test1');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1.json');
        $this->assertTrue($list[0]->value === '1');
        $this->assertTrue($list[0]->metadata->meta1 === '11');
        $this->assertTrue($list[1]->key === 'example/2.json');
        $this->assertTrue($list[1]->value === '22');
        $this->assertTrue($list[2]->key === 'example/3.json');
        $this->assertTrue($list[2]->value === '3');


        $app->dataBundles->removeItem('test1', 'example/2.json');
        $list = $app->dataBundles->getItemsList('test1');
        $this->assertTrue($list->length === 2);
        $this->assertTrue($list[0]->key === 'example/1.json');
        $this->assertTrue($list[0]->value === '1');
        $this->assertTrue($list[0]->metadata->meta1 === '11');
        $this->assertTrue($list[1]->key === 'example/3.json');
        $this->assertTrue($list[1]->value === '3');
    }

    /**
     * 
     */
    public function testAlreadyCreatedException()
    {
        $app = $this->getApp();

        $app->dataBundles->create('test2');
        $this->setExpectedException('\IvoPetkov\BearFramework\Addons\DataBundle\AlreadyExistsException');
        $app->dataBundles->create('test2');
    }

    /**
     * 
     */
    public function testMultipleItems()
    {
        $app = $this->getApp();

        $app->data->set($app->data->make('example/1a.json', '1'));
        $app->data->set($app->data->make('example/2a.json', '2'));
        $app->data->set($app->data->make('example/3a.json', '3'));

        $app->dataBundles->create('test1a');

        $app->dataBundles->addItems('test1a', [
            'example/1a.json',
            'example/2a.json',
            'example/3a.json'
        ]);

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1a.json');
        $this->assertTrue($list[0]->value === '1');
        $this->assertTrue($list[1]->key === 'example/2a.json');
        $this->assertTrue($list[1]->value === '2');
        $this->assertTrue($list[2]->key === 'example/3a.json');
        $this->assertTrue($list[2]->value === '3');

        $app->data->set($app->data->make('example/1a.json', '11'));
        $app->data->set($app->data->make('example/2a.json', '22'));

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1a.json');
        $this->assertTrue($list[0]->value === '1');
        $this->assertTrue($list[1]->key === 'example/2a.json');
        $this->assertTrue($list[1]->value === '2');
        $this->assertTrue($list[2]->key === 'example/3a.json');
        $this->assertTrue($list[2]->value === '3');

        $app->dataBundles->updateItems('test1a', [
            'example/1a.json',
            'example/2a.json'
        ]);

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1a.json');
        $this->assertTrue($list[0]->value === '11');
        $this->assertTrue($list[1]->key === 'example/2a.json');
        $this->assertTrue($list[1]->value === '22');
        $this->assertTrue($list[2]->key === 'example/3a.json');
        $this->assertTrue($list[2]->value === '3');

        $app->data->delete('example/1a.json');

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 3);
        $this->assertTrue($list[0]->key === 'example/1a.json');
        $this->assertTrue($list[0]->value === '11');
        $this->assertTrue($list[1]->key === 'example/2a.json');
        $this->assertTrue($list[1]->value === '22');
        $this->assertTrue($list[2]->key === 'example/3a.json');
        $this->assertTrue($list[2]->value === '3');

        $app->dataBundles->removeItems('test1a', [
            'example/2a.json',
            'example/3a.json'
        ]);

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 1);
        $this->assertTrue($list[0]->key === 'example/1a.json');
        $this->assertTrue($list[0]->value === '11');

        $app->dataBundles->updateItem('test1a', 'example/1a.json');

        $list = $app->dataBundles->getItemsList('test1a');
        $this->assertTrue($list->length === 0);
    }

}
