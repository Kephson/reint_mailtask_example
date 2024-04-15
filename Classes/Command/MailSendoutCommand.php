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
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Exception as CoreException;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\Exception as ExtbaseException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextFactory;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class MailSendoutCommand extends Command
{
    /*
     * array for default options
     * @var array
     */
    protected array $defaultConfig = [
        'extKey' => 'reint_mailtask_example',
        'lFilePath' => 'LLL:EXT:reint_mailtask_example/Resources/Private/Language/locallang.xlf:',
    ];

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
        $link = $input->getArgument('link');
        $transLang = $input->getArgument('trans-lang');
        $receiver_email = $input->getArgument('receiver-email');
        $receiver_name = $input->getArgument('receiver-name');
        $sender_email = $input->getArgument('sender-email');
        $sender_name = $input->getArgument('sender-name');
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
     * @param int $rootpage_id
     * @return bool
     * @throws ExtbaseException
     * @throws IllegalObjectTypeException
     * @throws InvalidQueryException
     * @throws UnknownObjectException
     * @throws CoreException
     */
    public function runTask(SymfonyStyle $io, string $link, string $transLang, string $receiver_email, string $receiver_name, string $sender_email, string $sender_name, int $rootpage_id): bool
    {
        $this->defaultConfig['link'] = $link;
        $this->defaultConfig['rootpageId'] = $rootpage_id;
        $this->defaultConfig['transLanguage'] = $transLang;
        $receiver = [$receiver_email => $receiver_name];
        $sender = [$sender_email => $sender_name];

        $subject = LocalizationUtility::translate($this->defaultConfig['lFilePath'] . 'subject', $this->defaultConfig['extKey']);
        $body = $this->renderMailContent($rootpage_id, $transLang);
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
     * @param int $rootpage_id
     * @param string $transLang
     * @param string $templateName
     *
     * @return string
     * @throws SiteNotFoundException
     */
    protected function renderMailContent(int $rootpage_id, string $transLang = 'en', string $templateName = 'Example'): string
    {
        $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByRootPageId($rootpage_id);
        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $request = (new ServerRequest())
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withAttribute('site', $site)
            ->withAttribute('language', $transLang);
        $contentObject->setRequest($request);
        /** @var StandaloneView $view */
        $view = GeneralUtility::makeInstance(StandaloneView::class, $contentObject);
        //$view->
        //$view->setRequest($request);

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
