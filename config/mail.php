<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mail Driver
    |--------------------------------------------------------------------------
    |
    | Laravel supports both SMTP and PHP's "mail" function as drivers for the
    | sending of e-mail. You may specify which one you're using throughout
    | your application here. By default, Laravel is setup for SMTP mail.
    |
    | Supported: "smtp", "mail", "sendmail", "mailgun", "mandrill",
    |            "ses", "sparkpost", "log"
    |
    */

    'driver' => env('MAIL_DRIVER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Address
    |--------------------------------------------------------------------------
    |
    | Here you may provide the host address of the SMTP server used by your
    | applications. A default option is provided that is compatible with
    | the Mailgun mail service which will provide reliable deliveries.
    |
    */

    'host' => env('MAIL_HOST', 'smtp.mailgun.org'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Host Port
    |--------------------------------------------------------------------------
    |
    | This is the SMTP port used by your application to deliver e-mails to
    | users of the application. Like the host we have set this value to
    | stay compatible with the Mailgun e-mail application by default.
    |
    */

    'port' => env('MAIL_PORT', 587),

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => ['address' => null, 'name' => null],

    /*
    |--------------------------------------------------------------------------
    | E-Mail Encryption Protocol
    |--------------------------------------------------------------------------
    |
    | Here you may specify the encryption protocol that should be used when
    | the application send e-mail messages. A sensible default using the
    | transport layer security protocol should provide great security.
    |
    */

    'encryption' => env('MAIL_ENCRYPTION', 'tls'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Username
    |--------------------------------------------------------------------------
    |
    | If your SMTP server requires a username for authentication, you should
    | set it here. This will get used to authenticate with your server on
    | connection. You may also set the "password" value below this one.
    |
    */

    'username' => env('MAIL_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | SMTP Server Password
    |--------------------------------------------------------------------------
    |
    | Here you may set the password required by your SMTP server to send out
    | messages from your application. This will be given to the server on
    | connection so that the application will be able to send messages.
    |
    */

    'password' => env('MAIL_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Sendmail System Path
    |--------------------------------------------------------------------------
    |
    | When using the "sendmail" driver to send e-mails, we will need to know
    | the path to where Sendmail lives on this server. A default path has
    | been provided here, which will work well on most of your systems.
    |
    */
    'sendmail' => '/usr/sbin/sendmail -bs',
    "mailFrom" => array("sender" => "system@boom.tv", "sendername" => "Boom.tv"),
    'sendMailForVideoMontage' => array("sender" => "system@boom.tv",
        "subject" => "Here is the montage you requested", "sendername" => "Boom.tv"
    ),
    "sendMailLog" => array("sendTo" => "supportgroup@boom.tv",
        "subject" => "There is a new log file"),
    "sendMailJira" => array("sendTo" => "jira@afkvrgg.atlassian.net",
        "subject" => "Issue from log file, streamer : ", "emailCc" => "supportgroup@boom.tv"),
    "sendMailContact" => array("sendTo" => "team@boom.tv",
        "subject" => "New contact"),
    /*
     * alert mail config
     */
    "sendMailAlert" => array("sendTo" => array('jira@afkvrgg.atlassian.net'), "emailCc" => array('chris.luong@boom.tv', 'trung.ht@boom.tv', 'sumit@boom.tv')),
    "alertState" => env('ALERT_STATE', false),
    /*"list_send_moment_montage" => ["tan.nn@boom.tv", "xoan.nt@boom.tv","avi@boom.tv", "sumit@boom.tv"],*/
    "send_moment_montage_recipient" => [
        /*[
            'address' => [
                'name' => 'Trung HT',
                'email' => 'trung.ht@boom.tv',
            ],
        ],*/
    ],
    "list_send_moment_montage" => [
        [
            'address' => [
                'name' => 'BoomTvTeam',
                'email' => 'team@boom.tv',
            ],
        ],
        [
            'address' => [
                'name' => 'Trung HT',
                'email' => 'trung.ht@boom.tv',
            ],
        ],
    ],
    "list_send_moment_montage_beta" => [
        [
            'address' => [
                'name' => 'TrungHT',
                'email' => 'trung.ht@boom.tv',
            ],
        ]
    ],

    "template_id_moment_monatge" => "test-2",
    "sendMailForVideoMomentMontage" => array("sender" => "team@boom.tv",
        "subject" => "Here is the montage you requested", "sendername" => "Boom.tv"),
];
