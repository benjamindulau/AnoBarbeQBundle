<?php

/**
 * This file is part of AnoBarbeQBundle
 *
 * (c) anonymation <contact@anonymation.com>
 *
 */
namespace Ano\Bundle\BarbeQBundle\Command;

use BarbeQ\BarbeQEvents;
use BarbeQ\Event\ConsumeEvent;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EatCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ano:barbeq:eat')
            ->setDescription('Start consuming messages')
            ->addOption('queue', null, InputOption::VALUE_REQUIRED, 'Queue to consume messages from.')
            ->addOption('amount', null, InputOption::VALUE_OPTIONAL, 'Consume only n messages before stopping.', 0)
            ->setHelp(<<<EOT
The <info>%command.name%</info> command starts consuming of messages from a given queue:

<info>php %command.full_name% --queue=encode_video</info>

To only consume a limited amount of messages before gracefully stopping, use the
<info>--amount</info> option:

<info>php %command.full_name% --queue=encode_video --amount=10</info>

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

        $barbeQ->addListener(BarbeQEvents::PRE_CONSUME, function(ConsumeEvent $event) use ($output) {
            $message = $event->getMessage();

            $output->writeln(sprintf(
                '<comment>%s</comment> <info>[start]</info> Queue: <comment>%s</comment> - Message <comment>#%d</comment>',
                date('H:i:s'),
                $message->getQueue(),
                $message->getMetadataValue('index')
            ));
        });

        $barbeQ->addListener(BarbeQEvents::POST_CONSUME, function(ConsumeEvent $event) use ($output) {
            $message = $event->getMessage();
            $output->writeln(sprintf(
                '<comment>%s</comment> <info>[end]</info> Memory: <comment>%s</comment> - Time: <comment>%0.04fs</comment>',
                date('H:i:s'),
                $message->getMemory(),
                $message->getTime()
            ));
        });

        $barbeQ->eat($input->getOption('queue'), $input->getOption('amount'));
    }

    /**
     * @return \BarbeQ\BarbeQ
     */
    public function getBarbeQ()
    {
        return $this->getContainer()->get('ano_barbeq.barbeq');
    }
}
