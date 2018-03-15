<?php

namespace IMAP;

use IMAP\IMAP;
use IMAP\IMAPAttachment;

Class IMAPAttachments extends \ArrayObject
{

    private $message;
    private $IS_INLINE_ONLY;

    public function __construct(IMAPMessage $message, $IS_INLINE_ONLY)
    {
        $this->IS_INLINE_ONLY = $IS_INLINE_ONLY;
        $array = $this->setMessage($message);
        parent::__construct($array);
    }

    private function setMessage(IMAPMessage $message) {
        $this->message = $message;
        return $this->getFiles($this->message->fetchParts());
    }

    private function getFiles($parts) {
        $files = array();
        foreach($parts as $section => $part) {
            if (!$part->ifdisposition) continue;
            if (!isset($part->id)) continue;
            if ($this->IS_INLINE_ONLY) if ($part->disposition !== 'inline') continue;

            $file = new \stdClass;
            $file->section = $section;
            $file->encoding = $part->encoding;
            $file->disposition = $part->disposition;
            $file->id = str_replace(['<','>'], '', $part->id);
            $file->filename = NULL;
            $file->name = NULL;
            $file->size = $part->bytes;
            $file->isAttachment = FALSE;

            $part->ifdparameters
                && ($file->filename = IMAP::getAttribute($part->dparameters, 'filename'))
                && $file->isAttachment = TRUE;
            $part->ifparameters
                && ($file->name = IMAP::getAttribute($part->parameters, 'name'))
                && $file->isAttachment = TRUE;
            $file->isAttachment
                && $files[] = new IMAPAttachment($this->message, $file);
        }
        return $files;
    }

}