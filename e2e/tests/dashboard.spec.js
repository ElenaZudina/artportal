const { test, expect } = require('@playwright/test');

const USER_EMAIL = 'e2e-user@artportal.test';
const PASSWORD = '123456';
const appUrl = (path) => new URL(path, 'http://localhost/artportal/').toString();

async function loginAsUser(page) {
  await page.goto(appUrl('login'));
  await page.fill('input[name="email"]', USER_EMAIL);
  await page.fill('input[name="password"]', PASSWORD);
  await Promise.all([
    page.waitForURL(/\/artportal\/dashboard\//),
    page.click('button[type="submit"]'),
  ]);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('body')).toContainText(/dashboard|logout/i);
}

test.describe('Личный кабинет', () => {
  test.beforeEach(async ({ page }) => {
    await loginAsUser(page);
  });

  test('Переход в профиль', async ({ page }) => {
    await page.goto(appUrl('dashboard/profile'));
    await expect(page.locator('h1, h2').filter({ hasText: /profile/i }).first()).toBeVisible();
    await expect(page.getByRole('heading', { name: /artist profile/i })).toBeVisible();
  });

  test('Мои картины', async ({ page }) => {
    await page.goto(appUrl('dashboard/my-paintings'));
    await expect(page.locator('h1, h2, a').filter({ hasText: /artist profile|become an artist/i }).first()).toBeVisible();
  });

  test('Избранное', async ({ page }) => {
    await page.goto(appUrl('dashboard/my-favorites'));
    await expect(page.locator('h1, h2').filter({ hasText: /favorites/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();
  });
});
