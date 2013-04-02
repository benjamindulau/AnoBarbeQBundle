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
use BarbeQ\Model\MessageInterface;
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

        $self = $this;
        $barbeQ->addListener(BarbeQEvents::PRE_CONSUME, function(ConsumeEvent $event) use ($output, $self) {
            $self->onPreConsume($event, $output);
        });

        $barbeQ->addListener(BarbeQEvents::POST_CONSUME, function(ConsumeEvent $event) use ($output, $self) {
            $self->onPostConsume($event, $output);
        });

        $barbeQ->eat($input->getOption('queue'), $input->getOption('amount'));
    }

    protected function onPreConsume(ConsumeEvent $event, OutputInterface $output)
    {
        $message = $event->getMessage();

        $output->writeln(sprintf(
            '<comment>%s</comment> <info>[start]</info> Queue: <comment>%s</comment> - Message <comment>#%d</comment>',
            date('Y-m-d H:i:s'),
            $message->getQueue(),
            $message->getMetadataValue('index')
        ));
    }

    protected function onPostConsume(ConsumeEvent $event, OutputInterface $output)
    {
        $message = $event->getMessage();

        $outputMsg = '';
        if (MessageInterface::STATE_ERROR == $message->getState()) {
            $outputMsg = '<comment>%s</comment> <error>[error]</error>';
        } else {
            $outputMsg = '<comment>%s</comment> <info>[end]</info>';
        }
        $outputMsg .= ' Memory: <comment>%s</comment> - Time: <comment>%0.04fs</comment>';

        $output->writeln(sprintf(
            $outputMsg,
            date('Y-m-d H:i:s'),
            $message->getMemory(),
            $message->getTime()
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
