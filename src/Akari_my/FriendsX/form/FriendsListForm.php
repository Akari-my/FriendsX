<?php

namespace Akari_my\FriendsX\form;

use Akari_my\FriendsX\libs\FormX\CustomForm;
use Akari_my\FriendsX\libs\FormX\ModalForm;
use Akari_my\FriendsX\libs\FormX\SimpleForm;
use Akari_my\FriendsX\Main;
use Akari_my\FriendsX\manager\FriendsMetadataManager;
use Akari_my\FriendsX\manager\LangManager;
use Akari_my\FriendsX\manager\PlayerStatusManager;
use Akari_my\FriendsX\util\Functions;
use Akari_my\FriendsX\util\TimeUtils;
use pocketmine\player\Player;

class FriendsListForm {

    private const PER_PAGE = 10;

    public static function open(Player $player, int $page = 0, array $searchResults = null): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $friendsManager = $plugin->getFriendsManager();
        $lastSeenManager = $plugin->getLastSeenManager();
        $metaManager = $plugin->getMetadataManager();
        $statusManager = $plugin->getPlayerStatusManager();

        $playerName = $player->getName();
        $playerLower = strtolower($playerName);
        $allFriends = $friendsManager->getFriends($playerLower);

        if ($searchResults !== null) {
            $friends = $searchResults;
        } else {
            $friends = $allFriends;
        }

        $total = count($friends);
        $maxPage = (int)ceil($total / self::PER_PAGE) - 1;
        if ($maxPage < 0) $maxPage = 0;
        if ($page > $maxPage) $page = $maxPage;
        $start = $page * self::PER_PAGE;
        $slice = array_slice($friends, $start, self::PER_PAGE);

        $form = new SimpleForm(function (Player $player, ?int $data) use ($allFriends, $friends, $page, $maxPage, $total, $searchResults): void {
            if ($data === null) {
                MainForm::open($player);
                return;
            }

            $idx = 0;
            $searchBtn = 0;

            if ($total > 0) {
                $friendCount = count(array_slice($friends, $page * self::PER_PAGE, self::PER_PAGE));
                if ($data < $friendCount) {
                    $globalIdx = $page * self::PER_PAGE + $data;
                    if (isset($friends[$globalIdx])) {
                        self::openActions($player, $friends[$globalIdx]);
                    }
                    return;
                }
                $idx = $friendCount;
            }

            if ($data === $idx && $searchResults === null && count($allFriends) > 0) {
                self::openSearch($player);
                return;
            }
            if ($searchResults !== null && $data === $idx) {
                self::open($player);
                return;
            }
            $idx++;

            $prevBtn = $idx;
            if ($page > 0 && $data === $idx) {
                self::open($player, $page - 1, $searchResults);
                return;
            }
            if ($page > 0) $idx++;

            $nextBtn = $page > 0 ? $prevBtn + 1 : $idx;
            if ($page < $maxPage && $data === ($page > 0 ? $prevBtn + 1 : $idx)) {
                self::open($player, $page + 1, $searchResults);
                return;
            }

            MainForm::open($player);
        });

        $title = LangManager::raw("ui-friends-title");
        if ($total > 0) $title .= " §7(" . ($page + 1) . "/" . ($maxPage + 1) . ")";
        $form->setTitle($title);

        if ($total === 0) {
            $form->setContent($searchResults !== null ? LangManager::raw("ui-friends-search-none") : LangManager::raw("ui-friends-empty"));
            if ($searchResults !== null) {
                $form->addButton(LangManager::raw("ui-button-back"));
            } else {
                $form->addButton(LangManager::raw("ui-button-back"));
            }
        } else {
            $form->setContent(LangManager::raw("ui-friends-content"));

            foreach ($slice as $friendName) {
                $favorite = $metaManager->isFavorite($playerLower, $friendName);
                $group = $metaManager->getGroup($playerLower, $friendName);

                $prefix = $favorite ? "§6★ " : "§7- ";

                $onlinePlayer = Functions::getPlayerByName($friendName);
                if ($onlinePlayer !== null && $onlinePlayer->isOnline()) {
                    $check = strtolower($onlinePlayer->getName());
                    $pStatus = $statusManager->getStatus($check);
                    if ($pStatus === "invisible") {
                        $label = $prefix . "§e" . $friendName . " §7(" . LangManager::raw("status-offline") . "§7)";
                    } else {
                        $statusLabel = PlayerStatusManager::getStatusLabel($pStatus);
                        $label = $prefix . "§e" . $onlinePlayer->getName() . " §7(" . $statusLabel . "§7)";
                    }
                } else {
                    $status = LangManager::raw("status-offline");
                    $lastSeen = $lastSeenManager->getLastSeen($friendName);
                    if ($lastSeen !== null) {
                        $ago = time() - $lastSeen;
                        $timeString = TimeUtils::formatDuration($ago);
                        $lastSeenText = LangManager::raw("last-seen", ["time" => $timeString]);
                        $status .= " §7- " . $lastSeenText;
                    }
                    $label = $prefix . "§e" . $friendName . " §7(" . $status . "§7)";
                }
                $form->addButton($label);
            }

            if ($searchResults === null && count($allFriends) > 0) {
                $form->addButton(LangManager::raw("ui-friends-search-btn"));
            }
            if ($searchResults !== null) {
                $form->addButton(LangManager::raw("ui-button-back"));
            }

            if ($page > 0) $form->addButton(LangManager::raw("ui-prev-page"));
            if ($page < $maxPage) $form->addButton(LangManager::raw("ui-next-page"));
            $form->addButton(LangManager::raw("ui-button-back"));
        }

        $form->sendTo($player);
    }

    public static function openSearch(Player $player): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $form = new CustomForm(function (Player $player, ?array $data): void {
            if ($data === null || !isset($data[0]) || trim($data[0]) === "") {
                self::open($player);
                return;
            }

            $query = strtolower(trim($data[0]));
            $plugin = Main::getInstance();
            $friendsManager = $plugin->getFriendsManager();
            $playerLower = strtolower($player->getName());
            $all = $friendsManager->getFriends($playerLower);

            $results = [];
            foreach ($all as $f) {
                if (str_contains(strtolower($f), $query)) {
                    $results[] = $f;
                }
            }

            if (empty($results)) {
                self::open($player, 0, []);
                return;
            }

            self::open($player, 0, $results);
        });

        $form->setTitle(LangManager::raw("ui-friends-search-title"));
        $form->addInput(LangManager::raw("ui-friends-search-input"), LangManager::raw("ui-friends-search-placeholder"));
        $form->sendTo($player);
    }

    private static function openActions(Player $player, string $friend): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $friendsManager = $plugin->getFriendsManager();
        $metaManager = $plugin->getMetadataManager();

        $playerLower = strtolower($player->getName());
        $friendLower = strtolower($friend);

        if (!in_array($friendLower, $friendsManager->getFriends($playerLower), true)) {
            self::open($player);
            return;
        }

        $isFav = $metaManager->isFavorite($playerLower, $friendLower);
        $currentGroup = $metaManager->getGroup($playerLower, $friendLower);
        $groupLabel = $metaManager->getGroupLabel($currentGroup);

        $form = new SimpleForm(function (Player $player, ?int $data) use ($friend, $friendLower, $isFav, $currentGroup): void {
            if ($data === null) {
                FriendsListForm::open($player);
                return;
            }

            $plugin = Main::getInstance();
            $metaManager = $plugin->getMetadataManager();

            if ($data === 0) {
                FriendsListForm::openRemoveConfirm($player, $friend, $friendLower);
            } elseif ($data === 1) {
                $newFav = $metaManager->toggleFavorite($player->getName(), $friendLower);
                $player->sendMessage(LangManager::get($newFav ? "friend-fav-added" : "friend-fav-removed", ["target" => $friend]));
                FriendsListForm::openActions($player, $friend);
            } elseif ($data === 2) {
                FriendsListForm::openGroupSelect($player, $friend);
            } elseif ($data === 3) {
                FriendsListForm::open($player);
            }
        });

        $form->setTitle(LangManager::raw("ui-friend-actions-title", ["name" => $friend]));
        $favStatus = $isFav ? LangManager::raw("ui-friend-actions-unfav") : LangManager::raw("ui-friend-actions-fav");
        $form->setContent(LangManager::raw("ui-friend-actions-content-ext", ["group" => $groupLabel, "name" => $friend]));
        $form->addButton(LangManager::raw("ui-friend-actions-remove"));
        $form->addButton($favStatus);
        $form->addButton(LangManager::raw("ui-friend-actions-group"));
        $form->addButton(LangManager::raw("ui-button-back"));

        $form->sendTo($player);
    }

    private static function openGroupSelect(Player $player, string $friend): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $friendLower = strtolower($friend);
        $groups = FriendsMetadataManager::getGroups();

        $form = new SimpleForm(function (Player $player, ?int $data) use ($friend, $friendLower, $groups): void {
            if ($data === null) {
                FriendsListForm::openActions($player, $friend);
                return;
            }

            if (isset($groups[$data])) {
                $plugin = Main::getInstance();
                $plugin->getMetadataManager()->setGroup($player->getName(), $friendLower, $groups[$data]);
                $player->sendMessage(LangManager::get("friend-group-set", ["target" => $friend, "group" => $plugin->getMetadataManager()->getGroupLabel($groups[$data])]));
                FriendsListForm::openActions($player, $friend);
            }
        });

        $form->setTitle(LangManager::raw("ui-group-select-title"));
        $form->setContent(LangManager::raw("ui-group-select-content", ["name" => $friend]));
        foreach ($groups as $g) {
            $form->addButton(FriendsMetadataManager::getGroupLabel($g));
        }
        $form->addButton(LangManager::raw("ui-button-back"));
        $form->sendTo($player);
    }

    private static function openRemoveConfirm(Player $player, string $friend, string $friendLower): void {
        $plugin = Main::getInstance();
        if (!$plugin->areFormsEnabled()) return;

        $form = new ModalForm(function (Player $player, ?bool $confirmed) use ($friend, $friendLower): void {
            if ($confirmed !== true) {
                FriendsListForm::open($player);
                return;
            }

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
        });

        $form->setTitle(LangManager::raw("ui-confirm-remove-title"));
        $form->setContent(LangManager::raw("ui-confirm-remove-content", ["name" => $friend]));
        $form->setButton1(LangManager::raw("ui-confirm-remove-yes"));
        $form->setButton2(LangManager::raw("ui-confirm-remove-no"));
        $form->sendTo($player);
    }
}
