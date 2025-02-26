# Website Setup & Contribution Guide

## :pushpin: Prerequisites
- PHP (Ensure it's installed and available in your terminal) [Mac](https://www.php.net/manual/en/install.macosx.packages.php) [Windows](https://www.php.net/manual/en/install.windows.php)
- **Composer** (Required for dependency management)
  - from project directory:  
    ```bash
      php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
      php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
      php composer-setup.php
      php -r "unlink('composer-setup.php');"
      mv composer.phar /usr/local/bin/composer
    ```
- [Local](https://localwp.com/) (For managing the WordPress site locally)
- [Git](https://git-scm.com/downloads) (For version control)
- A GitHub account with access to [this repository](https://github.com/NoVoiceUnheard/website)

# ✅ Setting Up a New Local Site & Transferring the Repo

## 1 Create a New Site in Local
- Open Local by Flywheel
- Click **Create a New Site**
- Choose **Preferred setup**
- Setup your admin user
- Complete the site setup

## 2 Remove Default public Folder
- Go to your Local site’s folder
- Delete the default **public** folder

## 3️ Clone Your Repo into the Site Folder
Open a terminal and navigate to your Local Sites app folder and clone:
```bash
cd path/to/Local\ Sites/your-site/app
git clone https://github.com/NoVoiceUnheard/website.git public
```

## 4️ Run Composer
```
cd public
composer install
```

## 5 Set Up .env File in the root directory with the following format:
   ```ini
      DB_NAME=local
      DB_USER=root
      DB_PASSWORD=root
      DB_HOST=localhost
      WP_HOME=http://novoiceunheard.local
      WP_SITEURL=http://novoiceunheard.local
      WP_ENVIRONMENT_TYPE=local
   ```

## 6. Access the WordPress Dashboard
- Navigate to `novoiceunheard.local/wp-admin/`
- Use the credentials provided (or set up a new admin user 
   
## Making Changes & Committing to GitHub

### 1. Pull the Latest Changes
Always pull the latest changes before making edits to ensure you're working with the most up-to-date code.
```bash
git pull origin main
```

### 2. Make Your Changes
Modify files as needed. This may include theme files, plugins, or content updates.

### 3. Commit Your Changes
After making changes, stage them for commit:
```bash
git add .
git commit -m "Describe your changes here"
```

### 4. Push Your Changes to GitHub
```bash
git push origin main
```

## Troubleshooting
If you encounter errors while pulling or pushing changes, try:
- Ensuring you're on the `main` branch: `git checkout main`
- Pulling changes again before pushing: `git pull origin main`
- Checking for merge conflicts and resolving them before committing
- Make sure environment variables are enabled in php

If issues persist, reach out to a team member for assistance!

## TO DO:

- [x] Create custom child theme
- [ ] Setup static pages
- [ ] Setup blog with ActivityPub
- [ ] Create protest listing page and connect to Proton calendar
- [ ] Static pages
- [ ] Shop / Donation integration
- [ ] AMP (mobile seo and performance)
- [ ] Organizers
- [ ] Contact
---
This README will evolve as we refine our workflow. Feel free to update it with any additional steps or best practices!
