<?php

namespace Akari_my\FriendsX\data;

interface DataProvider{

    public function load(): void;
    public function save(): void;
    public function getFriends(string $player): array;
    public function setFriends(string $player, array $friends): void;

}