# 🚀 Configuração de Implantação Automática - GitHub → Hostinger

## 📋 Informações para Configuração

### Webhook do Hostinger
- **URL:** `https://webhooks.hostinger.com/deploy/fc2164df3853183f2758ae225689dca2`
- **Repositório:** `https://github.com/pixel12digital/sg-juridico`
- **Branch:** `master`

---

## 🔧 Passos para Configurar no GitHub

### 1. Acesse a página de configuração de webhooks
Acesse diretamente:
**`https://github.com/pixel12digital/sg-juridico/settings/hooks/new`**

Ou navegue manualmente:
1. Vá para: `https://github.com/pixel12digital/sg-juridico`
2. Clique em **Settings** (Configurações)
3. No menu lateral, clique em **Webhooks**
4. Clique em **Add webhook** (Adicionar webhook)

### 2. Configure o Webhook

Preencha os seguintes campos:

| Campo | Valor |
|-------|-------|
| **Payload URL** | `https://webhooks.hostinger.com/deploy/fc2164df3853183f2758ae225689dca2` |
| **Content type** | `application/json` |
| **Secret** | Deixe em branco (ou crie um secret se quiser segurança extra) |
| **Which events would you like to trigger this webhook?** | Selecione **"Just the push event"** (Apenas o evento push) |

### 3. Branch
- Certifique-se de que o webhook será acionado apenas para a branch **`master`**
- Você pode usar filtros de branch se necessário

### 4. Status
- Marque como **Active** (Ativo)

### 5. Salvar
- Clique em **Add webhook** (Adicionar webhook)

---

## ✅ Verificação

Após configurar, você pode testar:

1. **Fazer um commit e push para o repositório:**
   ```bash
   git add .
   git commit -m "Teste de deploy automático"
   git push origin master
   ```

2. **Verificar o deploy no Hostinger:**
   - No hPanel, vá em **GIT**
   - Clique em **"Visualizar resultado da última compilação"**
   - Você deve ver o status do deploy

3. **Verificar no GitHub:**
   - Na página de webhooks: `https://github.com/pixel12digital/sg-juridico/settings/hooks`
   - Clique no webhook criado
   - Veja os **Recent Deliveries** (Entregas Recentes)
   - Deve mostrar status **200** (sucesso) ou outros códigos

---

## 🔍 Solução de Problemas

### Se o deploy não funcionar:

1. **Verificar URL do webhook:**
   - Certifique-se de copiar exatamente: `https://webhooks.hostinger.com/deploy/fc2164df3853183f2758ae225689dca2`

2. **Verificar eventos:**
   - O webhook deve estar configurado para **"Just the push event"**

3. **Verificar branch:**
   - Certifique-se de fazer push para `master`
   - Verifique se há filtros de branch no webhook

4. **Verificar logs:**
   - No GitHub, veja os **Recent Deliveries** do webhook
   - Clique em uma entrega para ver a resposta do servidor
   - No Hostinger, veja o resultado da compilação

5. **Verificar repositório:**
   - Certifique-se de que o repositório no Hostinger está configurado corretamente
   - Verifique se o caminho de instalação está correto (`/` para `public_html`)

---

## 📝 Notas Importantes

- ⚠️ **Cuidado:** O deploy automático vai sobrescrever os arquivos em `public_html` sempre que houver push em `master`
- 📦 **Backup:** Certifique-se de ter backups antes de ativar
- 🔒 **Segurança:** O webhook URL é único e específico para este site. Não compartilhe publicamente.

---

## 🎯 Resultado Esperado

Após a configuração, sempre que você fizer:
```bash
git push origin master
```

O Hostinger irá automaticamente:
1. Receber a notificação via webhook
2. Fazer pull do repositório
3. Fazer deploy para `public_html`
4. Você verá o resultado no painel do Hostinger

---

**Data da configuração:** 03/11/2025

