// Проверка: реально ли Playwright может открыть главную страницу
const { test, expect } = require('@playwright/test');

const fs = require('fs');
test('DEBUG: Проверка baseURL и HTML', async ({ page }) => {
  await page.goto('/');
  await page.screenshot({ path: 'e2e/tests/__debug_main.png' });
  const html = await page.content();
  fs.writeFileSync('e2e/tests/__debug_main.html', html);
});
