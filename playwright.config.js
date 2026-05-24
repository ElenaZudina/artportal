// Root Playwright config used by `npx playwright test` from the project root.

/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
  testDir: './e2e/tests',
  outputDir: './e2e/test-results',
  globalSetup: './e2e/global-setup.js',
  reporter: [['html', { outputFolder: './e2e/playwright-report', open: 'never' }], ['list']],
  timeout: 90000,
  workers: 1,
  retries: 0,
  use: {
    baseURL: 'http://localhost/artportal/',
    extraHTTPHeaders: {
      'X-App-Env': 'test',
    },
    headless: true,
    viewport: { width: 1280, height: 800 },
    ignoreHTTPSErrors: true,
    video: 'retain-on-failure',
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
};

module.exports = config;
