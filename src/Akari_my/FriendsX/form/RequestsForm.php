<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\command\arguments\acceptFriend;
use Akari_my\FriendsX\command\arguments\denyFriend;
use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\LangManager;
use pocketmine\player\Player;

class RequestsForm {

    private const PER_PAGE = 10;

    public static function open(Player $player, int $page = 0): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $requestsManager = $plugin->getRequestManager();
        $playerLower = strtolower($player->getName());
        $requests = array_values($requestsManager->getRequests($playerLower));
        $total = count($requests);
        $maxPage = (int)ceil($total / self::PER_PAGE) - 1;
        if ($maxPage < 0) $maxPage = 0;
        if ($page > $maxPage) $page = $maxPage;
        $start = $page * self::PER_PAGE;
        $slice = array_slice($requests, $start, self::PER_PAGE);

        $form = new SimpleForm(function (Player $player, ?int $data) use ($requests, $page, $maxPage, $total): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            $friendCount = count(array_slice($requests, $page * self::PER_PAGE, self::PER_PAGE));
            $idx = 0;

            if ($total > 0 && $data < $friendCount) {
                $globalIdx = $page * self::PER_PAGE + $data;
                if (isset($requests[$globalIdx])) {
                    self::openActions($player, $requests[$globalIdx]);
                }
                return;
            }
            $idx = $friendCount;

            if ($page > 0 && $data === $idx) {
                self::open($player, $page - 1);
                return;
            }
            if ($page > 0) $idx++;

            if ($page < $maxPage && $data === $idx) {
                self::open($player, $page + 1);
                return;
            }

            MainForm::open($player);
        });

        $title = LangManager::raw("ui-requests-title");
        if ($total > 0) $title .= " §7(" . ($page + 1) . "/" . ($maxPage + 1) . ")";
        $form->setTitle($title);

        if ($total === 0) {
            $form->setContent(LangManager::raw("ui-requests-empty"));
            $form->addButton(LangManager::raw("ui-button-back"));
        } else {
            $form->setContent(LangManager::raw("ui-requests-content"));
            foreach ($slice as $name) {
                $form->addButton(LangManager::raw("ui-requests-entry", ["name" => $name]));
            }
            if ($page > 0) $form->addButton(LangManager::raw("ui-prev-page"));
            if ($page < $maxPage) $form->addButton(LangManager::raw("ui-next-page"));
            $form->addButton(LangManager::raw("ui-button-back"));
        }

        $form->sendTo($player);
    }

    private static function openActions(Player $player, string $from): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

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
