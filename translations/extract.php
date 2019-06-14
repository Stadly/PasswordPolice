<?php

declare(strict_types=1);

use Symfony\Component\Translation\Dumper\PoFileDumper;
use Symfony\Component\Translation\Extractor\PhpExtractor;
use Symfony\Component\Translation\MessageCatalogue;

require_once '../vendor/autoload.php';

$catalogue = new MessageCatalogue('en_US');

$extractor = new PhpExtractor();
$extractor->extract('../src', $catalogue);

$dumper = new PoFileDumper();
$dumper->dump($catalogue, ['path' => '.']);
