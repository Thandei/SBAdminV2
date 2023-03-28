<?php

namespace App\Security\Voter;

use App\Entity\Account;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountVoter extends Voter
{
    public function __construct(public Security $security)
    {
    }

    public const SHOW = 'SHOW';
    public const DELETE = 'DELETE';



    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::SHOW, self::DELETE])
            && $subject instanceof \App\Entity\Account;
    }

    /**
     * @param string $attribute
     * @param Account $account
     * @param TokenInterface $token
     * @return false
     */
    protected function voteOnAttribute(string $attribute, $account, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $accessIsGranted = match ($attribute) {
            "SHOW" => $this->show($account, $user),
            "DELETE" => $this->security->isGranted("ROLE_ADMIN")
        };

        return $accessIsGranted;
    }

    private function show($account, $user): bool
    {
        return $account->getAccountHolder() == $user
            || $account->getAccountManager() == $user
            || $this->security->isGranted("ROLE_ADMIN");
    }
}
