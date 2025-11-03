<?php
/**
 * Script para verificar status dos uploads
 * 
 * Este script mostra quais arquivos existem localmente
 * e ajuda a preparar para upload manual
 */

require_once 'wp-load.php';

$uploads_dir = WP_CONTENT_DIR . '/uploads';
$base_url = home_url('/wp-content/uploads/');

echo "<h2>📊 Verificação de Uploads - SG Jurídico</h2>";
echo "<pre>";
echo "Diretório local: {$uploads_dir}\n";
echo "URL base: {$base_url}\n";
echo "\n" . str_repeat("=", 70) . "\n\n";

// Contar arquivos por ano
$years = ['2022', '2023', '2024', '2025'];
$total_files = 0;
$total_size = 0;

echo "📁 RESUMO POR ANO:\n";
echo str_repeat("-", 70) . "\n";
printf("%-10s | %-15s | %-20s\n", "Ano", "Arquivos", "Tamanho");
echo str_repeat("-", 70) . "\n";

foreach ($years as $year) {
    $year_dir = $uploads_dir . '/' . $year;
    if (is_dir($year_dir)) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($year_dir),
            RecursiveIteratorIterator::SILENT_FOR_PERMISSION_DENIED
        );
        
        $count = 0;
        $size = 0;
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $count++;
                $size += $file->getSize();
                $total_files++;
                $total_size += $file->getSize();
            }
        }
        
        printf("%-10s | %-15s | %-20s\n", 
            $year, 
            number_format($count), 
            size_format($size)
        );
    }
}

echo str_repeat("-", 70) . "\n";
printf("%-10s | %-15s | %-20s\n", 
    "TOTAL", 
    number_format($total_files), 
    size_format($total_size)
);

echo "\n" . str_repeat("=", 70) . "\n\n";

// Verificar se arquivos estão no Git
echo "🔍 VERIFICAÇÃO NO GIT:\n";
echo str_repeat("-", 70) . "\n";

$git_upload_files = shell_exec('git ls-files wp-content/uploads/ 2>&1');
$git_count = substr_count($git_upload_files, "\n");

if ($git_count <= 1) {
    echo "❌ NENHUM arquivo de upload está versionado no Git\n";
    echo "   Isso é esperado (uploads estão no .gitignore)\n";
    echo "\n✅ SOLUÇÃO: Fazer upload manual via:\n";
    echo "   1. File Manager do Hostinger\n";
    echo "   2. FTP\n";
    echo "   3. rsync via SSH\n";
} else {
    echo "⚠️  {$git_count} arquivos estão no Git\n";
    echo "   Considere remover do Git se não quiser versionar\n";
}

echo "\n" . str_repeat("=", 70) . "\n\n";

// Listar algumas imagens importantes para teste
echo "🖼️  IMAGENS IMPORTANTES PARA TESTAR:\n";
echo str_repeat("-", 70) . "\n";

$important_files = [
    '2023/09/Santo-Graal-Juridico-1.png',
    '2025/04/TJCE-Magistratura.webp',
];

foreach ($important_files as $file) {
    $local_path = $uploads_dir . '/' . $file;
    $url = $base_url . $file;
    
    if (file_exists($local_path)) {
        $size = filesize($local_path);
        echo "✅ {$file}\n";
        echo "   Local: {$local_path}\n";
        echo "   URL: {$url}\n";
        echo "   Tamanho: " . size_format($size) . "\n\n";
    } else {
        echo "❌ {$file} - NÃO ENCONTRADO\n\n";
    }
}

echo str_repeat("=", 70) . "\n\n";

echo "📋 PRÓXIMOS PASSOS:\n";
echo str_repeat("-", 70) . "\n";
echo "1. Fazer upload da pasta 'wp-content/uploads' para o servidor\n";
echo "2. Via File Manager ou FTP\n";
echo "3. Destino: public_html/wp-content/uploads/\n";
echo "4. Após upload, testar imagens no site\n";
echo "5. Executar fix-urls-wordpress.php se necessário\n";
echo "\n";

echo "📚 Veja SOLUCAO-UPLOAD-IMAGENS.md para instruções detalhadas\n";

echo "</pre>";
?>

