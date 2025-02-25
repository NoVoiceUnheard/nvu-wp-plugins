# Website Setup & Contribution Guide

## 📌 Prerequisites
- PHP (Ensure it's installed and available in your terminal)
- **Composer** (Required for dependency management)
  - Install via:  
    ```bash
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    ```
- [Local](https://localwp.com/) (For managing the WordPress site locally)
- [Git](https://git-scm.com/) (For version control)
- A GitHub account with access to this repository

## Setting Up the Project

### 1. Clone the Repository
Open a terminal and navigate to your preferred development directory, then run:
```bash
git clone https://github.com/NoVoiceUnheard/website.git
cd website
```

### 2. Import the Project into Local
1. Open Local and select **"Import Site"**.
2. Choose the cloned repository folder.
3. Follow the setup prompts (use the default PHP & MySQL versions recommended by Local).
4. Start the site in Local.

### 3. Access the WordPress Dashboard
- Navigate to `novoiceunheard.local/wp-admin/`
- Use the credentials provided (or set up a new admin user if required).

## 🌍 Environment Variables & Dotenv
This project uses **Dotenv** to manage environment variables.

### 🔧 Setup
1. **Ensure Composer is Installed**  
   Run:
   ```bash
   composer install
   ```

2. **Create a `.env` file** in the root directory with the following format:
   ```ini
      DB_NAME=local
      DB_USER=root
      DB_PASSWORD=root
      DB_HOST=localhost
      WP_HOME=http://novoiceunheard.local
      WP_SITEURL=http://novoiceunheard.local
      WP_ENVIRONMENT_TYPE=local
   ```
   
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
---
This README will evolve as we refine our workflow. Feel free to update it with any additional steps or best practices!
