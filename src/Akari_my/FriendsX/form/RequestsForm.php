<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\command\arguments\acceptFriend;
use Akari_my\FriendsX\command\arguments\denyFriend;
use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class RequestsForm{

    public static function open(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $requestsManager = $plugin->getRequestManager();
        $playerLower = strtolower($player->getName());
        $requests = array_values($requestsManager->getRequests($playerLower));

        $form = new SimpleForm(function (Player $player, ?int $data) use ($requests): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            if (empty($requests)) {
                if ($data === 0) {
                    MainForm::open($player);
                }
                return;
            }

            if (!isset($requests[$data])) {
                return;
            }

            $from = $requests[$data];
            self::openActions($player, $from);
        });

        $form->setTitle(LangManager::raw("ui-requests-title"));

        if (empty($requests)) {
            $form->setContent(LangManager::raw("ui-requests-empty"));
            $form->addButton(LangManager::raw("ui-button-back"));
        } else {
            $form->setContent(LangManager::raw("ui-requests-content"));
            foreach ($requests as $name) {
                $label = LangManager::raw("ui-requests-entry", ["name" => $name]);
                $form->addButton($label);
            }
        }

        $form->sendTo($player);
    }

    private static function openActions(Player $player, string $from): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) {
            return;
        }

        $form = new SimpleForm(function (Player $player, ?int $data) use ($from): void {
            if ($data === null) {
                RequestsForm::open($player);
                return;
            }

            if ($data === 0) {
                (new acceptFriend())->execute($player, [$from]);
                RequestsForm::open($player);
            } elseif ($data === 1) {
                (new denyFriend())->execute($player, [$from]);
                RequestsForm::open($player);
            } elseif ($data === 2) {
                RequestsForm::open($player);
            }
        });

        $form->setTitle(LangManager::raw("ui-request-actions-title", ["name" => $from]));
        $form->setContent(LangManager::raw("ui-request-actions-content", ["name" => $from]));
        $form->addButton(LangManager::raw("ui-request-actions-accept"));
        $form->addButton(LangManager::raw("ui-request-actions-deny"));
        $form->addButton(LangManager::raw("ui-button-back"));

        $form->sendTo($player);
    }
}