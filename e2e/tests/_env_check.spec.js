// Проверка доступности сайта для Playwright
const { test, expect } = require('@playwright/test');

test('Сайт artportal доступен', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Art|Арт|Портал/i);
});
