<?php
// config for Doinc\PersonaKyc
return [
    /*
     |--------------------------------------------------------------------------
     | Apy key
     |--------------------------------------------------------------------------
     |
     | Apy key used for authentication in Persona.
     | Registration is needed, https://withpersona.com
     |
     */
    "api_key" => env("PERSONA_API_KEY", null),

    /*
    |--------------------------------------------------------------------------
    | Available templates
    |--------------------------------------------------------------------------
    |
    | Associative array defining a known name and an associated value.
    | These values are used for the generation of the PersonaTemplates enum.
    | Changing a value in the following array means to trigger the update
    | command using artisan `persona:update`.
    | The creation of the enum will strongly improve your editor's intellicode
    | and the development experience
    |
    */
    "templates" => [
        "GOVERNMENT_ID" => "example_GOVERNMENT_ID_template_id",
        "GOVERNMENT_ID_AND_SELFIE" => "example_GOVERNMENT_ID_AND_SELFIE_template_id"
    ],

    /*
     |--------------------------------------------------------------------------
     | Webhook path prefix
     |--------------------------------------------------------------------------
     |
     | Prefix used for the generation of the webhook route.
     | The route will always be located at:
     | <webhook_prefix>/persona/hook
     |
     */
    "webhook_prefix" => env("PERSONA_WEBHOOK_PREFIX", ""),
];
