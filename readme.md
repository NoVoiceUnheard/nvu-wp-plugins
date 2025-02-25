# Website Setup & Contribution Guide

## Prerequisites
Before setting up the project, ensure you have the following installed:
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

If issues persist, reach out to a team member for assistance!

---
This README will evolve as we refine our workflow. Feel free to update it with any additional steps or best practices!
