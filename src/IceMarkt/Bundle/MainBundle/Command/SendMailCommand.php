<?php

namespace IceMarkt\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipient;
use IceMarkt\Bundle\MainBundle\Entity\MailRecipientCollection;

class SendMailCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('Main:SendMail')
            ->setDescription('Send Mail from CSV files');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //TODO get email type (for the subject from address etc)

        $doctrine   = $this->getContainer()->get('doctrine');
        $repository = $doctrine->getRepository('IceMarktMainBundle:MailRecipient');
        $recipients = $repository->findAll();

        foreach ($recipients as $recipient) {

            $body = $this->getContainer()->get('templating')->render(
                'IceMarktMainBundle:Email:email.html.twig',
                array(
                    'name'  => $recipient->getName()
                )
            );

            $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom('test@test.com')
                ->setTo($recipient->getEmailAddress())
                ->setBody($body)
                ->setContentType("text/html");

            $this->getContainer()
                ->get('mailer')
                ->send($message);

            //TODO save/log that the message was sent
            $output->writeln('Sent email to ' . $recipient->getEmailAddress());
        }

        $output->writeln('Message Sent');
    }

}
