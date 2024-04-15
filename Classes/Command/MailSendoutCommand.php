<?php

declare(strict_types=1);

namespace RENOLIT\ReintMailtaskExample\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Exception as CoreException;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception as ExtbaseException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class MailSendoutCommand extends Command
{
    /*
     * array for default options
     * @var array
     */
    protected $defaultConfig = array(
        'extKey' => 'reint_mailtask_example',
        'lFilePath' => 'LLL:EXT:reint_mailtask_example/Resources/Private/Language/locallang.xlf:',
    );

    protected function configure(): void
    {
        $this
            ->setHelp('An example scheduler task with mail send out with Fluid-templates and multilanguage support.')
            ->addArgument(
                'link',
                InputArgument::REQUIRED,
                'http-link or page id'
            )
            ->addArgument(
                'trans-lang',
                InputArgument::REQUIRED,
                'Language of mail sendout (ISO-2, e.g. en, de)'
            )
            ->addArgument(
                'receiver-email',
                InputArgument::REQUIRED,
                'Email address of receiver'
            )
            ->addArgument(
                'receiver-name',
                InputArgument::REQUIRED,
                'Name of receiver'
            )
            ->addArgument(
                'sender-email',
                InputArgument::REQUIRED,
                'Email address of sender'
            )
            ->addArgument(
                'sender-name',
                InputArgument::REQUIRED,
                'Name of sender'
            )
            ->addArgument(
                'rootpage-id',
                InputArgument::REQUIRED,
                'Rootpage ID of page tree (default is 1)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $link = (int)$input->getArgument('link');
        $transLang = (int)$input->getArgument('trans-lang');
        $receiver_email = (int)$input->getArgument('receiver-email');
        $receiver_name = (int)$input->getArgument('receiver-name');
        $sender_email = (int)$input->getArgument('sender-email');
        $sender_name = (int)$input->getArgument('sender-name');
        $rootpage_id = (int)$input->getArgument('rootpage-id');
        $io = new SymfonyStyle($input, $output);
        try {
            $this->runTask($io, $link, $transLang, $receiver_email, $receiver_name, $sender_email, $sender_name, $rootpage_id);
        } catch (Exception) {
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }


    /**
     * @param SymfonyStyle $io
     * @param string $link
     * @param string $transLang
     * @param string $receiver_email
     * @param string $receiver_name
     * @param string $sender_email
     * @param string $sender_name
     * @param integer $rootpage_id
     * @return bool
     * @throws ExtbaseException
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     * @throws UnknownObjectException
     * @throws CoreException
     */
    public function runTask(SymfonyStyle $io, $link, $transLang, $receiver_email, $receiver_name, $sender_email, $sender_name, $rootpage_id): bool
    {
        $this->defaultConfig['link'] = $link;
        $this->defaultConfig['rootpageId'] = $rootpage_id;
        $this->defaultConfig['transLanguage'] = $transLang;
        $receiver = array($receiver_email => $receiver_name);
        $sender = array($sender_email => $sender_name);

        // change language if other language is set in scheduler task
        //$GLOBALS['LANG'] = GeneralUtility::makeInstance(\TYPO3\CMS\Lang\LanguageService::class);
        //$GLOBALS['LANG']->init($transLang);

        $subject = LocalizationUtility::translate($this->defaultConfig['lFilePath'] . 'subject', $this->defaultConfig['extKey']);
        $body = $this->renderMailContent();
        $mailSent = $this->sendMail($receiver, $sender, $subject, $body);

        if ($mailSent) {
            $title = 'Emails successfully sent';
            $message = 'Successfully send the email with its content.';
        } else {
            $title = 'Emails not sent';
            $message = 'Could not send the email, please check the log.';
        }

        $isCli = Environment::isCli();
        if ($isCli) {
            $io->section($title);
            $io->success($message);
        } else {
            /** @var $messageOut FlashMessage */
            $messageOut = GeneralUtility::makeInstance(
                FlashMessage::class,
                $message,
                $title,
                AbstractMessage::INFO,
                false
            );
            /* get backend message queue */
            /** @var $flashMessageService FlashMessageService */
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
            $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
            /* add message */
            $flashMessageQueue->enqueue($messageOut);
        }

        return $mailSent;
    }

    /**
     * renders a fluid mail template
     *
     * @param string $templateName
     *
     * @return string
     */
    protected function renderMailContent($templateName = 'Example'): string
    {
        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->getRequest()->setControllerExtensionName($this->defaultConfig['extensionName']);
        $view->setPartialRootPaths(
            [10 => ExtensionManagementUtility::extPath($this->defaultConfig['extKey']) . 'Resources/Private/Partials/']
        );
        $view->setLayoutRootPaths(
            [10 => ExtensionManagementUtility::extPath($this->defaultConfig['extKey']) . 'Resources/Private/Layouts/']
        );
        $templatePathAndFilename = ExtensionManagementUtility::extPath(
                $this->defaultConfig['extKey']
            ) . 'Resources/Private/Templates/Mail/' . $templateName . '.html';
        $view->setTemplatePathAndFilename($templatePathAndFilename);
        $view->assign('config', $this->defaultConfig);
        return $view->render();
    }

    /**
     * sends an email
     *
     * @param array $receiver Array with receiver
     * @param array $sender Array with sender
     * @param string $subject Subject of mail
     * @param string $body Body content for mail
     * @param string $attachment Path to a file
     *
     * return boolean
     */
    protected function sendMail(array $receiver = [], array $sender = [], string $subject = '', string $body = '', string $attachment = ''): bool
    {

        /** @var $mail MailMessage */
        $mail = GeneralUtility::makeInstance(MailMessage::class);

        // add attachment
        if ($attachment !== '') {
            if (is_file($attachment)) {
                $mail->attachFromPath($attachment);
            }
        }

        if (!empty($receiver) && !empty($sender)) {
            return $mail->setFrom($sender)
                ->setTo($receiver)
                ->setSubject($subject)
                ->html($body)
                ->send();
        } else {
            return false;
        }
    }

}