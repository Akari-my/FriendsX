<?php

namespace Akari_my\FriendsX\listener;

use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class PlayerJoinListener implements Listener{

    public function __construct(private Main $plugin) {}

    public function onJoin(PlayerJoinEvent $event): void {
        $joinedName = strtolower($event->getPlayer()->getName());

        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $check = strtolower($player->getName());
            if ($check === $joinedName) continue;

            $friends = $this->plugin->getFriendsManager()->getFriends($check);
            if (in_array($joinedName, $friends)) {
                $player->sendMessage(LangManager::get("friend-joined", ["name" => $event->getPlayer()->getName()]));
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event): void {
        $quitName = strtolower($event->getPlayer()->getName());

        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $check = strtolower($player->getName());
            if ($check === $quitName) continue;

            $friends = $this->plugin->getFriendsManager()->getFriends($check);
            if (in_array($quitName, $friends)) {
                $player->sendMessage(LangManager::get("friend-left", [
                    "name" => $event->getPlayer()->getName()
                ]));
            }
        }
    }
}