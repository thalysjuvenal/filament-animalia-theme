import { expect, test, type Page } from '@playwright/test';

const panels = ['admin', 'light', 'indigo'];
const pages: Array<[string, string]> = [
    ['', 'dashboard'],
    ['kitchen-sink', 'kitchen-sink'],
    ['users', 'users'],
];

let pageErrors: Error[] = [];

test.beforeEach(({ page }) => {
    pageErrors = [];
    page.on('pageerror', (error) => pageErrors.push(error));
});

test.afterEach(() => {
    expect(pageErrors, 'uncaught page errors').toEqual([]);
});

async function settle(page: Page): Promise<void> {
    await page.waitForLoadState('networkidle');
    await page.evaluate(() => document.fonts.ready);
    await page.waitForTimeout(300);
}

for (const panel of panels) {
    for (const [path, name] of pages) {
        test(`${panel} ${name}`, async ({ page }) => {
            await page.goto(`/${panel}${path ? `/${path}` : ''}`);
            await settle(page);

            await expect(page).toHaveScreenshot(`${panel}-${name}.png`, {
                fullPage: true,
                mask: [page.locator('.fi-avatar')],
            });
        });
    }
}

test.describe('admin overlays', () => {
    test('login', async ({ browser, baseURL, colorScheme }) => {
        const context = await browser.newContext({
            baseURL,
            storageState: { cookies: [], origins: [] },
            viewport: { width: 1440, height: 900 },
            colorScheme,
        });
        const page = await context.newPage();
        page.on('pageerror', (error) => pageErrors.push(error));

        await page.goto('/admin/login');
        await settle(page);

        await expect(page).toHaveScreenshot('admin-login.png', { fullPage: true });
        await context.close();
    });

    test('modal', async ({ page }) => {
        await page.goto('/admin/kitchen-sink');
        await settle(page);
        await page.getByRole('button', { name: 'Abrir modal' }).click();

        const modal = page.locator('.fi-modal-window');
        await expect(modal).toBeVisible();
        await settle(page);

        await expect(modal).toHaveScreenshot('admin-modal.png');
    });

    test('notification', async ({ page }) => {
        await page.goto('/admin/kitchen-sink');
        await settle(page);
        await page.getByRole('button', { name: 'Notificar' }).click();

        const notification = page.locator('.fi-no-notification').first();
        await expect(notification).toBeVisible();
        await settle(page);

        await expect(notification).toHaveScreenshot('admin-notification.png');
    });

    test('dropdown', async ({ page }) => {
        await page.goto('/admin/kitchen-sink');
        await settle(page);
        await page.locator('main .fi-dropdown-trigger').first().click();

        const panel = page.locator('main .fi-dropdown-panel:visible').first();
        await expect(panel).toBeVisible();
        await settle(page);

        await expect(panel).toHaveScreenshot('admin-dropdown.png');
    });
});
