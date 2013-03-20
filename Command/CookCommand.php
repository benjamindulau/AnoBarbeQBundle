<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\Command;

use BarbeQ\Model\Message;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CookCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ano:barbeq:cook')
            ->setDescription('Create a new message')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Queue to consume messages from.')
            ->addOption('body', null, InputOption::VALUE_REQUIRED, 'Message body')
            ->addOption('priority', null, InputOption::VALUE_OPTIONAL, 'Message priority', 0)
            ->setHelp(<<<EOT
The <info>%command.name%</info> command append a message in the given queue:

<info>php %command.full_name% --queue=encode_video --body=test</info>

To set a priority for this message, use the
<info>--priority</info> option:

<info>php %command.full_name% --queue=encode_video --body=test --priority=100</info>

EOT
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $barbeQ = $this->getBarbeQ();

        $barbeQ->cook($input->getOption('queue'), new Message(
            json_decode($input->getOption('body'), true),
            $input->getOption('priority')
        ));

        $output->writeln(sprintf(
            '<comment>%s</comment> <info>[message+]</info> Queue: <comment>%s</comment>',
            date('H:i:s'),
            $input->getOption('queue')
        ));
    }

    /**
     * @return \BarbeQ\BarbeQ
     */
    public function getBarbeQ()
    {
        return $this->getContainer()->get('ano_barbeq.barbeq');
    }
}
