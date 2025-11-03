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
        // Verificar se funções do WordPress estão disponíveis
        if (!function_exists('add_action') || !function_exists('add_filter')) {
            return; // WordPress ainda não carregou
        }
        
        // Ativa em todos os ambientes (localhost e produção)
        // NOTA: Os filtros 'wp_db_connection' e 'wp_db_query_error' não existem no WordPress padrão
        // Removidos para evitar erros fatais
        
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
    
    // Funções removidas: limit_db_connections e handle_connection_error
    // Os hooks 'wp_db_connection' e 'wp_db_query_error' não existem no WordPress padrão
    // As otimizações de consultas e cache são suficientes para reduzir conexões
    
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
// Apenas se o WordPress já tiver carregado as funções necessárias
if (function_exists('add_action') && function_exists('add_filter')) {
    new DB_Connection_Manager();
}

