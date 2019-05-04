<?php

declare(strict_types=1);

namespace UsageFinder;

use function file_exists;
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
        {{ directoryXml }}
        {{ ignoreFilesXml }}
    </projectFiles>
PROJECT_FILES;

    private const DIRECTORY_XML = <<<'DIRECTORY'
        <directory name="%s" />
DIRECTORY;

    private const IGNORE_FILES_XML = <<<'IGNORE_FILES'
        <ignoreFiles>
            <directory name="%s" />
        </ignoreFiles>
IGNORE_FILES;

    public function __invoke(string $path) : string
    {
        $templatePath = __DIR__ . '/../usage-finder-psalm.xml.template';

        $tmpPath = tempnam(sys_get_temp_dir(), 'usage-finder-psalm') . '.xml';

        $vendorPath = null;

        if (file_exists($path . '/vendor')) {
            $vendorPath = 'vendor';
        }

        $codePath = (new GuessCodePath())->__invoke($path) ?? $path;

        $directoryXml   = sprintf(self::DIRECTORY_XML, $codePath);
        $ignoreFilesXml = $vendorPath !== null ? sprintf(self::IGNORE_FILES_XML, $vendorPath) : '';

        $projectFilesXml = str_replace(
            ['{{ directoryXml }}', '{{ ignoreFilesXml }}'],
            [$directoryXml, $ignoreFilesXml],
            self::PROJECT_FILES_XML
        );

        $configXml = file_get_contents($templatePath);
        $configXml = str_replace('{{ projectFilesXml }}', $projectFilesXml, $configXml);

        file_put_contents($tmpPath, $configXml);

        return $tmpPath;
    }
}
