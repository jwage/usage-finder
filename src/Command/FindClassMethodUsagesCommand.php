<?php

declare(strict_types=1);

namespace UsageFinder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use UsageFinder\ClassMethodReference;
use UsageFinder\ClassMethodUsage;
use UsageFinder\FindClassMethodUsages;
use function assert;
use function count;
use function is_string;
use function sprintf;

final class FindClassMethodUsagesCommand extends Command
{
    protected function configure() : void
    {
        $this
            ->setName('find')
            ->setDescription('Find class method usages in the given path.')
            ->addArgument('path', InputArgument::REQUIRED, 'The path to search in.')
            ->addArgument('find', InputArgument::REQUIRED, 'What to search for.')
            ->addOption('threads', 't', InputOption::VALUE_REQUIRED, 'How many threads to run psalm with.', 1);
    }

    public function execute(InputInterface $input, OutputInterface $output) : void
    {
        $path = $input->getArgument('path');
        assert(is_string($path));

        $find = $input->getArgument('find');
        assert(is_string($find));

        $threads = (int) $input->getOption('threads');

        $classMethodReference = new ClassMethodReference($find);

        $output->writeln(sprintf(
            'Searching for <info>%s</info> in <info>%s</info>.',
            $classMethodReference->getName(),
            $path
        ));
        $output->writeln('');

        $stopwatch = new Stopwatch(true);
        $stopwatch->start('usage-finder');

        $classMethodUsages = (new FindClassMethodUsages())->__invoke(
            $path,
            $classMethodReference,
            $threads
        );

        $this->outputClassMethodUsages($classMethodUsages, $output);

        $event = $stopwatch->stop('usage-finder');

        $output->writeln([
            sprintf('Finished in <info>%sms</info>', $event->getDuration()),
            '',
        ]);
    }

    /**
     * @param array<int, ClassMethodUsage> $classMethodUsages
     */
    private function outputClassMethodUsages(array $classMethodUsages, OutputInterface $output) : void
    {
        if (count($classMethodUsages) > 0) {
            foreach ($classMethodUsages as $classMethodUsage) {
                $output->writeln(sprintf(
                    '  Found usage in <info>%s</info> on line <info>%d</info>.',
                    $classMethodUsage->getFile(),
                    $classMethodUsage->getLine()
                ));

                $output->writeln($classMethodUsage->getConsoleSnippet());
                $output->writeln('');
            }
        } else {
            $output->writeln('Could not find any usages.');
        }
    }
}
