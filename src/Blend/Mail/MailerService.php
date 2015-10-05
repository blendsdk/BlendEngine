<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Mail;

use Blend\Core\Application;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * MailerService for BlendEngine
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class MailerService {

    private $in_use;
    private $initialized;

    /**
     * @var Application
     */
    private $application;

    /**
     * @var \Swift_Events_SimpleEventDispatcher
     */
    private $eventDispatcher;

    /**
     * @var \Swift_MemorySpool
     */
    private $memorySpool;

    /**
     * @var \Swift_Transport_SpoolTransport
     */
    private $spoolTransport;

    /**
     * @var \Swift_Transport_EsmtpTransport
     */
    private $smtpTransport;

    /**
     * @var \Swift_Transport_Esmtp_AuthHandler
     */
    private $smtp_auth_handler;

    /**
     * @var \Swift_Transport_StreamBuffer
     */
    private $transport_buffer;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @return \Swift_Mailer
     */
    public function getMailer() {
        $this->in_use = true;
        if (!$this->initialized) {
            $this->initialize();
        }
        return $this->mailer;
    }

    public function __construct(Application $application) {
        $this->in_use = false;
        $this->initialized = false;
        $this->application = $application;
        $this->application->getDispatcher()->addListener(KernelEvents::TERMINATE, array($this, 'flushEmails'));
    }

    private function initialize() {
        $this->initialized = true;
        $this->eventDispatcher = new \Swift_Events_SimpleEventDispatcher();
        $this->memorySpool = new \Swift_MemorySpool();
        $this->spoolTransport = new \Swift_Transport_SpoolTransport($this->eventDispatcher, $this->memorySpool);
        $this->smtp_auth_handler = new \Swift_Transport_Esmtp_AuthHandler(array(
            new \Swift_Transport_Esmtp_Auth_CramMd5Authenticator(),
            new \Swift_Transport_Esmtp_Auth_LoginAuthenticator(),
            new \Swift_Transport_Esmtp_Auth_PlainAuthenticator(),
        ));
        $this->transport_buffer = new \Swift_Transport_StreamBuffer(new \Swift_StreamFilters_StringReplacementFilterFactory());
        $this->smtpTransport = new \Swift_Transport_EsmtpTransport($this->transport_buffer, array($this->smtp_auth_handler), $this->eventDispatcher);

        $options = array(
            'host' => $this->application->getConfig('email.host', 'localhost'),
            'port' => $this->application->getConfig('email.port', 25),
            'username' => $this->application->getConfig('email.username', null),
            'password' => $this->application->getConfig('email.password', null),
            'encryption' => $this->application->getConfig('email.encryption', null),
            'auth_mode' => $this->application->getConfig('email.auth_mode', null),
        );

        $this->smtpTransport->setHost($options['host']);
        $this->smtpTransport->setPort($options['port']);
        $this->smtpTransport->setEncryption($options['encryption']);
        $this->smtpTransport->setUsername($options['username']);
        $this->smtpTransport->setPassword($options['password']);
        $this->smtpTransport->setAuthMode($options['auth_mode']);

        $this->mailer = new \Swift_Mailer($this->spoolTransport);
    }

    public function flushEmails() {
        if ($this->in_use) {
            $this->spoolTransport->getSpool()->flushQueue($this->smtpTransport);
        }
    }

}
