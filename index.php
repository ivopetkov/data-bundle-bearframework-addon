<?php

/*
 * Data bundle addon for Bear Framework
 * https://github.com/ivopetkov/data-bundle-bearframework-addon
 * Copyright (c) 2017 Ivo Petkov
 * Free to use under the MIT license.
 */

use \BearFramework\App;
use IvoPetkov\BearFramework\Addons\DataBundles;

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('\IvoPetkov\BearFramework\Addons\DataBundles', 'src/DataBundles.php')
        ->add('\IvoPetkov\BearFramework\Addons\DataBundle\AlreadyExistsException', 'src/DataBundles/AlreadyExistsException.php')
        ->add('\IvoPetkov\BearFramework\Addons\DataBundle\NotFoundException', 'src/DataBundles/NotFoundException.php');

$app->shortcuts->add('dataBundles', function() {
    return new DataBundles();
});
