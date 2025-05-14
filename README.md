![minecraft_title](https://github.com/user-attachments/assets/e3f89c56-2399-454f-9c63-17febaedaff1)

A complete, **Friends System Plugin** for **PocketMine-MP**, made with performance and modularity in mind.
---

## ✨ Features

- ✅ Add / remove friends with **request system**
- ✅ Accept or deny friend requests
- ✅ Automatic expiration of requests after configurable cooldown
- ✅ Optional storage: **YAML**, or **JSON**
- ✅ Friend status display: `Online` / `Offline`
- ✅ Notifications when a friend *joins* or *leaves* the server
- ✅ Configurable message prefix (`[FriendsX]`)
- ✅ Multi-language support: `eng`, `ita`, `fra`, `deu`, `esp`, `pol`, `por`
---

## 📦 Installation

1. Download the precompiled `.phar` or build from source (see below)
2. Place the `FriendsX.phar` into your server's `plugins/` folder
3. Start the server once to generate files
4. (Optional) Edit `config.yml` to configure:
   - Storage type
   - Friend request cooldown
   - Language
   - Prefix formatting

---

## 🧾 Commands

| Command | Description |
|--------|-------------|
| `/friend add <player>` | Send a friend request |
| `/friend accept <player>` | Accept a received request |
| `/friend deny <player>` | Deny a received request |
| `/friend remove <player>` | Remove from your friend list |
| `/friend list` | Show your friends and their statuses (`Online`/`Offline`) |

---

## 🌍 Supported Languages

Currently included:
- 🇬🇧 English
- 🇮🇹 Italian
- 🇫🇷 French
- 🇩🇪 German
- 🇪🇸 Spanish
- 🇵🇱 Polish
- 🇵🇹 Portuguese (BR)

You can add your own in `resources/lang/`.

Set the language in the `config.yml`:

```yaml
lang: eng
