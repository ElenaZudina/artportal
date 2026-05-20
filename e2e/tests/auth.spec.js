const { test, expect } = require('@playwright/test');

const randomEmail = () => `testuser_${Date.now()}_${Math.random().toString(16).slice(2)}@example.com`;
const testPassword = 'Test12345!';

async function registerUser(page) {
  const email = randomEmail();
  await page.goto('./registerForm');
  await page.fill('input[name="name"]', `testuser_${Date.now()}`);
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', testPassword);
  await page.fill('input[name="confirm"]', testPassword);
  await page.click('button[type="submit"]');
  await expect(page.locator('body')).toContainText(/User has been added|success|успешно/i);
  return email;
}

test('Регистрация пользователя', async ({ page }) => {
  await registerUser(page);
});

test('Логин и логаут', async ({ page }) => {
  const email = await registerUser(page);

  await page.goto('./logout');
  await page.goto('./login');
  await page.fill('input[name="email"]', email);
  await page.fill('input[name="password"]', testPassword);
  await page.click('button[type="submit"]');
  await expect(page.locator('body')).toContainText(/dashboard|кабинет|выход|logout/i);

  await page.goto('./logout');
  await expect(page.locator('form')).toBeVisible();
});
