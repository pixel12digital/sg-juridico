# Alterações no wp-config.php para Produção

## ⚠️ IMPORTANTE
O arquivo `wp-config.php` não é versionado no Git por segurança (contém credenciais). 
As alterações abaixo precisam ser aplicadas **MANUALMENTE** na produção.

## Alterações Realizadas (linhas 238-315)

### Problema Resolvido
Corrigir URLs redirecionando para localhost em produção.

### Mudanças Principais

#### 1. Detecção Melhorada de Ambiente (linhas 243-260)
```php
// ANTES: Usava variável global $is_localhost que podia estar desatualizada
// AGORA: Detecta ambiente baseado no HTTP_HOST real da requisição atual

$current_host = '';
if (!$is_cli && isset($_SERVER['HTTP_HOST'])) {
    $current_host = $_SERVER['HTTP_HOST'];
} elseif ($is_cli) {
    $current_host = 'localhost';
}

// Re-detecta localhost baseado no host atual da requisição
$is_actually_localhost = (
    $is_cli ||
    empty($current_host) ||
    strpos($current_host, 'localhost') !== false || 
    strpos($current_host, '127.0.0.1') !== false ||
    strpos($current_host, '::1') !== false ||
    $current_host === 'localhost'
);
```

#### 2. Detecção de Protocolo HTTPS Melhorada (linhas 262-275)
```php
// Adicionado suporte para headers de proxy reverso
if (!$is_cli && isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https';
}
```

#### 3. Uso Sempre do Host Real da Requisição (linhas 277-284)
```php
// SEMPRE usa o host real da requisição atual, nunca valores do banco
if ($is_cli || empty($current_host)) {
    $host = 'localhost';
} else {
    $host = $current_host; // Usa HTTP_HOST atual
}
```

#### 4. Variáveis Globais para Override (linhas 311-314)
```php
// Força atualização via variáveis globais caso as constantes já existam
$GLOBALS['wp_home_override'] = $base_url;
$GLOBALS['wp_siteurl_override'] = $base_url;
```

## Como Aplicar na Produção

### Opção 1: Editar Manualmente (Recomendado)
1. Acesse o arquivo `wp-config.php` na produção via FTP/SFTP ou painel de controle
2. Localize a seção "Configurações de URL dinâmicas" (aproximadamente linha 238)
3. Substitua o bloco de código das linhas 238-315 pelo código atualizado do arquivo local

### Opção 2: Comparar e Aplicar Diferenças
1. Faça backup do `wp-config.php` atual na produção
2. Compare o arquivo local com o da produção
3. Aplique apenas as mudanças na seção de URLs (linhas 238-315)

### Opção 3: Usar Script de Migração
1. Copie o `wp-config.php` local para produção (mantendo as credenciais de produção)
2. Ajuste manualmente as credenciais de banco de dados se necessário

## Verificação Após Aplicação

Após aplicar as mudanças, verifique:
- ✅ URLs não redirecionam mais para localhost
- ✅ Links do menu funcionam corretamente
- ✅ Páginas carregam com o domínio correto
- ✅ HTTPS funciona corretamente (se aplicável)

## Arquivos Relacionados

As seguintes alterações já foram commitadas e estarão disponíveis após o deployment:
- ✅ `wp-content/mu-plugins/force-correct-urls.php` - Plugin que força URLs corretas
- ✅ `fix-urls-database.php` - Script para corrigir URLs no banco de dados
- ✅ `fix-urls-direct-sql.php` - Script SQL direto para corrigir URLs
- ✅ `fix-plugins.php` - Script para corrigir plugins desativados

## Nota de Segurança

⚠️ **NUNCA** commite o `wp-config.php` no Git. Ele contém:
- Credenciais do banco de dados
- Chaves de segurança do WordPress
- Outras informações sensíveis










