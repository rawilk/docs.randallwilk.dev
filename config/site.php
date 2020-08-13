<?php

return [
    /*
     * The github repo for the docs site.
     */
    'github' => 'https://github.com/rawilk/docs.randallwilk.dev',

    /*
     * My github profile page url.
     */
    'github_profile' => 'https://github.com/rawilk',

    /*
     * Open source links url.
     */
    'open_source_url' => 'https://randallwilk.dev/projects',

    'git' => [
        /*
         * The access token needed to access the github repositories.
         */
        'github_access_token' => env('GITHUB_ACCESS_TOKEN'),
    ],

    /*
     * Directory where packages are being stored locally.
     */
    'packages_directory' => env('PACKAGES_DIRECTORY'),

    /*
     * Directory in packages where doc files live.
     */
    'default_package_docs_directory' => 'docs',

    'google_analytics_id' => env('GOOGLE_ANALYTICS_ID'),
];
