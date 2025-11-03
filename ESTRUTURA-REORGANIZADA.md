# ✅ Estrutura Reorganizada para Hostinger

## 📋 O que foi feito:

### 1. **Movimentação de Arquivos**
- ✅ Todos os arquivos de `public_html/` foram movidos para a **raiz** do repositório
- ✅ Pasta `public_html/` foi **removida** (não é mais necessária)
- ✅ Estrutura do WordPress agora está diretamente na raiz, como esperado pelo Hostinger

### 2. **Atualização do .gitignore**
- ✅ Removida a regra que ignorava `public_html/`
- ✅ Mantidas regras para ignorar:
  - Uploads grandes (`wp-content/uploads/*`)
  - Cache e arquivos temporários
  - Backups e arquivos sensíveis
  - Arquivos de documentação específicos

### 3. **Verificação de Caminhos**
- ✅ `wp-config.php` usa `__DIR__` que se adapta automaticamente
- ✅ Não há referências a `public_html/` no código
- ✅ Todos os caminhos estão relativos à raiz

---

## 📁 Estrutura Atual (Raiz do Repositório)

```
sg-juridico/
├── index.php                    ← Entrada principal WordPress
├── wp-config.php               ← Configuração do banco
├── wp-admin/                   ← Admin do WordPress
├── wp-content/                 ← Conteúdo (themes, plugins, uploads)
├── wp-includes/                ← Core WordPress
├── theme-only/                 ← Tema customizado (manter)
├── .gitignore                  ← Arquivos ignorados
├── README.md                   ← Documentação
└── [outros arquivos WordPress]
```

---

## 🚀 Próximos Passos

### Para fazer commit e deploy:

1. **Adicionar arquivos ao Git:**
   ```bash
   git add .
   ```

2. **Verificar o que será commitado:**
   ```bash
   git status
   ```

3. **Fazer commit:**
   ```bash
   git commit -m "feat: reorganizar estrutura para deployment Hostinger (arquivos na raiz)"
   ```

4. **Push para GitHub (dispara deploy automático):**
   ```bash
   git push origin master
   ```

---

## ⚠️ Importante

- **Uploads não serão versionados** (estão no `.gitignore`) - isso é correto pois são arquivos grandes gerados pelo usuário
- **O Hostinger agora fará deploy direto da raiz** para `public_html` no servidor
- **Não há mais duplicação** - tudo está na raiz, sem pasta `public_html` local

---

## ✅ Estrutura Esperada pelo Hostinger

O Hostinger espera que o repositório tenha os arquivos WordPress na raiz. Quando você faz deploy com caminho `/`, ele copia tudo da raiz do repositório para `public_html/` no servidor.

**Antes:**
```
repo/
  └── public_html/  ← Hostinger não encontrava aqui
      └── wp-config.php
```

**Agora:**
```
repo/
  └── wp-config.php  ← Hostinger encontra na raiz ✓
```

---

**Data da reorganização:** 03/11/2025

