// E2E тесты: админ-панель (smoke)
const { test, expect } = require('@playwright/test');

// Используйте валидные тестовые данные администратора
const ADMIN_EMAIL = 'admin@artportal.local';
const ADMIN_PASSWORD = 'admin123';

test.describe('Админ-панель', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', ADMIN_EMAIL);
    await page.fill('input[name="password"]', ADMIN_PASSWORD);
    await page.click('button[type="submit"]');
    await page.goto('/admin');
    await expect(page.locator('body')).toContainText(/админ|admin|панель/i);
  });

  test('Список пользователей', async ({ page }) => {
    await page.goto('/admin/users');
    await expect(page.locator('h1, h2')).toContainText(/пользовател|users/i);
  });

  test('Список категорий', async ({ page }) => {
    await page.goto('/admin/categories');
    await expect(page.locator('h1, h2')).toContainText(/категор/i);
  });
});
