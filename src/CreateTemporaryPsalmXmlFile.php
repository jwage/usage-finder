<?php

declare(strict_types=1);

namespace UsageFinder;

use function file_get_contents;
use function file_put_contents;
use function sprintf;
use function str_replace;
use function sys_get_temp_dir;
use function tempnam;

final class CreateTemporaryPsalmXmlFile
{
    private const PROJECT_FILES_XML = <<<'PROJECT_FILES'
    <projectFiles>
        <directory name="%s" />
        <ignoreFiles>
            <directory name="%s" />
        </ignoreFiles>
    </projectFiles>
PROJECT_FILES;

    public function __invoke(string $path) : string
    {
        $templatePath = __DIR__ . '/../usage-finder-psalm.xml.template';

        $tmpPath = tempnam(sys_get_temp_dir(), 'usage-finder-psalm') . '.xml';

        $codePath = (new GuessCodePath())->__invoke($path);

        $projectFilesXml = sprintf(self::PROJECT_FILES_XML, $codePath, 'vendor');

        $configXml = file_get_contents($templatePath);
        $configXml = str_replace('{{ projectFilesXml }}', $projectFilesXml, $configXml);

        file_put_contents($tmpPath, $configXml);

        return $tmpPath;
    }
}
