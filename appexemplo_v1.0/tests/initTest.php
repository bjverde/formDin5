<?php
$path =  __DIR__.'/../';
$pathFormDin5 = $path.'app/lib/widget/FormDin5/';

//Adianti 
require_once $path.'lib/adianti/widget/base/TElement.php';

require_once $path.'lib/adianti/base/AdiantiFileSaveTrait.php';

require_once $path.'lib/adianti/control/TAction.php';
require_once $path.'lib/adianti/control/TPage.php';
require_once $path.'lib/adianti/control/TWindow.php';

require_once $path.'lib/adianti/core/AdiantiClassMap.php';
require_once $path.'lib/adianti/core/AdiantiApplicationLoader.php';
require_once $path.'lib/adianti/core/AdiantiCoreLoader.php';
require_once $path.'lib/adianti/core/AdiantiCoreTranslator.php';

require_once $path.'lib/adianti/widget/base/TElement.php';
require_once $path.'lib/adianti/widget/form/AdiantiFormInterface.php';

//require_once $path.'lib/adianti/wrapper/AdiantiPDFDesigner.php';
require_once $path.'lib/adianti/wrapper/BootstrapDatagridWrapper.php';
require_once $path.'lib/adianti/wrapper/BootstrapFormBuilder.php';


//FormDin5
require_once $pathFormDin5.'helpers/autoload_formdin_helper.php';
require_once $pathFormDin5.'webform/autoload_formdin.php';
require_once $pathFormDin5.'webform/FormDinFields/autoload_formdin_fields.php';


// define the autoloader
spl_autoload_register(array('Adianti\Core\AdiantiCoreLoader', 'autoload'));
Adianti\Core\AdiantiCoreLoader::loadClassMap();

$loader = require $path.'vendor/autoload.php';
$loader->register();