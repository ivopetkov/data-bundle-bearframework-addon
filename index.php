<?php

/*
 * Data bundle addon for Bear Framework
 * https://github.com/ivopetkov/data-bundle-bearframework-addon
 * Copyright (c) Ivo Petkov
 * Free to use under the MIT license.
 */

use BearFramework\App;
use IvoPetkov\BearFramework\Addons\DataBundle;

$app = App::get();
$context = $app->context->get(__FILE__);

$context->classes
        ->add('IvoPetkov\BearFramework\Addons\DataBundle', 'classes/DataBundle.php')
        ->add('IvoPetkov\BearFramework\Addons\DataBundle\AlreadyExistsException', 'classes/DataBundle/AlreadyExistsException.php')
        ->add('IvoPetkov\BearFramework\Addons\DataBundle\NotFoundException', 'classes/DataBundle/NotFoundException.php');

$app->shortcuts->add('dataBundle', function() {
    return new DataBundle();
});
