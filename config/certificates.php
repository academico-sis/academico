<?php

/*
 * Certificate settings. These are defaults: the values saved in the Settings area
 * override them (see App\Services\Settings).
 */
return [
    'enabled' => false,

    /*
     * Title and text of the certificate. When empty, a translated default is used.
     * The text can contain the placeholders listed in App\Services\CertificateService::PLACEHOLDERS.
     */
    'title' => null,
    'body' => null,

    /*
     * Place printed before the date.
     */
    'city' => null,

    'signer_name' => null,
    'signer_function' => null,

    /*
     * Paths of the signature and background images on the local (private) disk.
     */
    'signature' => null,
    'background' => null,
];
