<?php

namespace Akari_my\FriendsX\util;

use Akari_my\FriendsX\Main;
use pocketmine\player\Player;

class Functions{

    public static function getMaxFriendsFor(Player $player): int {
        $plugin = Main::getInstance();
        $config = $plugin->getConfig();

        $max = (int)$config->get("default-friend-limit", 50);
        $permLimits = $config->get("friend-limits", []);
        if (is_array($permLimits)) {
            foreach ($permLimits as $permission => $limit) {
                if ($player->hasPermission((string)$permission)) {
                    $max = max($max, (int)$limit);
                }
            }
        }

        return $max;
    }

    public static function getPlayerByName(string $name): ?Player {
        $server = Main::getInstance()->getServer();
        foreach ($server->getOnlinePlayers() as $player) {
            if (strcasecmp($player->getName(), $name) === 0) {
                return $player;
            }
        }
        return null;
    }
}