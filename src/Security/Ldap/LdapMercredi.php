<?php

namespace AcMarche\Mercredi\Security\Ldap;

use Symfony\Component\Ldap\Adapter\EntryManagerInterface;
use Symfony\Component\Ldap\Adapter\QueryInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\Exception\LdapException;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LdapMercredi implements LdapInterface
{
    private Ldap $ldap;
    private string $dn;
    private string $user;
    private string $password;

    public function __construct(string $host, string $dn, string $user, string $password)
    {
        $this->ldap = Ldap::create(
            'ext_ldap',
            [
                'host' => $host,
                'encryption' => 'ssl',
            ]
        );

        $this->user = $user;
        $this->password = $password;
        $this->dn = $dn;
    }

    public function getEntry(string $uid): ?Entry
    {
        $this->ldap->bind($this->user, $this->password);
        $filter = "(&(|(sAMAccountName=*$uid*))(objectClass=person))";
        $query = $this->ldap->query($this->dn, $filter, ['maxItems' => 1]);
        $results = $query->execute();

        if ($results->count() > 0) {
            return $results[0];
        }

        return null;
    }

    /**
     * @throws LdapException
     */
    public function bind2(string $user, string $password): void
    {
        try {
            $this->ldap->bind($user, $password);
        } catch (\Exception $exception) {
            throw new BadCredentialsException($exception->getMessage());
        }
    }

    public function getEntryManager(): EntryManagerInterface
    {
        return $this->ldap->getEntryManager();
    }

    public function bind(string $dn = null, string $password = null)
    {
        dd($dn);
        // TODO: Implement bind() method.
    }

    public function query(string $dn, string $query, array $options = [])
    {
        dd($query);
        // TODO: Implement query() method.
    }

    public function escape(string $subject, string $ignore = '', int $flags = 0)
    {
        // TODO: Implement escape() method.
    }
}
