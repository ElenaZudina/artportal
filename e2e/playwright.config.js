// Playwright config for artportal e2e tests
// Docs: https://playwright.dev/docs/test-configuration

/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
  testDir: './tests',
  outputDir: './test-results',
  globalSetup: './global-setup.js',
  reporter: [['html', { outputFolder: './playwright-report', open: 'never' }], ['list']],
  timeout: 30000,
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
