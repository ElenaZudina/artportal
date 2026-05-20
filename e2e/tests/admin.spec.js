const { test, expect } = require('@playwright/test');

const ADMIN_EMAIL = 'admin@artportal.ee';
const ADMIN_PASSWORD = '123456';

test.describe('Админ-панель', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('./login');
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto('./admin/startAdmin');
    await expect(page.locator('body')).toContainText(/админ|admin|панель/i);
  });

  test('Список пользователей', async ({ page }) => {
    await page.goto('./admin/users');
    await expect(page.locator('h1, h2').filter({ hasText: /пользовател|users/i }).first()).toBeVisible();
  });

  test('Список категорий', async ({ page }) => {
    await page.goto('./admin/categories');
    await expect(page.locator('h1, h2').filter({ hasText: /категор|categor/i }).first()).toBeVisible();
  });
});
