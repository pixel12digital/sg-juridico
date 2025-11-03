# 🚀 Guia de Deployment - SG Jurídico

## 📋 Configuração Completa

Este projeto está configurado para **deploy automático** da raiz do repositório para o Hostinger.

---

## 🔧 Passos para Deploy

### 1. **Estrutura do Repositório**
✅ Arquivos WordPress na raiz do repositório (não em `public_html/`)  
✅ Hostinger faz deploy da raiz → `public_html/` no servidor  

### 2. **Configuração no Hostinger**

#### **Git no Hostinger:**
- Repositório: `https://github.com/pixel12digital/sg-juridico.git`
- Branch: `master`
- Caminho: `/` (raiz do repositório → `public_html`)

#### **Webhook Automático:**
- URL: `https://webhooks.hostinger.com/deploy/fc2164df3853183f2758ae225689dca2`
- Configurar no GitHub: `Settings → Webhooks → Add webhook`

---

## ⚠️ Importante: wp-config.php

### **Por Segurança:**
- ❌ `wp-config.php` **NÃO** está no repositório (contém senhas)
- ✅ Você precisa criá-lo **manual** no servidor

### **Como Criar wp-config.php no Hostinger:**

1. Acesse **File Manager** no hPanel
2. Vá para `public_html/`
3. Crie um novo arquivo `wp-config.php`
4. Copie a estrutura do seu `wp-config.php` local, ajustando se necessário

**Credenciais do Banco Remoto:**
```php
define('DB_NAME', 'u696538442_sgjuridico');
define('DB_USER', 'u696538442_sgjuridico');
define('DB_PASSWORD', 'SUA_SENHA_AQUI');
define('DB_HOST', 'srv1310.hstgr.io');
```

---

## 🔗 URLs Dinâmicas

### **Configuração Implementada:**
✅ URLs detectam automaticamente o ambiente  
✅ Funciona em **localhost** e **produção** sem configuração adicional  

**Como Funciona:**
- Local: usa `http://` + host da requisição
- Produção: usa `https://` + host da requisição
- Imagens carregam corretamente em ambos ambientes

### **Corrigir URLs no Banco:**

Se as imagens não aparecerem, execute:
```
https://sgjuridico.com.br/fix-urls-wordpress.php
```

**IMPORTANTE:** Delete o script após usar!

---

## 🗂️ Estrutura Final

```
Repositório (GitHub)
├── index.php              ← WordPress core
├── wp-config.php          ← NÃO está no Git (segurança)
├── wp-admin/
├── wp-content/
├── wp-includes/
├── theme-only/            ← Tema customizado
├── .gitignore             ← Configurado
└── [arquivos WordPress]

↓ Deploy Automático ↓

Servidor Hostinger (public_html/)
├── index.php              ← Do repositório
├── wp-config.php          ← Criado manualmente
├── wp-admin/              ← Do repositório
├── wp-content/            ← Do repositório
├── wp-includes/           ← Do repositório
└── theme-only/            ← Do repositório
```

---

## 🔄 Workflow de Deploy

### **Desenvolvimento Local:**
```bash
# Fazer alterações
git add .
git commit -m "descrição"
git push origin master
```

### **Deploy Automático:**
1. Push aciona webhook GitHub
2. Hostinger recebe notificação
3. Pull do repositório
4. Deploy para `public_html/`
5. **IMPORTANTE:** Não sobrescreve `wp-config.php` (não está no Git)

---

## ✅ Checklist de Deploy

### **Primeira Vez:**
- [ ] Repositório configurado no Hostinger (Git)
- [ ] Webhook configurado no GitHub
- [ ] `wp-config.php` criado manualmente no servidor
- [ ] Credenciais corretas no `wp-config.php`
- [ ] Deploy automático testado

### **Deploy Contínuo:**
- [ ] Código commitado e push feito
- [ ] Deploy automático executado
- [ ] Verificado resultado no hPanel
- [ ] Site testado após deploy

---

## 🔒 Segurança

### **Arquivos NÃO Versionados:**
- ✅ `wp-config.php` (senhas)
- ✅ `wp-content/uploads/` (muito grande)
- ✅ Arquivos de backup e temporários
- ✅ Documentação local

Ver `.gitignore` para lista completa.

---

## 📞 Suporte

### **Problemas com Deploy:**
1. Verificar logs no hPanel → GIT
2. Verificar webhook no GitHub → Settings → Webhooks
3. Verificar estrutura no File Manager

### **Imagens Não Aparecem:**
1. Executar `fix-urls-wordpress.php`
2. Verificar `wp-config.php` no servidor
3. Verificar cache (limpar se necessário)

---

**Data:** 03/11/2025  
**Versão:** 1.0

