/**
 * copyassets.js
 *
 * Script para copiar los assets compilados de build/ a public/css y public/js
 * Esto permite que las vistas Laravel usen los assets directamente sin Vite dev server
 *
 * Uso: node copyassets.js (ejecutar después de npm run build)
 */

import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const BUILD_DIR = path.join(__dirname, 'public', 'build', 'assets');
const PUBLIC_CSS = path.join(__dirname, 'public', 'css');
const PUBLIC_JS = path.join(__dirname, 'public', 'js');

// Colores para la consola
const colors = {
    reset: '\x1b[0m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    red: '\x1b[31m',
    cyan: '\x1b[36m'
};

function log(message, color = 'reset') {
    console.log(`${colors[color]}${message}${colors.reset}`);
}

function ensureDirectoryExists(dir) {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
        log(`📁 Directorio creado: ${dir}`, 'cyan');
    }
}

function findLatestFile(directory, extension) {
    if (!fs.existsSync(directory)) {
        return null;
    }

    const files = fs.readdirSync(directory)
        .filter(file => file.endsWith(extension))
        .map(file => ({
            name: file,
            path: path.join(directory, file),
            mtime: fs.statSync(path.join(directory, file)).mtime
        }))
        .sort((a, b) => b.mtime - a.mtime);

    return files.length > 0 ? files[0] : null;
}

function copyFile(source, destination) {
    try {
        fs.copyFileSync(source, destination);
        return true;
    } catch (error) {
        log(`❌ Error copiando ${source}: ${error.message}`, 'red');
        return false;
    }
}

function main() {
    log('\n🚀 SGDEA - Copy Assets Script', 'cyan');
    log('================================\n', 'cyan');

    // Verificar que existe el directorio de build
    if (!fs.existsSync(BUILD_DIR)) {
        log('❌ No se encontró el directorio build/assets', 'red');
        log('   Ejecuta primero: npm run build', 'yellow');
        process.exit(1);
    }

    // Crear directorios destino si no existen
    ensureDirectoryExists(PUBLIC_CSS);
    ensureDirectoryExists(PUBLIC_JS);

    let success = true;

    // Buscar y copiar archivo CSS
    const cssFile = findLatestFile(BUILD_DIR, '.css');
    if (cssFile) {
        const destCSS = path.join(PUBLIC_CSS, 'app.css');
        if (copyFile(cssFile.path, destCSS)) {
            log(`✅ CSS copiado: ${cssFile.name} -> public/css/app.css`, 'green');
        } else {
            success = false;
        }
    } else {
        log('⚠️  No se encontró archivo CSS en build/assets', 'yellow');
    }

    // Buscar y copiar archivo JS
    const jsFile = findLatestFile(BUILD_DIR, '.js');
    if (jsFile) {
        const destJS = path.join(PUBLIC_JS, 'app.js');
        if (copyFile(jsFile.path, destJS)) {
            log(`✅ JS copiado: ${jsFile.name} -> public/js/app.js`, 'green');
        } else {
            success = false;
        }
    } else {
        log('⚠️  No se encontró archivo JS en build/assets', 'yellow');
    }

    // Resumen
    console.log('');
    if (success) {
        log('✨ Assets copiados exitosamente!', 'green');
        log('\nAhora puedes usar en tus vistas:', 'cyan');
        log('  <link rel="stylesheet" href="{{ asset(\'css/app.css\') }}">', 'reset');
        log('  <script src="{{ asset(\'js/app.js\') }}" defer></script>', 'reset');
    } else {
        log('⚠️  Algunos archivos no pudieron copiarse', 'yellow');
        process.exit(1);
    }

    console.log('');
}

main();
