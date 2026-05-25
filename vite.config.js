import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { existsSync, readFileSync, writeFileSync } from 'node:fs';
import { resolve } from 'node:path';

function normalizeLaravelManifest() {
    return {
        name: 'normalize-laravel-manifest',
        apply: 'build',
        closeBundle() {
            const manifestPath = resolve('public/build/manifest.json');

            if (!existsSync(manifestPath)) {
                return;
            }

            const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));
            const normalized = {};

            for (const [key, value] of Object.entries(manifest)) {
                const normalizedKey = normalizeResourcePath(key);
                normalized[normalizedKey] = {
                    ...value,
                    src: value.src ? normalizeResourcePath(value.src) : value.src,
                };
            }

            writeFileSync(manifestPath, `${JSON.stringify(normalized, null, 2)}\n`);
        },
    };
}

function normalizeResourcePath(path) {
    const normalized = path.replaceAll('\\', '/');
    const resourcesIndex = normalized.lastIndexOf('resources/');

    return resourcesIndex === -1 ? normalized : normalized.slice(resourcesIndex);
}

export default defineConfig({
    server: {
        cors: true,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        normalizeLaravelManifest(),
    ],
});
