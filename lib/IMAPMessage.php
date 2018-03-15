<?php

namespace IMAP;

use IMAP\IMAP;
use IMAP\IMAPAttachments;

Class IMAPMessage
{

    private $stream;
    private $msgno;

    public function __construct(IMAPMailbox $mailbox, $msgno) {
        $this->stream = $mailbox->getStream();
        $this->msgno = $msgno;
    }

    public function getMsgno() {
        return $this->msgno;
    }

    public function getBody() {
        $parts = $this->fetchParts();
        $body = NULL;
        foreach ($parts as $section => $part) {
            if (!$part->ifdisposition && $part->subtype == 'HTML') {
                $body = $this->fetchBody($section);
                $charset = IMAP::getAttribute($part->parameters, 'charset');
                $encoding = $part->encoding;
                break;
            }
        }
        !(NULL === $body) && $body = IMAP::Decode($body, $encoding, $charset);
        return $body;
    }

    public function fetchParts() {
        $struct = $this->fetchStructure();
        if (isset($struct->parts)) {
            return $this->flatParts($struct->parts);
        }
        
        $result = array();
        $result[1] = $struct;

        return $result;
    }

    public function fetchBody($section) {
        return imap_fetchbody($this->stream, $this->msgno, $section);
    }

    public function fetchHeaderinfo() {
        $result = imap_headerinfo($this->stream, $this->msgno);
        if (FALSE === $result) {
            throw new Exception('FetchHeaderInfo failed: ' . imap_last_error());            
        }
        foreach ($result as &$prop) {
            if (!is_array($prop)) {
                $prop = imap_utf8($prop);
            } else {
                list($prop) = $prop;
                foreach($prop as &$sub) {
                    $sub = imap_utf8($sub);
                }
            }
        }
        return $result;
    }

    public function fetchOverview() {
        $result = imap_fetch_overview($this->stream, $this->msgno);
        if (FALSE === $result) {
            throw new Exception('FetchOverview failed: ' . imap_last_error());
        }
        list($result) = $result;
        foreach ($result as &$prop) {
            $prop = imap_utf8($prop);
        }
        return $result;
    }

    private function flatParts($parts, $flattened=array(), $prefix='', $index=1) {
        foreach ($parts as $part) {
            $flattened[$prefix.$index] = $part;
            if (isset($part->parts)) {
                $flattened = $this->flatParts($part->parts, $flattened, $prefix.$index.'.');
                unset($flattened[$prefix.$index]->parts);
            }
            $index++;
        }
        return $flattened;
    }

    public function fetchStructure() {
        return imap_fetchstructure($this->stream, $this->msgno);
    }

    public function getAttachments($IS_INLINE_ONLY = FALSE) {
        return new IMAPAttachments($this, $IS_INLINE_ONLY);
    }
}