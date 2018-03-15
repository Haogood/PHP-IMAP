<?php

namespace IMAP;

use IMAP\IMAP;

Class IMAPAttachment
{

    private $message;
    private $attachment;

    public function __construct(IMAPMessage $message, $attachment) {
        $this->message = $message;
        $this->attachment = $attachment;
    }

    public function getInfo() {
        return $this->attachment;
    }

    public function ID() {
        return $this->attachment->id;
    }

    public function getBody() {
        $body = $this->message->fetchBody($this->attachment->section);
        return IMAP::Decode($body, $this->attachment->encoding);
    }

    public function getFilename() {
        $filename = $this->attachment->filename;
        NULL === $filename && $filename = $this->attachment->name;
        return $filename;
    }

    public function getExtension() {
        return pathinfo($this->attachment->filename, PATHINFO_EXTENSION);
    }

}