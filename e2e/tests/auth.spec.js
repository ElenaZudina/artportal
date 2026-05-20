// E2E тесты: регистрация, логин, логаут
const { test, expect } = require('@playwright/test');

const randomEmail = () => `testuser_${Date.now()}@example.com`;
const testPassword = 'Test12345!';

// Регистрация нового пользователя
// (Если капча или email-верификация — тест может потребовать доработки)
test('Регистрация пользователя', async ({ page }) => {
  await page.goto('/registerForm');
  await page.fill('input[name="username"]', 'Тестовый пользователь');
  const email = randomEmail();
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', testPassword);
  await page.fill('input[name="password2"]', testPassword);
  await page.click('button[type="submit"]');
  await expect(page.locator('body')).toContainText(/успешно|профиль|profile|зарегистрирован/i);
});

test('Логин и логаут', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input[name="email"]', 'demo@artportal.local'); // замените на валидного тестового пользователя
  await page.fill('input[name="password"]', 'demo123'); // замените на валидный пароль
  await page.click('button[type="submit"]');
  await expect(page.locator('body')).toContainText(/dashboard|кабинет|выход|logout/i);
  // Логаут
  await page.goto('/logout');
  await expect(page.locator('form')).toBeVisible(); // форма логина
});
