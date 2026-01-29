![minecraft_title](https://github.com/user-attachments/assets/e3f89c56-2399-454f-9c63-17febaedaff1)

A complete, **Friends System Plugin** for **PocketMine-MP**, made with performance and modularity in mind.
---

## ✨ Features

- ✅ Add / remove friends with **request system**
- ✅ Accept or deny friend requests (player optional if only one request)
- ✅ Automatic expiration of requests after configurable cooldown
- ✅ Optional storage: **YAML** or **JSON**
- ✅ Friend status display: `Online` / `Offline` + last seen
- ✅ Notifications when a friend *joins* or *leaves* the server
- ✅ Configurable message prefix (`[FriendsX]`)
- ✅ Multi-language support (see below)
- ✅ Per-player settings:
   - Receive friend requests on/off
   - Friend join/leave notifications on/off
- ✅ Friend limit system, with permission-based limits
- ✅ Block system:
   - Block / unblock players
   - List blocked players
- ✅ Requests helper:
   - List all pending requests
   - Accept/deny via name or UI
- ✅ Optional **Forms UI** (GUI) for managing friends, requests, settings, blocked players

---

## 📦 Installation

1. Download the precompiled `.phar` or build from source (see below)
2. Place the `FriendsX.phar` into your server's `plugins/` folder
3. Start the server once to generate files
4. (Optional) Edit `config.yml` to configure:
   - Storage type (`yaml` / `json`)
   - Friend request cooldown
   - Language
   - Prefix formatting
   - Friend limits
   - Forms (GUI) enabled / disabled

---

## 🧾 Commands

| Command | Description |
|--------|-------------|
| `/friends` | Open the Friends GUI (if forms are enabled) or show help |
| `/friends add <player>` | Send a friend request |
| `/friends accept [player]` | Accept a received request (player optional if only one) |
| `/friends deny [player]` | Deny a received request (player optional if only one) |
| `/friends remove <player>` | Remove from your friend list |
| `/friends list` | Show your friends and their statuses (`Online` / `Offline`) |
| `/friends requests` | Show all pending friend requests |
| `/friends settings` | Open personal settings (requests + notifications) |
| `/friends block <player>` | Block a player (they cannot send you friend requests) |
| `/friends unblock <player>` | Unblock a previously blocked player |
| `/friends blocked` | Show your blocked players list |

## ⚙️ Config
```yaml
# ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
# ┃         FriendsX Plugin       ┃
# ┃       Main Configuration      ┃
# ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

# 🔧 Storage system for player data (friends list)
# Options:
#  - yaml   = saves data into YAML files (human-readable)
#  - json   = saves data into JSON files
storage: yaml

# 🌐 Language for all plugin messages
# Available: eng, ita, fra, deu, esp, por, pol
# Make sure there is a corresponding file in the /lang folder, e.g. eng.yml
lang: eng

# ⏱️ Friend request expiration time (in seconds)
# After this time, a pending request can no longer be accepted.
friend-request-cooldown: 120

# ✉️ Prefix shown before every message sent by the plugin
# You can use Minecraft color codes like §6, §e, etc.
prefix: "§7[§6FriendsX§7] §r"

# 🤝 Friendship behaviour
# If true, when a player removes a friend, the other side is also removed.
# Example:
#  - two-sided-friends: true  -> A removes B, B also loses A as friend
#  - two-sided-friends: false -> A removes B, but B still has A in their list
two-sided-friends: true

# 👥 Friend limit
# Maximum number of friends a player can have by default.
default-friend-limit: 50

# 🔐 Friend limits by permission
# You can override the default limit using permissions.
# Format:
#  friend-limits:
#    permission.node.here: limit
#
# Example:
#  friend-limits:
#    friendsx.limit.vip: 100
#    friendsx.limit.elite: 200
#
# If a player has multiple permissions, the HIGHEST limit is used.
friend-limits: {}

# 🎛 UI / Forms
# Enable or disable UI menus..
forms:
  enabled: true
```

## 🌍 Supported Languages
- 🇬🇧 `eng` – English
- 🇮🇹 `ita` – Italian
- 🇫🇷 `fra` – French
- 🇩🇪 `deu` – German
- 🇪🇸 `esp` – Spanish
- 🇵🇱 `pol` – Polish
- 🇵🇹 `por` – Portuguese (BR)
- 🇺🇦 `ukr` – Ukrainian
- 🇷🇺 `rus` – Russian
- 🇯🇵 `jpn` – Japanese
- 🇰🇷 `kor` – Korean
- 🇨🇳 `zho` – Chinese (Simplified)
- 🇹🇷 `tur` – Turkish

## ⭐ Contribute

- Found a bug? Open an issue
- Pull requests welcome
- Star the repo if you like the project!