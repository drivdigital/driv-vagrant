<?php
require_once 'Test/TestSuite.php';

require_once 'fixtures/Vagrant.php';

require_once 'TestSetupPhp.php';
require_once 'TestSystem.php';

$suite = new TestSuite();
$suite
    ->addTest( new TestSetupPhp() )
    ->addTest( new TestSystem() )
    ->run();
$suite->printResults();
