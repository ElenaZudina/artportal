// Проверка доступности сайта для Playwright
const { test, expect } = require('@playwright/test');

test('Сайт artportal доступен', async ({ page }) => {
  await page.goto('./', { waitUntil: 'domcontentloaded' });
  await expect(page.getByRole('heading', { name: 'ArtPortal' })).toBeVisible();
});
