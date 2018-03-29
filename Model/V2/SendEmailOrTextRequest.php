<?php

namespace LuxSciApiClient_Model_V2;

use LuxSciApiClient_Model\BaseRequest;
use LuxSciApiClient_Model\BodyType;

/**
 * Send email request model
 * Class SendEmailRequest
 * @package LuxSciApiClient_Model
 */
class SendEmailOrTextRequest extends BaseRequest
{
    /**
     * Optional.  If included, this is an array of JSON objects, one for each uploaded file.
     * See the General API Usage Guide for how to upload files with your request.
     *
     * The individual JSON objects will have the following keywords and values:
     *  - name – The exact filename of the uploaded file
     *  - hash – The SHA256 hex-encoded hash of the content of this file.
     * @var array
     */
    public $attachments = [];

    /**
     * Optional. Array of email addresses for the recipients in the "Bcc" of the message.
     * @var array
     */
    public $bcc = [];

    /**
     * Required. The body content of this message.
     * This can be either a block of plain text (\n or \r\n – UNIX or Windows style linefeeds are recommended) or a block of HTML content.
     * For best results, HTML content should NOT be a completed HTML Document,
     * but what you would put inside <body> … </body> tags.
     * If you need to include a complete, arbitrary HTML document, we recommend uploading that as an attached file.
     * See the "body_type" parameter.
     * @var string
     */
    public $body;

    /**
     * Optional. If included, this must be "text" or "html".
     * If omitted, it takes on the value "text".
     * The body_type parameter indicates what content is being passed in the "body" field
     * @var string
     */
    public $body_type = BodyType::Html;

    /**
     * Optional. Array of email addresses for the recipients in the "Cc" of the message.
     * @var array
     */
    public $cc = [];

    /**
     * Optional. Email address to use as the "From address" for this message.
     * If omitted, the sending user's login email address will be used.
     * Note that it is against the terms of service to use an address here that does not belong to you or for which you do not otherwise have permission to send from.
     * @var string
     */
    public $from_address;

    /**
     * Optional. The plain text name of the sender.
     * If omitted, the sending user's "contact" name (from his/her profile) will be used.
     * If that is empty, then the "from_address" will be used.
     * The from name can be a maximum of 100 characters long.
     * @var string
     */
    public $from_name;

    /**
     * Optional.  If this is passed and is true (1), then no email messages to recipients will be allowed to be sent using "TLS Only"
     * – other encryption methods (e.g. Escrow) will be used.  SecureText messages are never sent via TLS only.
     * @var int 0|1
     */
    public $no_tls_only = 0;

    /**
     * Optional.  If set to "1", a read receipt will be requested. For SecureLine Escrow and SecureText,
     * these read receipts will always be sent back on the 1st time the message is viewed by each recipient …
     * the recipient has no say in the matter and no indication of if this is happening.
     * @var int 0|1
     */
    public $receipt = 0;

    /**
     *Optional. Email address to use as the "Reply-To address" for this message.
     *If omitted, the from address will be used. Note that it is against the terms of service to use an address
     *here that does not belong to you or for which you do not otherwise have permission to send from
     */
    public $reply_address;

    /**
     * Required.  Subject of this message.  This can be a maximum of 1000 characters long.
     * Leading and training spaces are automatically trimmed from this subject.
     * @var string
     */
    public $subject;

    /**
     * Optional. Array of email addresses for the recipients in the "To" of the message.
     * @var array
     */
    public $to;

}