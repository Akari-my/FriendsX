<?php

namespace Akari_my\FriendsX\manager;

use Akari_my\FriendsX\Main;
use pocketmine\utils\Config;

class LangManager {

    private static array $messages = [];
    private static string $prefix = "";

    public static function init(Main $plugin): void {
        $langCode = strtolower($plugin->getConfig()->get("lang", "eng"));

        $langPath = $plugin->getDataFolder() . "lang/$langCode.yml";
        if (!file_exists($langPath)) {
            $plugin->getLogger()->warning("Language file '$langCode.yml' not found. Falling back to English.");
            $langPath = $plugin->getDataFolder() . "lang/eng.yml";
        }

        if (!file_exists($langPath)) {
            $plugin->saveResource("lang/eng.yml");
        }

        self::$messages = (new Config($langPath, Config::YAML))->getAll();
        self::$prefix = $plugin->getConfig()->get("prefix", "");
    }

    public static function get(string $key, array $placeholders = []): string {
        $message = self::$messages[$key] ?? "§c[Missing message: $key]";

        foreach ($placeholders as $search => $replace) {
            $message = str_replace("{" . $search . "}", $replace, $message);
        }

        return self::$prefix . $message;
    }
}