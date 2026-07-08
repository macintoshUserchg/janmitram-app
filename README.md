# Janmitram App

Laravel ecommerce SPA.

## Setup

### MAMP

1. Open MAMP → Preferences → Web Server
2. Set **Document Root** to the project's `public/` folder:
   ```
   /Applications/MAMP/htdocs/janmitram-app/public
   ```
3. Apache restarts automatically. App loads at:
   ```
   http://localhost:8888/
   ```

**Alternative (no MAMP config change):**
```
http://localhost:8888/janmitram-app/public/
```

### Environment

Copy `.env.example` to `.env` and configure:

| Key | Value |
|-----|-------|
| `DB_DATABASE` | `ready_ecommerce` |
| `DB_USERNAME` | `root` |
| `DB_PASSWORD` | `root` |

### Database

```bash
php artisan migrate
```
