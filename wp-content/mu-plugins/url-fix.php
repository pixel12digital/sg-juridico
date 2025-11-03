<?php
/**
 * Plugin Name: URL Fix Helper
 * Description: Corrige URLs automaticamente para dev e produção
 * Version: 1.0.0
 */

// Previne acesso direto
if (!defined('ABSPATH')) {
    exit;
}

class URL_Fix_Helper {
    
    private $production_url = 'https://sgjuridico.com.br';
    private $local_url = '';
    private $is_local = false;
    
    public function __construct() {
        // Detecta ambiente
        $this->detect_environment();
        
        // Verificar se funções do WordPress estão disponíveis
        if (!function_exists('add_action') || !function_exists('add_filter')) {
            return; // WordPress ainda não carregou
        }
        
        // SEMPRE aplica filtros (híbrido - funciona em produção e local)
        // Hook no momento certo para atualizar URLs
        add_action('init', array($this, 'fix_urls'), 1);
        add_filter('option_home', array($this, 'fix_url_option'));
        add_filter('option_siteurl', array($this, 'fix_url_option'));
        add_filter('the_content', array($this, 'fix_content_urls'));
        add_filter('the_excerpt', array($this, 'fix_content_urls'));
        
        // Força URLs corretas ignorando constantes - PRIORIDADE MÁXIMA (999)
        add_filter('site_url', array($this, 'force_correct_url'), 999);
        add_filter('home_url', array($this, 'force_correct_url'), 999);
        add_filter('network_site_url', array($this, 'force_correct_url'), 999);
        add_filter('network_home_url', array($this, 'force_correct_url'), 999);
        add_filter('admin_url', array($this, 'force_correct_url'), 999);
        add_filter('includes_url', array($this, 'force_correct_url'), 999);
        add_filter('content_url', array($this, 'force_correct_url'), 999);
        add_filter('plugins_url', array($this, 'force_correct_url'), 999);
        add_filter('theme_root_uri', array($this, 'force_correct_url'), 999);
        add_filter('stylesheet_uri', array($this, 'force_correct_url'), 999);
        add_filter('stylesheet_directory_uri', array($this, 'force_correct_url'), 999);
        add_filter('template_directory_uri', array($this, 'force_correct_url'), 999);
        add_filter('template_uri', array($this, 'force_correct_url'), 999);
        
        // Filtro adicional para garantir URLs corretas em wp_enqueue_style/wp_enqueue_script
        add_filter('style_loader_src', array($this, 'force_correct_url'), 999);
        add_filter('script_loader_src', array($this, 'force_correct_url'), 999);
        
        // Filtra permalinks
        add_filter('post_link', array($this, 'force_correct_url'), 999);
        add_filter('page_link', array($this, 'force_correct_url'), 999);
        add_filter('attachment_link', array($this, 'force_correct_url'), 999);
        add_filter('post_type_link', array($this, 'force_correct_url'), 999);
        
        // FILTROS ESPECÍFICOS PARA IMAGENS E ANEXOS - CRÍTICO!
        add_filter('wp_get_attachment_url', array($this, 'force_correct_url'), 999);
        add_filter('wp_get_attachment_image_src', array($this, 'fix_attachment_image_src'), 999, 4);
        add_filter('wp_calculate_image_srcset', array($this, 'fix_image_srcset'), 999, 5);
        
        // Corrige URLs em metadados de posts
        add_filter('get_post_metadata', array($this, 'fix_post_meta_urls'), 999, 4);
        
        // Filtra URLs em JSON (para Elementor)
        add_filter('wp_json_encode', array($this, 'fix_json_urls'), 10, 2);
        
        // Adiciona JavaScript para corrigir URLs no front-end
        add_action('wp_footer', array($this, 'add_frontend_js'));
        
        // OUTPUT BUFFERING: Intercepta todo o HTML antes de ser enviado
        add_action('template_redirect', array($this, 'start_output_buffering'), 1);
        
        // Filtro de saída final - última oportunidade antes de enviar para o navegador
        add_filter('final_output', array($this, 'fix_final_output'), 999);
    }
    
    /**
     * Detecta o ambiente automaticamente
     */
    private function detect_environment() {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $this->is_local = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
        
        // Se for local, define a URL local correta baseada no HOST
        if ($this->is_local && isset($_SERVER['HTTP_HOST'])) {
            $this->local_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sg-juridico';
        }
    }
    
    /**
     * Adiciona JavaScript para corrigir URLs no front-end (HÍBRIDO)
     */
    public function add_frontend_js() {
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $is_local = (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false);
        $local_url = $is_local ? ('http://' . $current_host . '/sg-juridico') : '';
        ?>
        <script>
        (function() {
            'use strict';
            
            const productionUrl = '<?php echo esc_js($this->production_url); ?>';
            const localUrl = '<?php echo esc_js($local_url); ?>';
            const isLocal = <?php echo $is_local ? 'true' : 'false'; ?>;
            
            // Função para corrigir URLs (híbrida)
            function fixUrl(url) {
                if (!url || typeof url !== 'string') return url;
                
                if (isLocal) {
                    // PRIORIDADE: Se está em local, substitui produção por local (especialmente uploads)
                    if (url.indexOf(productionUrl + '/wp-content/uploads/') === 0) {
                        return url.replace(productionUrl, localUrl).replace('https://', 'http://');
                    }
                    // Outras URLs de produção
                    if (url.indexOf(productionUrl) !== -1) {
                        return url.replace(productionUrl, localUrl).replace('https://', 'http://');
                    }
                } else {
                    // Se está em produção, substitui local por produção
                    if (url.indexOf('localhost') !== -1 || url.indexOf('127.0.0.1') !== -1) {
                        return url.replace(/http:\/\/[^/]+\/sg-juridico/g, productionUrl);
                    }
                }
                return url;
            }
            
            // Corrige todos os links da página
            function fixAllLinks() {
                // Todos os links
                document.querySelectorAll('a[href]').forEach(function(link) {
                    const originalHref = link.getAttribute('href');
                    if (originalHref) {
                        const fixed = fixUrl(originalHref);
                        if (fixed !== originalHref) {
                            link.setAttribute('href', fixed);
                        }
                    }
                });
                
                // Imagens - PRIORIDADE: corrige todas, inclusive com data-src (lazy loading)
                document.querySelectorAll('img').forEach(function(img) {
                    // Corrige src principal
                    const originalSrc = img.getAttribute('src');
                    if (originalSrc) {
                        const fixed = fixUrl(originalSrc);
                        if (fixed !== originalSrc) {
                            img.setAttribute('src', fixed);
                        }
                    }
                    // Corrige data-src (lazy loading)
                    const dataSrc = img.getAttribute('data-src');
                    if (dataSrc) {
                        const fixed = fixUrl(dataSrc);
                        if (fixed !== dataSrc) {
                            img.setAttribute('data-src', fixed);
                        }
                    }
                    // Corrige data-lazy-src
                    const dataLazySrc = img.getAttribute('data-lazy-src');
                    if (dataLazySrc) {
                        const fixed = fixUrl(dataLazySrc);
                        if (fixed !== dataLazySrc) {
                            img.setAttribute('data-lazy-src', fixed);
                        }
                    }
                    
                    // Corrige srcset também
                    const srcset = img.getAttribute('srcset');
                    if (srcset) {
                        const fixedSrcset = srcset.split(',').map(function(item) {
                            const parts = item.trim().split(' ');
                            if (parts[0]) {
                                return fixUrl(parts[0]) + (parts[1] ? ' ' + parts[1] : '');
                            }
                            return item;
                        }).join(', ');
                        if (fixedSrcset !== srcset) {
                            img.setAttribute('srcset', fixedSrcset);
                        }
                    }
                });
                
                // Background images em elementos com estilo inline
                document.querySelectorAll('[style*="background-image"]').forEach(function(el) {
                    const style = el.getAttribute('style');
                    if (style) {
                        const fixed = style.replace(/url\(['"]?([^'")]+)['"]?\)/gi, function(match, url) {
                            return 'url(' + fixUrl(url) + ')';
                        });
                        if (fixed !== style) {
                            el.setAttribute('style', fixed);
                        }
                    }
                });
                
                // Formulários
                document.querySelectorAll('form[action]').forEach(function(form) {
                    const originalAction = form.getAttribute('action');
                    if (originalAction) {
                        const fixed = fixUrl(originalAction);
                        if (fixed !== originalAction) {
                            form.setAttribute('action', fixed);
                        }
                    }
                });
            }
            
            // Executa imediatamente
            fixAllLinks();
            
            // Observa mudanças no DOM (para Elementor dinâmico)
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        fixAllLinks();
                    }
                });
            });
            
            // Inicia observação
            if (document.body) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    attributeFilter: ['src', 'href', 'srcset', 'style']
                });
            }
            
            // Executa após carregar completamente
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', fixAllLinks);
            } else {
                setTimeout(fixAllLinks, 100);
            }
            
            // Executa após um pouco de tempo (para Elementor e carregamento dinâmico)
            setTimeout(fixAllLinks, 500);
            setTimeout(fixAllLinks, 1000);
            setTimeout(fixAllLinks, 2000);

            // Corrige URLs de produção que já estejam no DOM (executa imediatamente)
            if (isLocal) {
                // Força correção de todas as imagens que já estão com URL de produção
                document.querySelectorAll('img[src*="sgjuridico.com.br"]').forEach(function(img) {
                    const src = img.getAttribute('src');
                    if (src && src.indexOf(productionUrl) === 0) {
                        img.setAttribute('src', src.replace(productionUrl, localUrl).replace('https://', 'http://'));
                    }
                });
            }
            
            // Fallback de imagem: se falhar em local, tenta mesma URL na produção
            if (isLocal) {
                document.addEventListener('error', function(e){
                    var el = e.target;
                    if (el && el.tagName === 'IMG') {
                        var src = el.getAttribute('src') || '';
                        if (src.indexOf(localUrl + '/wp-content/uploads/') === 0) {
                            el.setAttribute('src', src.replace(localUrl, productionUrl));
                        } else if (src.indexOf('/wp-content/uploads/') === 0) {
                            el.setAttribute('src', productionUrl + src);
                        }
                    }
                }, true);
            }

        })();
        </script>
        <?php
    }
    
    /**
     * Corrige URLs em respostas JSON
     */
    public function fix_json_urls($data, $flags = 0) {
        if (is_string($data)) {
            $data = $this->fix_content_urls($data);
        }
        return $data;
    }
    
    /**
     * Força URL correta mesmo com constantes definidas
     * FUNCIONA EM AMBOS OS AMBIENTES (HÍBRIDO)
     */
    public function force_correct_url($url) {
        if (empty($url) || !is_string($url)) {
            return $url;
        }
        
        // Debug em localhost (comentar depois)
        // error_log('URL original: ' . $url);
        
        // Detecta ambiente dinamicamente
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $is_local = (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false);
        
        // Corrige caminhos raiz relativos de uploads: /wp-content/uploads -> base + caminho
        if (strpos($url, '/wp-content/uploads') === 0) {
            $base = ($is_local ? ('http://' . $current_host . '/sg-juridico') : 'https://sgjuridico.com.br');
            $absolute = $base . $url;
            
            // Se arquivo não existir localmente, faz fallback para produção
            $relative_path = str_replace('/wp-content/uploads', 'wp-content/uploads', $url);
            $fs_path = trailingslashit( ABSPATH ) . ltrim( $relative_path, '/' );
            if (!file_exists($fs_path)) {
                return 'https://sgjuridico.com.br' . $url; // fallback serve da produção
            }
            return $absolute;
        }

        if ($is_local) {
            $local_url = 'http://' . $current_host . '/sg-juridico';
            
            // PRIORIDADE 1: Se a URL contém sgjuridico.com.br, substitui imediatamente
            if (strpos($url, 'sgjuridico.com.br') !== false) {
                $url = str_replace('https://sgjuridico.com.br', $local_url, $url);
                $url = str_replace('http://sgjuridico.com.br', $local_url, $url);
                // Remove HTTPS forçado em localhost
                $url = str_replace('https://localhost', 'http://localhost', $url);
                $url = str_replace('https://127.0.0.1', 'http://127.0.0.1', $url);
                return $url; // Retorna imediatamente após substituição
            }
            
            // PRIORIDADE 2: URLs de wp-content (themes, plugins, uploads) - SEMPRE corrige
            if (strpos($url, '/wp-content/') !== false) {
                // Se é uma URL relativa que começa com /wp-content, transforma em absoluta
                if (strpos($url, '/wp-content/') === 0) {
                    $url = $local_url . $url;
                    return $url; // Retorna imediatamente
                }
                // Se não tem protocolo, adiciona a URL local
                elseif (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
                    $url = $local_url . '/' . ltrim($url, '/');
                    return $url; // Retorna imediatamente
                }
                // Se já tem protocolo mas é de produção, substitui
                elseif (strpos($url, 'https://sgjuridico.com.br') === 0 || strpos($url, 'http://sgjuridico.com.br') === 0) {
                    $url = str_replace('https://sgjuridico.com.br', $local_url, $url);
                    $url = str_replace('http://sgjuridico.com.br', $local_url, $url);
                    return $url; // Retorna imediatamente
                }
            }
            
            // PRIORIDADE 3: URLs que começam apenas com / mas não são wp-content
            // (podem ser URLs absolutas que o WordPress gerou incorretamente)
            if (strpos($url, '/') === 0 && strpos($url, '//') !== 0 && strpos($url, '/wp-content/') === false) {
                // Se não tem protocolo e começa com /, é um caminho absoluto - adiciona o domínio local
                if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {
                    // Verifica se é um caminho de tema/arquivo que existe localmente
                    $test_path = trailingslashit( ABSPATH ) . ltrim( $url, '/' );
                    if (file_exists($test_path) || strpos($url, '/wp-content/themes/') === 0 || strpos($url, '/wp-content/plugins/') === 0) {
                        $url = $local_url . $url;
                        return $url;
                    }
                }
            }
            
            // PRIORIDADE 4: Substitui URL de produção por local (uploads específicos)
            if (strpos($url, 'https://sgjuridico.com.br/wp-content/uploads/') === 0 || strpos($url, 'http://sgjuridico.com.br/wp-content/uploads/') === 0) {
                $url_path = str_replace('https://sgjuridico.com.br', '', $url);
                $url_path = str_replace('http://sgjuridico.com.br', '', $url_path);
                $url = $local_url . $url_path;
            }
            
            // Fallback: se for uma URL de uploads local e arquivo não existir, usa produção
            if (strpos($url, $local_url . '/wp-content/uploads') === 0) {
                $relative = str_replace($local_url . '/', '', $url);
                $fs_path = trailingslashit( ABSPATH ) . $relative;
                if (!file_exists($fs_path)) {
                    $url = str_replace($local_url, 'https://sgjuridico.com.br', $url);
                }
            }
            
            // Remove HTTPS forçado em localhost
            $url = str_replace('https://localhost', 'http://localhost', $url);
            $url = str_replace('https://127.0.0.1', 'http://127.0.0.1', $url);
        } else {
            // Se estiver em produção, substitui localhost por produção
            if (strpos($url, 'localhost') !== false || strpos($url, '127.0.0.1') !== false) {
                $url = str_replace('http://localhost/sg-juridico', $this->production_url, $url);
                $url = str_replace('http://127.0.0.1/sg-juridico', $this->production_url, $url);
            }
        }
        
        return $url;
    }
    
    /**
     * Corrige URLs em wp_get_attachment_image_src
     */
    public function fix_attachment_image_src($image, $attachment_id, $size, $icon) {
        if (!is_array($image) || empty($image[0])) {
            return $image;
        }
        
        $image[0] = $this->force_correct_url($image[0]);
        return $image;
    }
    
    /**
     * Corrige URLs em srcset de imagens
     */
    public function fix_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
        if (!is_array($sources)) {
            return $sources;
        }
        
        foreach ($sources as &$source) {
            if (isset($source['url'])) {
                $source['url'] = $this->force_correct_url($source['url']);
            }
        }
        
        return $sources;
    }
    
    /**
     * Corrige URLs em metadados de posts (onde podem estar URLs de imagens)
     */
    public function fix_post_meta_urls($value, $object_id, $meta_key, $single) {
        // Não interfere se não for uma meta key que contém URLs
        // Aplicamos apenas se o valor for uma string que parece ser uma URL
        if ($value === null) {
            return $value;
        }
        
        if (is_string($value)) {
            // Verifica se parece ser uma URL
            if (strpos($value, 'http://') !== false || strpos($value, 'https://') !== false || 
                strpos($value, '//') === 0 || strpos($value, '/wp-content/uploads/') !== false) {
                return $this->force_correct_url($value);
            }
        } elseif (is_array($value)) {
            // Processa arrays recursivamente
            foreach ($value as &$item) {
                if (is_string($item)) {
                    if (strpos($item, 'http://') !== false || strpos($item, 'https://') !== false || 
                        strpos($item, '//') === 0 || strpos($item, '/wp-content/uploads/') !== false) {
                        $item = $this->force_correct_url($item);
                    }
                }
            }
            return $value;
        }
        
        return $value;
    }
    
    /**
     * Corrige URLs das opções do WordPress (HÍBRIDO)
     */
    public function fix_url_option($value) {
        if (empty($value) || !is_string($value)) {
            return $value;
        }
        
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $is_local = (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false);
        
        if ($is_local) {
            $local_url = 'http://' . $current_host . '/sg-juridico';
            // Se a URL atual contém o domínio de produção, substitui
            if (strpos($value, 'sgjuridico.com.br') !== false) {
                return $local_url;
            }
        } else {
            // Se estiver em produção e URL contém localhost, substitui
            if (strpos($value, 'localhost') !== false || strpos($value, '127.0.0.1') !== false) {
                return $this->production_url;
            }
        }
        
        return $value;
    }
    
    /**
     * Corrige URLs no conteúdo (HÍBRIDO - funciona em ambos os ambientes)
     */
    public function fix_content_urls($content) {
        if (empty($content) || !is_string($content)) {
            return $content;
        }
        
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $is_local = (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false);
        
        if ($is_local) {
            $production_url = 'https://sgjuridico.com.br';
            $local_url = 'http://' . $current_host . '/sg-juridico';
            
            // PRIORIDADE: Substitui URLs absolutas de produção por local no conteúdo HTML
            // Primeiro substitui URLs de uploads especificamente
            $content = preg_replace(
                '#(src|href|data-src|data-lazy-src)=["\']https?://sgjuridico\.com\.br(/wp-content/uploads[^"\']*)["\']#i',
                '$1="' . $local_url . '$2"',
                $content
            );
            // Depois substitui outras URLs de produção
            $content = str_replace($production_url, $local_url, $content);
            $content = str_replace('http://sgjuridico.com.br', $local_url, $content);
            // Prefixa caminhos raiz de uploads
            $content = preg_replace('#(src|href)=["\'](/wp-content/uploads[^"\']*)["\']#i', '$1="' . $local_url . '$2"', $content);
            // Fallback: se arquivo não existir localmente, aponta para produção
            $content = preg_replace_callback('#(src|href)=["\'](' . preg_quote($local_url, '#') . '/wp-content/uploads[^"\']*)["\']#i', function($m) use ($local_url) {
                $url = $m[2];
                $relative = str_replace($local_url . '/', '', $url);
                $fs = trailingslashit( ABSPATH ) . $relative;
                if (!file_exists($fs)) {
                    $url = str_replace($local_url, 'https://sgjuridico.com.br', $url);
                }
                return $m[1] . '="' . $url . '"';
            }, $content);
        } else {
            // Em produção, substitui URLs locais
            $content = preg_replace('#http://(localhost|127\.0\.0\.1)[^"\'<>\s]+#i', $this->production_url . '$1', $content);
            // Prefixa caminhos raiz de uploads
            $content = preg_replace('#(src|href)=["\'](/wp-content/uploads[^"\']*)["\']#i', '$1="' . $this->production_url . '$2"', $content);
        }
        
        return $content;
    }
    
    /**
     * Atualiza URLs no banco de dados quando necessário
     */
    public function fix_urls() {
        // Só executa em admin ou se for uma requisição AJAX do frontend
        if (!is_admin() && !wp_doing_ajax()) {
            return;
        }
        
        // Verifica se já foi executado nesta sessão
        if (isset($_SESSION['url_fix_applied'])) {
            return;
        }
        
        $is_local = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
        
        if ($is_local) {
            $this->update_local_urls();
        } else {
            $this->update_production_urls();
        }
        
        // Marca como executado
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['url_fix_applied'] = true;
    }
    
    /**
     * Atualiza URLs para ambiente local
     */
    private function update_local_urls() {
        global $wpdb;
        
        // URLs alvo
        $local_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sg-juridico';
        $production_url = 'https://sgjuridico.com.br';
        
        // Verifica se precisa atualizar
        $current_url = get_option('home');
        
        if (strpos($current_url, 'localhost') === false && strpos($current_url, $_SERVER['HTTP_HOST']) === false) {
            // Não atualiza, apenas força via filtro
            return;
        }
        
        // Atualiza opções principais
        if (strpos($current_url, $production_url) !== false) {
            update_option('home', $local_url);
            update_option('siteurl', $local_url);
        }
    }
    
    /**
     * Inicia output buffering para interceptar todo HTML
     */
    public function start_output_buffering() {
        if (!is_admin() && !wp_doing_ajax()) {
            if (!ob_get_level()) {
                ob_start();
            }
            // Obtém o conteúdo do buffer ao final
            add_action('shutdown', function() {
                if (ob_get_level()) {
                    $content = ob_get_clean();
                    echo $this->fix_output_buffer($content);
                }
            }, 999);
        }
    }
    
    /**
     * Corrige URLs no buffer de saída
     */
    public function fix_output_buffer($buffer) {
        if (empty($buffer) || !is_string($buffer)) {
            return $buffer;
        }
        
        $current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        $is_local = (strpos($current_host, 'localhost') !== false || strpos($current_host, '127.0.0.1') !== false);
        
        if ($is_local) {
            $local_url = 'http://' . $current_host . '/sg-juridico';
            $production_url = 'https://sgjuridico.com.br';
            
            // PRIORIDADE 1: Substitui URLs absolutas de uploads com regex mais agressivo
            $buffer = preg_replace_callback(
                '#(src|href|data-src|data-lazy-src|data-original|data-thumb)=["\']https?://sgjuridico\.com\.br(/wp-content/uploads[^"\']*)["\']#i',
                function($matches) use ($local_url) {
                    return $matches[1] . '="' . $local_url . $matches[2] . '"';
                },
                $buffer
            );
            
            // PRIORIDADE 2: Substitui URLs de temas e plugins (CRÍTICO para CSS/JS)
            $buffer = preg_replace_callback(
                '#(href|src)=["\']https?://sgjuridico\.com\.br(/wp-content/(themes|plugins)/[^"\']*)["\']#i',
                function($matches) use ($local_url) {
                    return $matches[1] . '="' . $local_url . $matches[2] . '"';
                },
                $buffer
            );
            
            // PRIORIDADE 3: Substitui todas ocorrências de produção por local
            $buffer = str_replace($production_url . '/wp-content/uploads/', $local_url . '/wp-content/uploads/', $buffer);
            $buffer = str_replace('https://sgjuridico.com.br', $local_url, $buffer);
            $buffer = str_replace('http://sgjuridico.com.br', $local_url, $buffer);
            
            // PRIORIDADE 3: Corrige URLs em estilo inline (background-image)
            $buffer = preg_replace_callback(
                '#style=["\']([^"\']*)background-image:\s*url\(https?://sgjuridico\.com\.br(/wp-content/uploads[^)]+)\)#i',
                function($matches) use ($local_url) {
                    return 'style="' . str_replace('https://sgjuridico.com.br' . $matches[2], $local_url . $matches[2], $matches[1]) . 'background-image: url(' . $local_url . $matches[2] . ')';
                },
                $buffer
            );
        }
        
        return $buffer;
    }
    
    /**
     * Filtro de saída final (backup)
     */
    public function fix_final_output($output) {
        return $this->fix_output_buffer($output);
    }
    
    /**
     * Atualiza URLs para ambiente de produção
     */
    private function update_production_urls() {
        global $wpdb;
        
        $production_url = 'https://sgjuridico.com.br';
        $local_url_pattern = 'http://localhost';
        
        // Atualiza opções principais
        $current_url = get_option('home');
        
        if (strpos($current_url, 'localhost') !== false) {
            update_option('home', $production_url);
            update_option('siteurl', $production_url);
        }
    }
}

// Inicializa o helper apenas quando WordPress estiver pronto
if (function_exists('add_action')) {
    // Instanciar quando WordPress estiver pronto
    add_action('plugins_loaded', function() {
        if (class_exists('URL_Fix_Helper')) {
            new URL_Fix_Helper();
        }
    }, 1);
} else {
    // Se WordPress ainda não carregou, tentar instanciar depois
    // Mas isso não deve acontecer em mu-plugins
}

// Função auxiliar para forçar correção de URLs
function force_fix_wordpress_urls() {
    global $wpdb;
    
    $is_local = isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false;
    
    if ($is_local) {
        $new_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sg-juridico';
    } else {
        $new_url = 'https://sgjuridico.com.br';
    }
    
    // Atualiza as opções de URL
    update_option('siteurl', $new_url);
    update_option('home', $new_url);
    
    // Atualiza URLs no banco de dados
    $wpdb->query("UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, 'https://sgjuridico.com.br', '{$new_url}') WHERE option_value LIKE '%https://sgjuridico.com.br%'");
    $wpdb->query("UPDATE {$wpdb->options} SET option_value = REPLACE(option_value, 'http://localhost', '{$new_url}') WHERE option_value LIKE '%http://localhost%'");
}
