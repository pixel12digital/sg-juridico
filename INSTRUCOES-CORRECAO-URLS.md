# 🔧 Correção de URLs - WordPress Local e Produção

## ✅ O que foi feito:

### 1. **Configuração Dinâmica de URLs** (`wp-config.php`)
- ✅ URLs agora são detectadas automaticamente baseadas no ambiente
- ✅ Local: usa o host da requisição (ex: `http://localhost/sg-juridico`)
- ✅ Produção: usa o host da requisição (ex: `https://sgjuridico.com.br`)
- ✅ Não mais URLs hardcoded que causam problemas

### 2. **Removida Configuração WP_CONTENT_URL**
- ✅ WordPress agora calcula automaticamente URLs de imagens
- ✅ Funciona dinamicamente em qualquer ambiente

### 3. **Script de Correção Criado** (`fix-urls-wordpress.php`)
- ✅ Script para corrigir URLs antigas no banco de dados
- ✅ Atualiza opções `home` e `siteurl`
- ✅ Substitui URLs antigas nos posts

---

## 🚀 Como Aplicar a Correção:

### **Passo 1: Executar o Script de Correção**

1. **Localmente:**
   - Acesse: `http://localhost/sg-juridico/fix-urls-wordpress.php`
   - O script detectará automaticamente o ambiente
   - Aguarde a mensagem de sucesso

2. **Após executar:**
   - **DELETE o arquivo** `fix-urls-wordpress.php` por segurança!

### **Passo 2: Verificar Resultado**

As imagens devem carregar automaticamente com URLs corretas:
- **Local:** `http://localhost/sg-juridico/wp-content/uploads/...`
- **Produção:** `https://sgjuridico.com.br/wp-content/uploads/...`

---

## 📝 Notas Importantes:

1. **wp-config.php não está no Git**
   - Por segurança, não versionamos credenciais
   - Você precisa criar manualmente no servidor Hostinger

2. **Como Criar wp-config.php no Hostinger:**
   - Acesse File Manager no hPanel
   - Vá para `public_html/`
   - Crie/copie o arquivo `wp-config.php` com sua configuração local

3. **Banco de Dados Compartilhado:**
   - Como você usa o mesmo banco remoto em local e produção
   - As URLs no banco serão atualizadas pela última execução do script
   - Pode precisar ajustar dependendo do ambiente

---

## 🔍 Solução de Problemas:

### **Imagens não aparecem localmente:**
```bash
# Execute o script novamente
http://localhost/sg-juridico/fix-urls-wordpress.php
```

### **Imagens não aparecem em produção:**
```bash
# Acesse o script pela URL de produção (se ainda existir)
https://sgjuridico.com.br/fix-urls-wordpress.php
```

### **Reset manual no banco:**
Se preferir, execute via phpMyAdmin:
```sql
UPDATE wp_options 
SET option_value = 'https://sgjuridico.com.br' 
WHERE option_name = 'home' OR option_name = 'siteurl';
```

---

## ✅ Checklist Final:

- [x] `wp-config.php` configurado com URLs dinâmicas
- [x] Removido `WP_CONTENT_URL` hardcoded
- [x] Script de correção criado
- [ ] Script executado localmente
- [ ] Script deletado após execução
- [ ] `wp-config.php` criado no Hostinger
- [ ] Testado em produção

---

**Data:** 03/11/2025

