const { test, expect } = require('@playwright/test');
test('DEBUG: baseURL and HTML are available', async ({ page }) => {
  const response = await page.goto('./', { waitUntil: 'domcontentloaded' });

  expect(response && response.ok()).toBeTruthy();
  await expect(page.getByRole('heading', { name: 'ArtPortal' })).toBeVisible();

  const html = await page.content();
  expect(html).toContain('ArtPortal');
});
