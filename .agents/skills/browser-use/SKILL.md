---
name: browser-use
description: Automated browser control, UI testing, web scraping, and browser task execution using browser-use, Playwright, or Puppeteer. Use whenever the user asks to automate browser interactions, test web pages visually, verify UI/UX flows, or run headless web tasks.
---

# Browser Use Skill

Automate web browser interactions, end-to-end UI testing, page validation, and web scraping using `browser-use`, Playwright, or headless browser scripts.

## When to Use

Trigger this skill when:
- The user requests automated browser testing, web navigation, or form filling.
- Performing visual or functional UI checks on local or staging URLs (e.g. `http://localhost:8888/janmitram-app/`).
- Validating user checkout flows, address saving, login forms, or shop owner dashboards automatically.
- Extracting dynamic web page content or taking screenshots of rendered web layouts.

## Standard Workflows

### 1. Web UI Validation with Playwright / Python browser-use

Run headless or headed browser automation scripts using Node.js Playwright or Python `browser-use` / `playwright`:

```bash
# Node Playwright test run
npx playwright test
```

Or execute a quick Playwright/Puppeteer script to verify local routes:

```javascript
const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto('http://localhost:8888/janmitram-app/manage-address');
  await page.screenshot({ path: 'manage_address_page.png' });
  await browser.close();
})();
```

### 2. Live Application End-to-End Verification

1. **Start Local Dev Server** (if not already running on MAMP / Apache / Vite).
2. **Navigate & Interact**: Open target pages (e.g., `/products`, `/checkout`, `/manage-address`).
3. **Assert State**: Confirm inputs (e.g., Latitude & Longitude inputs under Leaflet map), buttons, and toasts function without console errors.
4. **Capture Evidence**: Save screenshots or logs for verification reporting.

## Best Practices

- Always use explicit timeouts and wait for network idle or key DOM selectors (`.janmitram-map-wrapper`, `#Area`, etc.).
- Ensure local dev server (`http://localhost:8888/janmitram-app/`) or target URL is accessible before launching browser tasks.
- Keep headless mode enabled by default for CI and background execution, or headed mode for visual demonstration.
