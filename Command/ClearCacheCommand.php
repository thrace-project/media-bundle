<?php
/*
 * This file is part of ThraceMediaBundle
 *
 * (c) Nikolay Georgiev <symfonist@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace Thrace\MediaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for cleaning files in temporary directory
 *
 * @author Nikolay Georgiev <symfonist@gmail.com>
 * @since 1.0
 */
class ClearCacheCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('thrace:media:cache-clear')
            ->setDescription('Remove cached images and files in temporary directory')
            ->addOption('maxAge', null, InputOption::VALUE_OPTIONAL, 'Max age in seconds', 7200)
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $num = $this->getContainer()->get('thrace_media.filemanager')->clearCache($input->getOption('maxAge'));
        
        $output->writeln('<info>' . sprintf('%s files successfully removed', $num) . '</info>');
    }
}
