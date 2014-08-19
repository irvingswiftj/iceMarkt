<?php

namespace IceMarkt\Bundle\MainBundle\Command;

use IceMarkt\Bundle\MainBundle\Entity\EmailTemplate;
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
            ->setDescription('Send Mail from CSV files')
            ->addArgument(
                'emailTemplateId',
                InputArgument::REQUIRED,
                'Which email template do you want to send'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        //TODO get email profile

        $doctrine                   = $this->getContainer()->get('doctrine');
        $mailRecipientRepository    = $doctrine->getRepository('IceMarktMainBundle:MailRecipient');
        $emailTemplateRepository    = $doctrine->getRepository('IceMarktMainBundle:EmailTemplate');
        $emailTemplate              = $emailTemplateRepository->findOneBy(array(
            'id' => $input->getArgument('emailTemplateId')
        ));
        $recipients                 = $mailRecipientRepository->findAll();

        foreach ($recipients as $recipient) {

            $twig = new \Twig_Environment(new \Twig_Loader_String());

            $body = $twig->render(
                $emailTemplate->getTemplate(),
                array(
                    'first_name'  => $recipient->getFirstName()
                )
            );

            $message = \Swift_Message::newInstance()
                ->setSubject($emailTemplate->getSubject())
                ->setFrom($emailTemplate->getEmailProfile()->getFromEmail())
                ->setTo($recipient->getEmailAddress())
                ->setBody($body)
                ->setContentType(EmailTemplate::$headers[$emailTemplate->getFormat()]);

            $this->getContainer()
                ->get('mailer')
                ->send($message);

            //TODO save/log that the message was sent
            $output->writeln('Sent email to ' . $recipient->getEmailAddress());
        }

        $output->writeln('Message Sent');
    }

}
