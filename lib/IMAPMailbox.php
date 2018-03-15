<?php

namespace IMAP;

use IMAP\IMAPMessage;

Class IMAPMailbox
{

    private $stream;

    public function __construct($hostname, $username, $password) {
        $stream = imap_open($hostname, $username, $password);
        if (FALSE === $stream) {
            throw new \Exception('Connect failed: ' . imap_last_error());
        }
        $this->stream = $stream;
    }

    public function getStream() {
        return $this->stream;
    }

    public function check() {
        $info = imap_check($this->stream);
        if (FALSE === $info) {
            throw new \Exception('Check failed: ' . imap_last_error());
        }
        return $info;
    }

    public function search($criteria) {
        $emails = imap_search($this->stream, $criteria);
        if (FALSE === $emails) {
            throw new \Exception('Search failed: No email or somthing ' . imap_last_error());
        }
        foreach ($emails as &$email) {
            $email = $this->getMessageByNumber($email);
        }
        return $emails;
    }

    public function getMessageByNumber($msgno) {
        return new IMAPMessage($this, $msgno);
    }

}