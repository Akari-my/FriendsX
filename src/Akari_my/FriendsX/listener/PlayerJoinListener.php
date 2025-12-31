<?php

namespace Akari_my\FriendsX\listener;

use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerJoinListener implements Listener {

    public function __construct(private Main $plugin) {}

    public function onJoin(PlayerJoinEvent $event): void {
        $joinedPlayer = $event->getPlayer();
        $joinedName = strtolower($joinedPlayer->getName());

        $settings = $this->plugin->getSettingsManager();

        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if ($player->getId() === $joinedPlayer->getId()) {
                continue;
            }

            $check = strtolower($player->getName());
            if (!$settings->canReceiveFriendNotifications($check)) {
                continue;
            }

            $friends = $this->plugin->getFriendsManager()->getFriends($check);
            if (in_array($joinedName, $friends, true)) {
                $player->sendMessage(LangManager::get("friend-joined", ["name" => $joinedPlayer->getName()]));
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $quitPlayer = $event->getPlayer();
        $quitName = strtolower($quitPlayer->getName());

        $this->plugin->getLastSeenManager()->setLastSeen($quitName, time());

        $settings = $this->plugin->getSettingsManager();

        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            if ($player->getId() === $quitPlayer->getId()) {
                continue;
            }

            $check = strtolower($player->getName());
            if (!$settings->canReceiveFriendNotifications($check)) {
                continue;
            }

            $friends = $this->plugin->getFriendsManager()->getFriends($check);
            if (in_array($quitName, $friends, true)) {
                $player->sendMessage(LangManager::get("friend-left", [
                    "name" => $quitPlayer->getName()
                ]));
            }
        }
    }
}