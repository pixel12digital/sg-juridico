# 📤 Solução: Upload de Imagens para Produção

## 🔍 Problema Identificado

- ✅ **Localmente:** 1.816 arquivos (~196 MB) na pasta `wp-content/uploads/`
- ❌ **No Git:** 0 arquivos (estão no `.gitignore`)
- ❌ **Produção:** Imagens não aparecem porque os arquivos não foram enviados

---

## 🚀 SOLUÇÃO RECOMENDADA: Upload Manual via Hostinger

### **Método 1: File Manager do Hostinger** ⭐ (Mais Fácil)

#### Passo 1: Preparar os arquivos
1. No seu computador local, vá até:
   ```
   C:\xampp\htdocs\sg-juridico\wp-content\uploads
   ```

#### Passo 2: Compactar (para facilitar upload)
1. Selecione toda a pasta `uploads` (ou subpastas principais)
2. Comprima em ZIP:
   - `uploads.zip` ou
   - `uploads-2023-2024.zip` (separar por ano se for muito grande)

#### Passo 3: Upload no Hostinger
1. Acesse **hPanel** do Hostinger
2. Vá em **Gerenciador de Arquivos** (File Manager)
3. Navegue até: `public_html/wp-content/`
4. Verifique se existe a pasta `uploads/`:
   - Se **não existir**: crie a pasta `uploads`
   - Se **existir vazia**: está correto
5. Faça upload do arquivo ZIP:
   - Clique em **Upload**
   - Selecione seu arquivo ZIP
   - Aguarde o upload
6. Extraia o ZIP:
   - Clique com botão direito no ZIP
   - Selecione **Extrair**
   - Confirme o destino: `public_html/wp-content/uploads/`
7. Delete o arquivo ZIP após extrair

---

### **Método 2: FTP** (Para muitos arquivos)

#### Passo 1: Obter credenciais FTP
1. No hPanel do Hostinger
2. Vá em **FTP** ou **FileZilla** ou **Acesso SSH**
3. Anote:
   - Servidor FTP
   - Usuário
   - Senha

#### Passo 2: Conectar via FTP
Use um cliente FTP como:
- **FileZilla** (gratuito)
- **WinSCP** (Windows)
- **Cyberduck**

#### Passo 3: Sincronizar
1. Conecte ao servidor
2. Navegue até: `/public_html/wp-content/`
3. Local: `C:\xampp\htdocs\sg-juridico\wp-content\uploads`
4. Sincronize a pasta `uploads` completa

---

### **Método 3: rsync via SSH** (Avançado)

Se tiver acesso SSH:
```bash
rsync -avz -e ssh \
  C:\xampp\htdocs\sg-juridico\wp-content\uploads/ \
  usuario@servidor:/home/u696538442/public_html/wp-content/uploads/
```

---

## 🔄 SOLUÇÃO ALTERNATIVA: Habilitar Uploads no Git

⚠️ **ATENÇÃO:** Isso vai aumentar muito o tamanho do repositório (~196 MB)

### Se quiser versionar uploads:

1. **Remover do .gitignore:**
```bash
# Editar .gitignore e comentar/remover:
# wp-content/uploads/*
```

2. **Adicionar ao Git:**
```bash
git add wp-content/uploads/
git commit -m "feat: adicionar uploads ao repositório"
git push origin master
```

3. **Problemas:**
   - Repositório ficará muito pesado
   - Deploy mais lento
   - Não recomendado para produção

---

## ✅ Checklist Pós-Upload

Após fazer upload das imagens:

- [ ] Verificar se pasta `uploads` existe em `public_html/wp-content/`
- [ ] Verificar se arquivos foram copiados
- [ ] Testar imagens no site (recarregar página)
- [ ] Verificar Biblioteca de Mídia no WordPress Admin
- [ ] Executar `fix-urls-wordpress.php` se URLs ainda estiverem erradas

---

## 🔍 Verificar se Funcionou

### Teste 1: Verificar arquivo específico
Acesse diretamente uma imagem conhecida:
```
https://sgjuridico.com.br/wp-content/uploads/2023/09/Santo-Graal-Juridico-1.png
```
Se carregar = ✅ Funcionou!

### Teste 2: Biblioteca de Mídia
1. WordPress Admin → Mídia → Biblioteca
2. Verifique se os thumbnails aparecem
3. Se aparecerem = ✅ Funcionou!

### Teste 3: Frontend
1. Acesse `sgjuridico.com.br`
2. Verifique se imagens de produtos aparecem
3. Se aparecerem = ✅ Funcionou!

---

## 📝 Estrutura Esperada no Servidor

```
public_html/
└── wp-content/
    └── uploads/
        ├── 2022/
        ├── 2023/
        │   ├── 09/
        │   ├── 10/
        │   ├── 11/
        │   └── 12/
        ├── 2024/
        │   ├── 01/
        │   ├── 02/
        │   └── ... (todos os meses)
        ├── 2025/
        ├── elementor/
        ├── woocommerce_uploads/
        └── ...
```

---

## ⚠️ Importante

1. **Permissões:** Certifique-se de que a pasta `uploads` tem permissão 755 e arquivos 644
2. **Tamanho:** Upload pode demorar dependendo da conexão (~196 MB)
3. **Backup:** Faça backup antes de substituir arquivos existentes
4. **URLs:** Após upload, pode precisar executar `fix-urls-wordpress.php`

---

## 🆘 Problemas Comuns

### **Imagens ainda não aparecem após upload:**
1. Verifique permissões (755 para pastas, 644 para arquivos)
2. Execute `fix-urls-wordpress.php`
3. Limpe cache do WordPress e navegador
4. Verifique se URLs no banco estão corretas

### **Upload muito lento:**
1. Comprima em ZIP e faça upload do ZIP
2. Use FTP em vez de File Manager
3. Faça upload em partes (por ano)

---

**Data:** 03/11/2025

