// Smoke test: Проверка доступности главных страниц
const { test, expect } = require('@playwright/test');

test.describe('Smoke: Public pages', () => {
  test('Главная страница открывается', async ({ page }) => {
    await page.goto('./');
    await expect(page).toHaveTitle(/Art|Арт|Портал/i);
  });

  test('Галерея всех картин', async ({ page }) => {
    await page.goto('./all');
    await expect(page.locator('h1, h2').filter({ hasText: /paintings|картины|gallery|галерея/i }).first()).toBeVisible();
  });

  test('Галерея художников', async ({ page }) => {
    await page.goto('./artists');
    await expect(page.locator('h1, h2').filter({ hasText: /artists|художник|artist/i }).first()).toBeVisible();
  });

  test('Страница логина', async ({ page }) => {
    await page.goto('./login');
    await expect(page.locator('form')).toBeVisible();
  });
});
