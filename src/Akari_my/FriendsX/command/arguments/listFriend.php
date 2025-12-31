<?php

namespace Akari_my\FriendsX\command\arguments;

use Akari_my\FriendsX\command\SubCommand;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\util\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class listFriend implements SubCommand {

    public function execute(CommandSender $sender, array $args): void {
        if (!$sender instanceof Player) {
            return;
        }

        $plugin = Main::getInstance();
        $playerName = $sender->getName();
        $player = strtolower($playerName);

        $manager = $plugin->getFriendsManager();
        $friends = $manager->getFriends($player);

        if (empty($friends)) {
            $sender->sendMessage(LangManager::get("no-friends"));
            return;
        }

        $formatted = [];
        $lastSeenManager = $plugin->getLastSeenManager();

        foreach ($friends as $friendName) {
            $onlinePlayer = $plugin->getPlayerByName($friendName);
            if ($onlinePlayer !== null && $onlinePlayer->isOnline()) {
                $displayName = $onlinePlayer->getName();
                $status = LangManager::raw("status-online");
                $formatted[] = "§7- §e" . $displayName . " §7(" . $status . "§7)";
            } else {
                $status = LangManager::raw("status-offline");
                $lastSeen = $lastSeenManager->getLastSeen($friendName);
                if ($lastSeen !== null) {
                    $ago = time() - $lastSeen;
                    $timeString = TimeUtils::formatDuration($ago);
                    $lastSeenText = LangManager::raw("last-seen", ["time" => $timeString]);
                    $status .= " §7- " . $lastSeenText;
                }
                $formatted[] = "§7- §e" . $friendName . " §7(" . $status . "§7)";
            }
        }

        $message = LangManager::get("friends-list", ["list" => implode("\n", $formatted)]);
        $sender->sendMessage($message);
    }
}