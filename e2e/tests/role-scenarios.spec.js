const { test, expect } = require('@playwright/test');

const PASSWORD = '123456';
const appUrl = (path) => new URL(path, 'http://localhost/artportal/').toString();

async function loginAs(page, email) {
  await page.goto(appUrl('login'));
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', PASSWORD);
  await Promise.all([
    page.waitForURL(/\/artportal\/(dashboard|admin)\//),
    page.click('button[type="submit"]'),
  ]);
  await page.waitForLoadState('networkidle');
  await expect(page.locator('body')).toContainText(/dashboard|admin|logout/i);
}

test.describe('Основные сценарии по ролям', () => {
  test('Гость просматривает публичную галерею и карточку работы', async ({ page }) => {
    await page.goto(appUrl('all'));
    await expect(page.locator('h1, h2').filter({ hasText: /paintings|gallery|картины|галерея/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();

    await page.getByText('E2E Seed Painting').click();
    await expect(page.getByRole('heading', { name: 'E2E Seed Painting' })).toBeVisible();
    await expect(page.getByText('E2E Seed Artist')).toBeVisible();
    await expect(page.getByRole('button', { name: /inquire about purchase/i })).toBeVisible();
  });

  test('Гость не может отправить purchase inquiry', async ({ page }) => {
    await page.goto(appUrl('all'));
    await page.getByText('E2E Inquiry Painting').click();

    await Promise.all([
      page.waitForURL(/\/artportal\/login/),
      page.getByRole('button', { name: /inquire about purchase/i }).click(),
    ]);

    await expect(page.getByRole('heading', { name: /login/i })).toBeVisible();
    await expect(page.locator('body')).toContainText(/must be logged in/i);
  });

  test('Пользователь видит кабинет, избранное и свои запросы', async ({ page }) => {
    await loginAs(page, 'e2e-user@artportal.test');
    await page.goto(appUrl('dashboard/my-favorites'));
    await expect(page.locator('h1, h2').filter({ hasText: /favorites|избран/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();
    await expect(page.getByRole('button', { name: /remove from favorites/i })).toBeVisible();

    await page.goto(appUrl('dashboard/my-requests'));
    await expect(page.locator('h1, h2').filter({ hasText: /requests|запрос/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();
    await expect(page.getByText('E2E Seed Artist')).toBeVisible();
  });

  test('Пользователь отправляет purchase inquiry и видит его в своих запросах', async ({ page }) => {
    await loginAs(page, 'e2e-user@artportal.test');
    await page.goto(appUrl('all'));
    await page.getByText('E2E Inquiry Painting').click();

    await page.getByRole('button', { name: /inquire about purchase/i }).click();
    await expect(page.locator('body')).toContainText(/request sent|request saved|email notification/i);

    await page.goto(appUrl('dashboard/my-requests'));
    await expect(page.locator('h1, h2').filter({ hasText: /requests|запрос/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Inquiry Painting')).toBeVisible();
  });

  test('Художник видит портфолио и входящие запросы на покупку', async ({ page }) => {
    await loginAs(page, 'e2e-artist@artportal.test');
    await page.goto(appUrl('dashboard/my-paintings'));
    await expect(page.locator('h1, h2').filter({ hasText: /my paintings|paintings/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();
    await expect(page.getByRole('main').getByRole('link', { name: /^add painting$/i })).toBeVisible();

    await page.goto(appUrl('dashboard/purchase-requests'));
    await expect(page.locator('h1, h2, h3').filter({ hasText: /purchase requests/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Seed Painting')).toBeVisible();
    await expect(page.getByRole('link', { name: 'e2e-user@artportal.test' }).first()).toBeVisible();
  });

  test('Администратор видит управление пользователями, категориями и модерацией художников', async ({ page }) => {
    await loginAs(page, 'admin@artportal.ee');

    await page.goto(appUrl('admin/users'));
    await expect(page.locator('h1, h2').filter({ hasText: /users|пользовател/i }).first()).toBeVisible();
    await expect(page.getByText('e2e-user@artportal.test')).toBeVisible();
    await expect(page.getByText('e2e-artist@artportal.test')).toBeVisible();

    await page.goto(appUrl('admin/categories'));
    await expect(page.locator('h1, h2').filter({ hasText: /categories|категор/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Category')).toBeVisible();

    await page.goto(appUrl('admin/moderation-artists'));
    await expect(page.locator('h1, h2').filter({ hasText: /artist requests|moderation/i }).first()).toBeVisible();
    await expect(page.getByText('E2E Pending Artist')).toBeVisible();
    await expect(page.getByRole('link', { name: /view profile/i }).first()).toBeVisible();
  });
});
