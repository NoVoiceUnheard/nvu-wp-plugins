# NVU WordPress Plugins

This repository contains custom and third-party plugins used in the NVU WordPress setup. It is managed as a **Git subtree** within the main WordPress project.

## 📥 Cloning the Repository

To clone this repository independently, use:

```bash
git clone https://github.com/NoVoiceUnheard/nvu-wp-plugins.git
```
If you are working within the main WordPress repository using a Git subtree, the plugins folder is updated with:
```bash
git subtree pull --prefix=wp-content/plugins https://github.com/NoVoiceUnheard/nvu-wp-plugins.git master --squash
```
## 🔧 Adding or Updating Plugins
To add a new plugin, place it inside the wp-content/plugins/ folder and commit the changes:
```bash
git add wp-content/plugins/new-plugin
git commit -m "Added new-plugin"
git push origin master
```
To push local plugin changes back to this repository from the main project:
```bash
git subtree push --prefix=wp-content/plugins https://github.com/NoVoiceUnheard/nvu-wp-plugins.git master
```
## 🛠️ Installation & Usage
1.	Ensure the wp-content/plugins/ directory contains the required plugins.
2.	Activate the plugins in the WordPress admin panel under Plugins → Installed Plugins.
3.	If a plugin requires additional setup (e.g., API keys, configurations), refer to its documentation.
## 📜 License
This repository contains a mix of custom and third-party plugins. Ensure you comply with the licensing terms of any third-party plugins included here.