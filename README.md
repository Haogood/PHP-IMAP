
## Requirements
- PHP >= 5.5.0
- IMAP PHP Extension

## Installation
```
composer require haogood/php-imap
```

## Initialize
```php
use IMAP\IMAPMailbox;
$mailbox = new IMAPMailbox($host, $user, $pwd);
```

## Search
```php
$emails = $mailbox->search('ALL');
```

## Fetch header info
```php
foreach ($emails as $email) {

    // Header info
    $headerinfo = $email->fetchHeaderinfo();

    // Author
    $author = $headerinfo->from->personal;

    // Sender address
    $from = $headerinfo->from->mailbox.'@'.$headerinfo->from->host;

    // Timestamp
    $timstamp = $headerinfo->udate

    // Contents
    $contents = $email->getBody();

}
```

## Fetch attachments
```php
foreach ($emails as $email) {
    foreach($email->getAttachments() as $attachment) {

        // Filename
        $filename = $attachment->getFilename();

        // Extension
        $ext = $attachment->getExtension();

        // Attachment file
        $file = $attachment->getBody();

        // Attchment info
        $info = $attachment->getInfo();

    }
}
```

## Reference
- [hakre/imap-attachment.php](https://gist.github.com/hakre/2363305)
- [electrictoolbox](https://www.electrictoolbox.com/extract-attachments-email-php-imap/)

License
------------
`haogood/php-imap` is licensed under [The MIT License (MIT)](LICENSE).
