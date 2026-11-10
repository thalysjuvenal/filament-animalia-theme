import { execSync } from 'node:child_process';
import { defineConfig } from '@playwright/test';

function filamentMajor(): string {
    try {
        const version = execSync(
            `php -r "require 'vendor/autoload.php'; echo Composer\\InstalledVersions::getPrettyVersion('filament/filament');"`,
            { encoding: 'utf8' },
        ).trim();

        return version.replace(/^v/, '').split('.')[0];
    } catch (error) {
        const message = error instanceof Error ? error.message : String(error);

        throw new Error(
            `Could not detect the Filament version - run \`composer install\` first (needs php and vendor/). Original error: ${message}`,
        );
    }
}

const major = filamentMajor();

export default defineConfig({
    testDir: 'tests/Browser',
    globalSetup: './tests/Browser/global-setup.ts',
    fullyParallel: false,
    workers: 1,
    reporter: [['list']],
    snapshotPathTemplate: `tests/Browser/__screenshots__/filament-${major}/{projectName}/{arg}{ext}`,
    expect: {
        toHaveScreenshot: { maxDiffPixels: 50, animations: 'disabled' },
    },
    webServer: {
        command: 'php vendor/bin/testbench serve --host=0.0.0.0 --port=8123',
        url: 'http://127.0.0.1:8123/admin/login',
        reuseExistingServer: false,
        timeout: 120_000,
        env: { PHP_CLI_SERVER_WORKERS: '4' },
    },
    use: {
        baseURL: 'http://127.0.0.1:8123',
        viewport: { width: 1440, height: 900 },
        storageState: 'tests/Browser/.auth/state.json',
    },
    projects: [
        { name: 'light', use: { colorScheme: 'light' } },
        { name: 'dark', use: { colorScheme: 'dark' } },
    ],
});
