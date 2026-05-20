const { test, expect } = require('@playwright/test');

const randomEmail = () => `dashboard_${Date.now()}_${Math.random().toString(16).slice(2)}@example.com`;
const testPassword = 'Test12345!';

async function registerAndLogin(page) {
  await page.goto('./registerForm');
  await page.fill('input[name="name"]', `dashboard_${Date.now()}`);
  await page.fill('input[name="email"]', randomEmail());
  await page.fill('input[name="password"]', testPassword);
  await page.fill('input[name="confirm"]', testPassword);
  await page.click('button[type="submit"]');
  await expect(page.locator('body')).toContainText(/User has been added|success|успешно/i);
}

test.describe('Личный кабинет', () => {
  test.beforeEach(async ({ page }) => {
    await registerAndLogin(page);
    await page.goto('./dashboard/startDashboard');
    await expect(page.locator('body')).toContainText(/dashboard|кабинет|выход|logout/i);
  });

  test('Переход в профиль', async ({ page }) => {
    await page.goto('./dashboard/profile');
    await expect(page.locator('h1, h2').filter({ hasText: /профиль|profile/i }).first()).toBeVisible();
  });

  test('Мои картины', async ({ page }) => {
    await page.goto('./dashboard/my-paintings');
    await expect(
      page.locator('h1, h2, a').filter({ hasText: /картины|paintings|artist profile|become an artist/i }).first()
    ).toBeVisible();
  });

  test('Избранное', async ({ page }) => {
    await page.goto('./dashboard/my-favorites');
    await expect(page.locator('h1, h2').filter({ hasText: /избран|favor/i }).first()).toBeVisible();
  });
});
