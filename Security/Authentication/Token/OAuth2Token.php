<?php

namespace OAuth2\ClientBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OAuth2Token extends AbstractToken
{
    private $access_token;
    private $refresh_token;
    private $expires_at;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);

        // If the user has roles, consider it authenticated
        $this->setAuthenticated(count($roles) > 0);
    }

    public function getCredentials()
    {
        return null;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
        return $this;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }

    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
        return $this;
    }

    public function getRefreshToken()
    {
        return $this->refresh_token;
    }

    public function setExpiresIn($expires_in)
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $now->modify('+' . $expires_in . ' seconds');
        $this->expires_at = $now;
        return $this;
    }

    public function getExpiresIn()
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $interval = $now->diff($this->expires_at);
        $difference = $interval->format('s');
        return ($difference < 0) ? 0 : $difference;
    }

    public function setExpiresAt($expires_at)
    {
        $this->expires_at = $expires_at;
        return $this;
    }

    public function getExpiresAt()
    {
        return $this->expires_at;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->access_token, $this->refresh_token, $this->expires_at, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->access_token, $this->refresh_token, $this->expires_at, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
