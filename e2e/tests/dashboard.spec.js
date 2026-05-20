// E2E тесты: личный кабинет пользователя
const { test, expect } = require('@playwright/test');

// Используйте валидные тестовые данные
const TEST_EMAIL = 'demo@artportal.local';
const TEST_PASSWORD = 'demo123';

test.describe('Личный кабинет', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', TEST_EMAIL);
    await page.fill('input[name="password"]', TEST_PASSWORD);
    await page.click('button[type="submit"]');
    await expect(page.locator('body')).toContainText(/dashboard|кабинет|выход|logout/i);
  });

  test('Переход в профиль', async ({ page }) => {
    await page.goto('/dashboard/profile');
    await expect(page.locator('h1, h2')).toContainText(/профиль|profile/i);
  });

  test('Мои картины', async ({ page }) => {
    await page.goto('/dashboard/my-paintings');
    await expect(page.locator('h1, h2')).toContainText(/картины|paintings/i);
  });

  test('Избранное', async ({ page }) => {
    await page.goto('/dashboard/my-favorites');
    await expect(page.locator('h1, h2')).toContainText(/избран/i);
  });
});
