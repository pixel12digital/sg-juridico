<?php
/**
 * Script temporário para verificar agendamento de concursos no banco de dados
 * 
 * USO: Acesse http://localhost/sg-juridico/public_html/verificar-agendamento-concursos.php
 * 
 * IMPORTANTE: Deletar este arquivo após uso por segurança!
 */

// Carregar configurações do WordPress
require_once(__DIR__ . '/wp-load.php');

// Verificar se é localhost (segurança)
$is_localhost = (
    (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) ||
    (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) ||
    (!isset($_SERVER['HTTP_HOST']))
);

if (!$is_localhost && (!defined('WP_DEBUG') || !WP_DEBUG)) {
    die('Este script só pode ser executado em localhost ou com WP_DEBUG ativado.');
}

// Conectar ao banco de dados
global $wpdb;

echo '<h1>Verificação de Agendamento de Concursos</h1>';
echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #5CE1E6; color: #000; }
    .highlight { background-color: #ffeb3b; }
</style>';

// 1. Verificar Custom Post Types relacionados
echo '<h2>1. Custom Post Types Registrados</h2>';
$post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
if (empty($post_types)) {
    echo '<p>Nenhum custom post type encontrado.</p>';
} else {
    echo '<table><tr><th>Nome</th><th>Label</th><th>Quantidade de Posts</th></tr>';
    foreach ($post_types as $post_type) {
        $count = wp_count_posts($post_type->name);
        echo '<tr><td>' . esc_html($post_type->name) . '</td>';
        echo '<td>' . esc_html($post_type->label) . '</td>';
        echo '<td>' . esc_html($count->publish) . '</td></tr>';
    }
    echo '</table>';
}

// 2. Procurar posts/páginas com palavras-chave relacionadas
echo '<h2>2. Posts/Páginas com "agendamento" ou "concurso"</h2>';
$posts_query = $wpdb->get_results("
    SELECT ID, post_title, post_type, post_status, post_date
    FROM {$wpdb->posts}
    WHERE (post_title LIKE '%agendamento%' 
           OR post_title LIKE '%concurso%'
           OR post_content LIKE '%agendamento%'
           OR post_content LIKE '%concurso%')
    AND post_status IN ('publish', 'draft', 'pending', 'future')
    ORDER BY post_date DESC
    LIMIT 50
");

if (empty($posts_query)) {
    echo '<p>Nenhum post encontrado com essas palavras-chave.</p>';
} else {
    echo '<table><tr><th>ID</th><th>Título</th><th>Tipo</th><th>Status</th><th>Data</th></tr>';
    foreach ($posts_query as $post) {
        echo '<tr><td>' . esc_html($post->ID) . '</td>';
        echo '<td>' . esc_html($post->post_title) . '</td>';
        echo '<td>' . esc_html($post->post_type) . '</td>';
        echo '<td>' . esc_html($post->post_status) . '</td>';
        echo '<td>' . esc_html($post->post_date) . '</td></tr>';
    }
    echo '</table>';
}

// 3. Procurar Meta Keys relacionadas
echo '<h2>3. Meta Keys relacionadas a agendamento/concursos</h2>';
$meta_keys = $wpdb->get_results("
    SELECT DISTINCT meta_key, COUNT(*) as count
    FROM {$wpdb->postmeta}
    WHERE meta_key LIKE '%agendamento%'
       OR meta_key LIKE '%concurso%'
       OR meta_key LIKE '%agenda%'
       OR meta_key LIKE '%schedule%'
       OR meta_key LIKE '%data_prova%'
       OR meta_key LIKE '%data_exame%'
    GROUP BY meta_key
    ORDER BY count DESC
");

if (empty($meta_keys)) {
    echo '<p>Nenhuma meta key relacionada encontrada.</p>';
} else {
    echo '<table><tr><th>Meta Key</th><th>Quantidade de Registros</th></tr>';
    foreach ($meta_keys as $meta) {
        echo '<tr><td class="highlight">' . esc_html($meta->meta_key) . '</td>';
        echo '<td>' . esc_html($meta->count) . '</td></tr>';
    }
    echo '</table>';
}

// 4. Verificar tabelas customizadas
echo '<h2>4. Tabelas Customizadas no Banco</h2>';
$table_prefix = $wpdb->prefix;
$tables = $wpdb->get_results("SHOW TABLES LIKE '{$table_prefix}%'", ARRAY_N);

$custom_tables = array();
foreach ($tables as $table) {
    $table_name = $table[0];
    // Verificar se contém palavras relacionadas
    if (stripos($table_name, 'agendamento') !== false || 
        stripos($table_name, 'concurso') !== false ||
        stripos($table_name, 'agenda') !== false ||
        stripos($table_name, 'schedule') !== false) {
        $custom_tables[] = $table_name;
    }
}

if (empty($custom_tables)) {
    echo '<p>Nenhuma tabela customizada encontrada com essas palavras-chave.</p>';
} else {
    echo '<table><tr><th>Nome da Tabela</th><th>Estrutura</th></tr>';
    foreach ($custom_tables as $table_name) {
        $columns = $wpdb->get_results("DESCRIBE `{$table_name}`");
        echo '<tr><td class="highlight"><strong>' . esc_html($table_name) . '</strong></td>';
        echo '<td><ul>';
        foreach ($columns as $column) {
            echo '<li>' . esc_html($column->Field) . ' (' . esc_html($column->Type) . ')</li>';
        }
        echo '</ul></td></tr>';
    }
    echo '</table>';
}

// 5. Procurar em todas as meta keys por dados de data/agendamento
echo '<h2>5. Dados de Data/Agendamento em Meta Values</h2>';
$date_metas = $wpdb->get_results("
    SELECT pm.post_id, pm.meta_key, pm.meta_value, p.post_title
    FROM {$wpdb->postmeta} pm
    LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
    WHERE (pm.meta_value LIKE '%2024%' OR pm.meta_value LIKE '%2025%')
    AND (pm.meta_key LIKE '%data%' OR pm.meta_key LIKE '%date%' OR pm.meta_key LIKE '%agend%')
    AND p.post_status IN ('publish', 'draft', 'future')
    LIMIT 50
");

if (empty($date_metas)) {
    echo '<p>Nenhum dado de data/agendamento encontrado em meta values.</p>';
} else {
    echo '<table><tr><th>Post ID</th><th>Título</th><th>Meta Key</th><th>Meta Value</th></tr>';
    foreach ($date_metas as $meta) {
        echo '<tr><td>' . esc_html($meta->post_id) . '</td>';
        echo '<td>' . esc_html($meta->post_title ?: 'Sem título') . '</td>';
        echo '<td class="highlight">' . esc_html($meta->meta_key) . '</td>';
        echo '<td>' . esc_html(substr($meta->meta_value, 0, 100)) . '</td></tr>';
    }
    echo '</table>';
}

echo '<hr>';
echo '<p><strong>Atenção:</strong> Este arquivo deve ser deletado após uso por questões de segurança!</p>';
?>

