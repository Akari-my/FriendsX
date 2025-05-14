<?php

namespace Akari_my\FriendsX\command;

use pocketmine\command\CommandSender;

interface SubCommand{
    public function execute(CommandSender $sender, array $args): void;
}