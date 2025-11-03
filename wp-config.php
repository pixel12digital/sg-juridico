<?php
define( 'WP_CACHE', true );

// WP_CACHE será definido dinamicamente baseado no ambiente (veja mais abaixo)



/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// Detecção de ambiente local (ANTES das configurações de banco)
$is_localhost = false;
$is_cli = php_sapi_name() === 'cli';

if ($is_cli || !isset($_SERVER['HTTP_HOST'])) {
    $is_localhost = true;
} else {
    $is_localhost = (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
}

// ** Database settings - You can get this info from your web host ** //
// Configuração de banco de dados local vs produção
if ($is_localhost) {
    /** Configuração para desenvolvimento local */
    define( 'DB_NAME', 'u696538442_sgjuridico' );
    define( 'DB_USER', 'u696538442_sgjuridico' );
    define( 'DB_PASSWORD', 'Los@ngo#081081' );
    define( 'DB_HOST', 'srv1310.hstgr.io' );
} else {
    /** Configuração para produção */
    define( 'DB_NAME', 'u696538442_sgjuridico' );
    define( 'DB_USER', 'u696538442_sgjuridico' );
    define( 'DB_PASSWORD', 'Los@ngo#081081' );
    define( 'DB_HOST', 'srv1310.hstgr.io' );
}

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'twXa%KWH.E:D;;s8(]ccXJV&9I]+%vp.=4/!Q:x4f=ksMV i1gpQa39u5R^%lCv=' );
define( 'SECURE_AUTH_KEY',   'L&g((NdYwYAT[jOkk3fMd-vg]e6Ks|_``E{G_6TErh(Ij+ <1;NGlc3Qw=.,G*}N' );
define( 'LOGGED_IN_KEY',     '>Ja(5?ydd`1iR;M%d/d;mVsgD7y;,wXIt^&m((5GF=`kxVtEI1/ZNAD)x#Glx]TZ' );
define( 'NONCE_KEY',         '<k%bxx7>kfpt])lA.QsvF#P@fY`il@aNKm$SN#IAX1.d~?+ !J.iM4/+u!fxYyHQ' );
define( 'AUTH_SALT',         '}z~s/=D@^.oY?M?~nh&UBpr(L(rP&en^fIw3Y{[L/:r{hFR$,_t~VfmY%x<9.5C7' );
define( 'SECURE_AUTH_SALT',  '/P`~It)B1,4de2roLlZ,ETt{J/H(17Mb85.UAhDH1J/Y5W4F)9P`m;i:)76z.vOs' );
define( 'LOGGED_IN_SALT',    'BzRzTrzOqHkVq;R*s%0^3dGL6d7.z-~Q;W>Eq5Z3u/wv::WfU3gYf-n<60m+ehH(' );
define( 'NONCE_SALT',        '?-2gmq*0SV>SEi17)%+`+|z:_Dw:2<qeJ`D,@1wEmd8__Sk`w<rou[n.q8PR1;gg' );
define( 'WP_CACHE_KEY_SALT', 'ktPDZ4&{T1[SshXfB?f~CS2Yb(mrJNT2}on^XCL!{Z S!UwxH~0XHxiC@-&kJ?,E' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */


/* Add any custom values between this line and the "stop editing" line. */

// Configurações para desenvolvimento local
// Sobrescreve URLs do site se acessado via localhost
if ($is_localhost) {
    if (!$is_cli && isset($_SERVER['HTTP_HOST'])) {
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        if (!defined('WP_HOME')) {
            define('WP_HOME', $host);
        }
        if (!defined('WP_SITEURL')) {
            define('WP_SITEURL', $host);
        }
    } elseif ($is_cli) {
        // Em CLI, define URL local padrão
        if (!defined('WP_HOME')) {
            define('WP_HOME', 'http://localhost');
        }
        if (!defined('WP_SITEURL')) {
            define('WP_SITEURL', 'http://localhost');
        }
    }
    
    // Desabilitar HTTPS forçado em localhost (apenas em web)
    if (!$is_cli && isset($_SERVER['HTTPS'])) {
        $_SERVER['HTTPS'] = 'off';
    }
} elseif (!defined('WP_HOME')) {
    // Produção - URLs HTTPS (apenas se não for localhost)
    if (!$is_localhost) {
        define('WP_HOME', 'https://sgjuridico.com.br');
        define('WP_SITEURL', 'https://sgjuridico.com.br');
    }
}

// Garantir URLs corretas de uploads
if (!defined('UPLOADS')) {
    define('UPLOADS', 'wp-content/uploads'); // Caminho relativo para uploads
}

// Forçar URLs de conteúdo corretas
if (!defined('WP_CONTENT_URL')) {
    if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        define('WP_CONTENT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/wp-content');
    } else {
        define('WP_CONTENT_URL', 'https://sgjuridico.com.br/wp-content');
    }
}

define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
// Debug desativado para melhor performance
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );

// ==========================================
// OTIMIZAÇÕES MÁXIMAS DE PERFORMANCE
// ==========================================

// OTIMIZAÇÕES DE BANCO DE DADOS - Reduzir queries
define( 'WP_POST_REVISIONS', 2 ); // Menos revisões = menos queries
define( 'AUTOSAVE_INTERVAL', 300 ); // Auto-save a cada 5 minutos (padrão: 60)
define( 'EMPTY_TRASH_DAYS', 7 ); // Lixeira padrão
define( 'WP_MEMORY_LIMIT', '256M' ); // Memória adequada para local
define( 'WP_MAX_MEMORY_LIMIT', '256M' );

// OTIMIZAÇÕES DE CRON
define( 'WP_CRON_LOCK_TIMEOUT', 60 ); // Reduz timeout
define( 'DISABLE_WP_CRON', false );

// OTIMIZAÇÕES ESPECÍFICAS PARA LOCALHOST
if ($is_localhost) {
    // Desabilita features não essenciais em local para melhor performance
     // Desabilita cache em desenvolvimento
    define( 'COOKIE_DOMAIN', '' ); // Cookies locais
} else {
    // Em produção, habilita cache
    if (!defined('WP_CACHE')) {
        
    }
}

// DESABILITA QUERY SAVING (Causa lentidão com banco remoto)
define( 'SAVEQUERIES', false ); // CRÍTICO: Desabilita monitoramento de queries
define( 'SCRIPT_DEBUG', false ); // Desabilita debug de scripts

// OTIMIZAÇÕES DE CACHE E COMPRESSÃO
define( 'COMPRESS_CSS', true );
define( 'COMPRESS_SCRIPTS', true );
define( 'ENFORCE_GZIP', true );
define( 'CONCATENATE_SCRIPTS', true ); // Junta scripts
define( 'CONCATENATE_CSS', true ); // Junta CSS

// LIMITA AUTOLOAD DE OPÇÕES (Reduz queries na inicialização)
define( 'AUTOLOAD_LIMIT', 50 ); // CRÍTICO: Limita opções autoloaded

// OTIMIZAR TRANSIENTS (Reduz queries de cache)
define( 'WP_TRANSIENT_TIMEOUT', 600 ); // Cache de 10 minutos

// OTIMIZAÇÕES DE REMOTE REQUESTS
define( 'WP_HTTP_BLOCK_EXTERNAL', false );
define( 'WP_ACCESSIBLE_HOSTS', '*' );

// DESABILITA WIDGETS UNUSED (Reduz queries)
define( 'WP_DISABLE_WP_CRON_TIMEOUT', true );

// OTIMIZAÇÕES DE IMAGENS
define( 'IMAGICK_SET_THREAD_LIMIT', 1 ); // Reduz uso de CPU

// Segurança
define( 'DISALLOW_FILE_EDIT', true ); // Desabilita edição de arquivos via admin    

// Force SSL no admin apenas em produção (com SSL configurado)
// Desabilitado em localhost para evitar erros de certificado
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') === false) {
    define( 'FORCE_SSL_ADMIN', true );
}

// ==========================================
// OTIMIZAÇÕES DE CONEXÃO DE BANCO DE DADOS
// ==========================================

// Desabilitar compressão em banco local (causa overhead desnecessário)
if (!$is_localhost) {
    define('DB_COMPRESS', true);
}

// Timeouts otimizados para ambiente local
ini_set('mysql.connect_timeout', '2');
ini_set('default_socket_timeout', '2');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
