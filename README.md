# OAuth2 Client Bundle

OAuth2 Client Bundle for Symfony 2.

## Overview

Allow for the protection of resources via OAuth2. Currently only supports Bearer Access Tokens. The access tokens can be provided via a header (recommended) or query e.g. `Authorization: Bearer {Access Token}` or `http://example.com/resource?access_token={Access Token}`.

## Installation

### Step 1: Add package to Composer

Add the bundle to your composer.json:

``` js
{
    "require": {
        "jdelaune/oauth2-client-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update jdelaune/oauth2-client-bundle
```

Composer will install the bundle to your project's `vendor/jdelaune` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new OAuth2\ClientBundle\OAuth2ClientBundle(),
    );
}
```

### Step 3: Add parameters

You'll need to two parameters to your parameters.yml

``` yaml
# app/config/parameters.yml

parameters:
    oauth2.client.server.uri:           https://example.com
    oauth2.client.server.verify_path:   '/verify'
```

The verify path should verify the access token on your OAuth2 Server and provide a JSON encoded array of:

- Access Token
- Client ID
- Expires (Unix Timestamp)
- User ID (Optional)
- Scope (Optional)

### Step 4: Configure security

You'll need to setup a firewall in your security.yml

``` yaml
# app/config/security.yml

security:
    encoders:
        OAuth2\ClientBundle\Security\User\OAuth2User: plaintext

    providers:
        oauth2_client:
            id: oauth2.client.user_provider

    firewalls:
        oauth2_secured:
            pattern:    ^/*
            stateless:  true
            oauth2:     true
```

## The OAuth2User

The client bundle will provide an `OAuth2User` object for any secured path in your controllers.

Scopes will be turned into roles automatically, e.g. a scope of `email` would result in a role of `ROLE_EMAIL`.

There are additional getters available on the `OAuth2User` object:

``` php
$user = $this->getUser();
$user->getClientId(); // Client ID
$user->getUserId(); // User ID
$user->getType(); // Client or User
$user->getUsername(); // Client ID if type Client, or UserID if type User
$user->getScopes(); // Array of scopes
$user->getExpires(); // Expiry datetime object
```