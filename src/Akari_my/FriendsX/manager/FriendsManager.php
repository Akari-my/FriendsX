<?php

declare(strict_types=1);

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\data\DataProvider;

class FriendsManager {

    public function __construct(private DataProvider $data) {
    }

    private function normalizeName(string $name): string {
        return strtolower(trim($name));
    }

    public function addFriend(string $player, string $friend): bool {
        $player = $this->normalizeName($player);
        $friend = $this->normalizeName($friend);

        // Prevent adding self
        if ($player === $friend) {
            return false;
        }

        $friends = $this->getFriends($player);
        if (in_array($friend, $friends, true)) {
            return false;
        }

        $friends[] = $friend;
        $this->data->setFriends($player, $friends);
        $this->data->save();
        return true;
    }

    public function removeFriend(string $player, string $friend): bool {
        $friends = $this->getFriends($player);
        if (!in_array($friend, $friends, true)) {
            return false;
        }

        $friends = array_values(array_diff($friends, [$friend]));
        $this->data->setFriends($player, $friends);
        $this->data->save();
        return true;
    }

    public function getFriends(string $player): array {
        $player = $this->normalizeName($player);
        return $this->data->getFriends($player);
    }

    public function getData(): DataProvider {
        return $this->data;
    }
}