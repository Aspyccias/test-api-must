<?php


namespace App\Transformers;


use App\Entity\User;
use Doctrine\ORM\PersistentCollection;

class UserTransformer
{
    /**
     * @param User $user
     * @return array
     */
    private function userToArray(User $user)
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getUserName(),
            'login' => $user->getLogin(),
        ];
    }

    /**
     * @param User|PersistentCollection $users
     * @return array
     */
    public function transform($users)
    {
        if (!is_countable($users)) {
            return $this->userToArray($users);
        }

        $usersArray = [];
        foreach ($users as $brand) {
            $usersArray[] = $this->userToArray($brand);
        }

        return $usersArray;
    }
}
