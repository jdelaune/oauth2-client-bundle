# OAuth2 Client Bundle

OAuth2 Client Bundle for Symfony 2.

## Overview

Allow for the protection of resources via OAuth2. Provides two Symfony firewalls. One for checking bearer access tokens for securing API application. The access tokens can be provided via a header (recommended) or query e.g. `Authorization: Bearer {Access Token}` or `http://example.com/resource?access_token={Access Token}`. The other firewall is for securing web applications via the authorization code grant type.

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

You'll need add your OAuth2 Server URIs as parameters to your `parameters.yml`

``` yaml
# app/config/parameters.yml

parameters:
    oauth2.client.server:
        authorize_uri: 'http://example.com/authorize'
        token_uri: 'https://example.com/token'
        verify_uri: 'https://example.com/verify-token'
        validate_ssl: true
```

The verify uri should verify the access token on your OAuth2 Server and provide a JSON encoded array of:

- `access_token`
- `client_id`
- `expires_in`
- `user_id` (Optional)
- `scope` (Optional)

### Step 4a: Configure security (access token)

Access token only firewall is most often used for securing APIs where the end user won't actually be interacting with your Symfony application directly.

You'll need to setup a firewall in your `security.yml`

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
            pattern: ^/secured_area/
            oauth2_access_token: true
            stateless: true
```

### Step 4b: Configure security (authorization code)

Authorization code firewall is most often used when the end user is interacting with your Symfony application.

You'll need to setup a firewall in your `security.yml`

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
            pattern: ^/secured_area/
            oauth2_authorization_code:
                client_id: ~
                client_secret: ~
                redirect_uri: http://www.example.com/secured_area/authorized
                scope: basic
```

The `redirect_uri` needs to be a URI behind the same firewall. You can use all the usual configuration options here as well that one would use for the form firewall like `use_referer` and `default_target_path`.

### Step 5: Add routing

We provide default routing for some paths needed when using the authorization code firewall. Add this to your `routing.yml`

``` yaml
# app/config/routing.yml

oauth2_client:
    resource: "@OAuth2ClientBundle/Controller/"
    type:     annotation
    prefix:   /
```

## The OAuth2Token

The client bundle will provide an `OAuth2Token` object for any secured path in your controllers.

There are additional getters available on the `OAuth2User` object:

``` php
$token = $this->get('security.context')->getToken();
$token->getAccessToken(); // The access token
$token->getRefreshToken(); // The refresh token
$token->getExpiresAt(); // Expiry datetime object
$token->getExpiresIn(); // Seconds until the access token expires
```

## The OAuth2User

The client bundle will provide an `OAuth2User` object for any secured path in your controllers.

Scopes will be turned into roles automatically, e.g. a scope of `email` would result in a role of `ROLE_EMAIL`.

There are additional getters available on the `OAuth2User` object:

``` php
$user = $this->getUser();
$user->getClientId(); // Client ID
$user->getUserId(); // User ID
$user->isUser(); // True if user, false if client only
$user->getUsername(); // Client ID if client only, or User ID if user
$user->getScopes(); // Array of scopes
$user->getAccessToken(); // The access token
```