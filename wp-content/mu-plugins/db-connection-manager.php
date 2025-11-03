<?php
/**
 * Plugin Name: Database Connection Manager
 * Description: Gerencia e limita conexões ao banco de dados remoto
 * Version: 1.0.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class DB_Connection_Manager {
    
    private static $connection_attempts = 0;
    private static $max_attempts_per_minute = 10;
    private static $last_attempt_time = 0;
    private static $connection_cache = null;
    
    public function __construct() {
        // Ativa em todos os ambientes (localhost e produção)
        // Filtra a classe wpdb para limitar conexões
        add_filter('wp_db_connection', array($this, 'limit_db_connections'), 10, 4);
        
        // Intercepta erros de conexão
        add_action('wp_db_query_error', array($this, 'handle_connection_error'), 10, 2);
        
        // Desabilita queries desnecessárias
        add_action('init', array($this, 'reduce_database_queries'), 1);
        
        // Cache agressivo de objetos
        add_action('init', array($this, 'enable_object_cache'), 1);
        
        // Garante que conexões sejam fechadas adequadamente
        add_action('shutdown', array($this, 'close_db_connections'), 999);
    }
    
    /**
     * Reduz queries ao banco de dados
     */
    public function reduce_database_queries() {
        // Desabilita queries de transients excessivas
        if (!defined('WP_CACHE')) {
            define('WP_CACHE', false);
        }
        
        // Desabilita heartbeat (evita queries a cada 15 segundos)
        wp_deregister_script('heartbeat');
        
        // Limita queries de autoload
        add_filter('pre_update_option', array($this, 'prevent_unnecessary_options'), 10, 3);
    }
    
    /**
     * Previne atualizações desnecessárias de opções
     */
    public function prevent_unnecessary_options($value, $option, $old_value) {
        // Não atualiza se o valor for o mesmo
        if ($value === $old_value) {
            return false;
        }
        return $value;
    }
    
    /**
     * Limita tentativas de conexão
     */
    public function limit_db_connections($connection, $dbuser, $dbpassword, $dbname) {
        $current_time = time();
        
        // Reset contador a cada minuto
        if ($current_time - self::$last_attempt_time > 60) {
            self::$connection_attempts = 0;
            self::$last_attempt_time = $current_time;
        }
        
        // Se excedeu limite, aguarda
        if (self::$connection_attempts >= self::$max_attempts_per_minute) {
            $wait_time = 60 - ($current_time - self::$last_attempt_time);
            if ($wait_time > 0) {
                sleep(min($wait_time, 5)); // Máximo 5 segundos de espera
            }
            self::$connection_attempts = 0;
        }
        
        self::$connection_attempts++;
        self::$last_attempt_time = $current_time;
        
        return $connection;
    }
    
    /**
     * Trata erros de conexão
     */
    public function handle_connection_error($error, $query) {
        // Se for erro de limite de conexões, aguarda antes de tentar novamente
        if (strpos($error, 'max_connections_per_hour') !== false) {
            error_log('DB Connection Manager: Limite de conexões excedido. Aguardando...');
            sleep(2); // Aguarda 2 segundos antes de permitir nova tentativa
        }
    }
    
    /**
     * Habilita cache de objetos
     */
    public function enable_object_cache() {
        // Tenta usar cache de arquivos se disponível
        if (!wp_using_ext_object_cache()) {
            // Cache simples em memória (apenas para esta requisição)
            add_filter('pre_get_option', array($this, 'cache_options'), 10, 2);
        }
    }
    
    /**
     * Cache simples de opções em memória
     */
    private static $options_cache = array();
    
    public function cache_options($pre_option, $option) {
        // Cache apenas para opções comuns
        $cacheable = array('home', 'siteurl', 'blogname', 'blogdescription', 'active_plugins');
        
        if (in_array($option, $cacheable)) {
            if (isset(self::$options_cache[$option])) {
                return self::$options_cache[$option];
            }
        }
        
        return $pre_option;
    }
    
    /**
     * Fecha conexões persistentes do banco de dados ao finalizar a requisição
     * Nota: O WordPress gerencia suas próprias conexões, mas podemos garantir
     * que não deixemos conexões persistentes abertas desnecessariamente
     */
    public function close_db_connections() {
        global $wpdb;
        
        // Não forçamos fechamento da conexão principal do WordPress
        // pois ele gerencia isso automaticamente
        // Apenas garantimos que não há conexões extras persistentes
        
        // Limpar qualquer conexão cacheada que possamos ter criado
        self::$connection_cache = null;
        
        // O WordPress fecha automaticamente as conexões no shutdown,
        // então não precisamos fazer nada além de limpar nosso cache
    }
}

// Inicializa em todos os ambientes para otimizar conexões
new DB_Connection_Manager();

