<?php

declare(strict_types=1);

namespace UsageFinder;

use Symfony\Component\Console\Application;
use UsageFinder\Command\FindClassMethodUsagesCommand;

require_once __DIR__ . '/../vendor/autoload.php';

$application = new Application();
$application->add(new FindClassMethodUsagesCommand());
$application->run();
