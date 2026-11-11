import { chromium, type FullConfig } from '@playwright/test';

export default async function globalSetup(config: FullConfig): Promise<void> {
    const baseURL = config.projects[0].use.baseURL ?? 'http://127.0.0.1:8123';

    const browser = await chromium.launch();
    const context = await browser.newContext({ baseURL });
    const page = await context.newPage();

    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill('demo@example.com');
    await page.locator('input[type="password"]').fill('password');
    await page.locator('button[type="submit"]').click();
    await page.waitForURL('**/admin');

    await context.storageState({ path: 'tests/Browser/.auth/state.json' });
    await browser.close();
}
