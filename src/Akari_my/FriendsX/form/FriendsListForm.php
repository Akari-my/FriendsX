<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\util\Functions;
use Akari_my\FriendsX\util\TimeUtils;
use pocketmine\player\Player;

class FriendsListForm {

    public static function open(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $friendsManager = $plugin->getFriendsManager();
        $lastSeenManager = $plugin->getLastSeenManager();

        $playerName = $player->getName();
        $friends = array_values($friendsManager->getFriends($playerName));
        $friendsCount = count($friends);

        $form = new SimpleForm(function (Player $player, ?int $data) use ($friends, $friendsCount): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            if ($friendsCount === 0) {
                if ($data === 0) {
                    MainForm::open($player);
                }
                return;
            }

            if ($data === $friendsCount) {
                MainForm::open($player);
                return;
            }

            if (!isset($friends[$data])) {
                return;
            }

            $target = $friends[$data];
            self::openActions($player, $target);
        });

        $form->setTitle(LangManager::raw("ui-friends-title"));

        if ($friendsCount === 0) {
            $form->setContent(LangManager::raw("ui-friends-empty"));
            $form->addButton(LangManager::raw("ui-button-back"));
        } else {
            $form->setContent(LangManager::raw("ui-friends-content"));
            foreach ($friends as $friendName) {
                $onlinePlayer = Functions::getPlayerByName($friendName);
                if ($onlinePlayer !== null && $onlinePlayer->isOnline()) {
                    $displayName = $onlinePlayer->getName();
                    $status = LangManager::raw("status-online");
                    $label = "§e" . $displayName . " §7(" . $status . "§7)";
                } else {
                    $displayName = $friendName;
                    $status = LangManager::raw("status-offline");
                    $lastSeen = $lastSeenManager->getLastSeen($friendName);
                    if ($lastSeen !== null) {
                        $ago = time() - $lastSeen;
                        $timeString = TimeUtils::formatDuration($ago);
                        $lastSeenText = LangManager::raw("last-seen", ["time" => $timeString]);
                        $status .= " §7- " . $lastSeenText;
                    }
                    $label = "§e" . $displayName . " §7(" . $status . "§7)";
                }
                $form->addButton($label);
            }
            $form->addButton(LangManager::raw("ui-button-back"));
        }

        $form->sendTo($player);
    }

    private static function openActions(Player $player, string $friend): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $friendsManager = $plugin->getFriendsManager();

        $playerLower = strtolower($player->getName());
        $friendLower = strtolower($friend);

        if (!in_array($friendLower, $friendsManager->getFriends($playerLower), true)) {
            self::open($player);
            return;
        }

        $form = new SimpleForm(function (Player $player, ?int $data) use ($friend, $friendLower): void {
            if ($data === null) {
                FriendsListForm::open($player);
                return;
            }

            if ($data === 0) {
                $plugin = Main::getInstance();
                $manager = $plugin->getFriendsManager();

                $playerLower = strtolower($player->getName());

                if ($manager->removeFriend($playerLower, $friendLower)) {
                    if ($plugin->getConfig()->get("two-sided-friends", true)) {
                        $manager->removeFriend($friendLower, $playerLower);
                    }
                    $player->sendMessage(LangManager::get("friend-removed", ["target" => $friend]));
                } else {
                    $player->sendMessage(LangManager::get("not-in-friend-list", ["target" => $friend]));
                }

                FriendsListForm::open($player);
            } elseif ($data === 1) {
                FriendsListForm::open($player);
            }
        });

        $form->setTitle(LangManager::raw("ui-friend-actions-title", ["name" => $friend]));
        $form->setContent(LangManager::raw("ui-friend-actions-content", ["name" => $friend]));
        $form->addButton(LangManager::raw("ui-friend-actions-remove"));
        $form->addButton(LangManager::raw("ui-button-back"));

        $form->sendTo($player);
    }

}