# 🎯 Como Organizar o Menu de Navegação - Guia Passo a Passo

## 📋 Estrutura Recomendada para o Menu SG Jurídico

### Estrutura Hierárquica Ideal

```
┌─ Início
│
├─ Cursos ⬇
│  ├─ Todos os Cursos
│  ├─ Método SG
│  │  ├─ Método SG | TJRJ
│  │  ├─ Método SG | MPSP
│  │  ├─ Método SG | PCMG
│  │  └─ Método SG | ENAM
│  ├─ Lei Seca
│  │  ├─ Magistratura
│  │  ├─ Delegado
│  │  ├─ Ministério Público
│  │  └─ Analista Judiciário
│  └─ Sumulas
│     ├─ TJRJ
│     ├─ STJ
│     └─ STF
│
├─ Loja (Shop)
│
├─ Blog
│
├─ Sobre
│
└─ Contato
```

---

## 🔧 Como Configurar no WordPress

### Passo 1: Acessar o Gerenciador de Menus

1. Faça login no painel admin: `http://localhost/sg-juridico/public_html/wp-admin`
2. Vá em: **Aparência → Menus**
3. Se você já tem um menu "Menu Principal", edite-o
4. Se não tem, clique em **"Criar um novo menu"**

### Passo 2: Criar a Estrutura Base

Adicione os itens principais primeiro:

#### Itens Principais (Primeiro Nível)
- ✅ **Início** (Home)
- ✅ **Cursos** (Com dropdown - será expandido)
- ✅ **Loja** (Link para Shop do WooCommerce)
- ✅ **Blog** (Link para posts)
- ✅ **Sobre** (Página sobre a empresa)
- ✅ **Contato** (Página de contato)

### Passo 3: Adicionar Submenu "Cursos"

1. Clique em **"Ver todas"** na estrutura do menu
2. Adicione as páginas desejadas
3. Depois de adicionar, arraste os itens para a direita **SE** pertencem a "Cursos"

#### Como Criar Submenu:
```
- Cursos (mantém à esquerda)
  - Todos os Cursos (arraste para direita)
  - Método SG (arraste para direita)
    - Método SG | TJRJ (arraste mais para direita ainda)
    - Método SG | MPSP (arraste mais para direita ainda)
  - Lei Seca (arraste para direita)
    - Magistratura
    - Delegado
  - Sumulas (arraste para direita)
```

**Para transformar em submenu**: Clique e arraste o item para a DIREITA, sob o item pai.

### Passo 4: Links Importantes que DEVEM Aparecer

#### ✅ INCLUIR
- Início
- Cursos (com dropdown)
- Loja (Shop)
- Blog
- Sobre
- Contato

#### ❌ REMOVER/OCULTAR
- Área de Membros
- Assinatura do Lojista
- Cadastro
- Cadastro de alunos
- Cadastro de instrutores
- Calendário de Eventos (a menos que seja importante)
- Manutenção
- Materiais (a menos que seja importante)
- Cart
- Checkout
- Finalização de compra
- Etn Category / Etn Tags
- Favoritos
- Minha conta (já está no dropdown do perfil)

### Passo 5: Finalizar

1. **Nomeie o menu**: "Menu Principal"
2. **Atribua à localização**: Marque **"Primary Menu"**
3. **Clique em "Salvar menu"**

---

## 🎨 Menu Padrão (Fallback)

Se não houver menu configurado, o sistema mostrará automaticamente:

```
Início | Cursos ▼ | Loja | Blog | Sobre | Contato
```

Onde **Cursos** terá submenu com:
- Todos os Cursos
- Método SG
- Lei Seca
- Sumulas

---

## 📱 Como o Menu Ficará

### Desktop
```
┌─────────────────────────────────────────────────┐
│ Início │ Cursos ▾ │ Loja │ Blog │ Sobre │ Contato │
└─────────────────────────────────────────────────┘
         │
         ├─ Todos os Cursos
         ├─ Método SG
         │  └─ Método SG | TJRJ
         │  └─ Método SG | MPSP
         ├─ Lei Seca
         │  └─ Magistratura
         │  └─ Delegado
         └─ Sumulas
```

### Mobile
```
┌─────────────────┐
│ ☰ Menu          │
└─────────────────┘
         ▼ (ao clicar)
┌─────────────────┐
│ Início          │
│ Cursos ►        │
│ Loja            │
│ Blog            │
│ Sobre           │
│ Contato         │
└─────────────────┘
```

---

## 🛠️ Customização Avançada

### Adicionar Badges/Indicadores
Você pode adicionar badges como "Novo!" ou "Popular" usando plugins como:
- **Restricted Site Access** para badges
- Ou editar CSS para adicionar ícones

### Adicionar Ícones ao Menu
Use CSS para adicionar ícones:
```css
.nav-menu > li.menu-item-has-children > a::before {
  content: "📚 ";
}
```

---

## ⚡ Resumo Rápido

1. **Vá para**: Aparência → Menus
2. **Adicione**: Início, Cursos, Loja, Blog, Sobre, Contato
3. **Arraste**: Itens de Cursos para direita para criar submenu
4. **Atribua**: Localização "Primary Menu"
5. **Salve**: Clique em "Salvar menu"

---

## ✅ Checklist

- [ ] Menu criado com nome "Menu Principal"
- [ ] Itens principais adicionados (6 itens)
- [ ] Submenu "Cursos" criado com 3 subitens
- [ ] Menu atribuído à localização "Primary Menu"
- [ ] Menu salvo
- [ ] Visualização no site testada
- [ ] Menu responsivo testado (mobile)
- [ ] Dropdown funcionando corretamente

---

## 🎯 Resultado Final

**Menu Limpo e Organizado**
- ✅ Apenas itens importantes
- ✅ Hierarquia clara
- ✅ Submenu funcional
- ✅ Responsivo
- ✅ Fácil navegação
- ✅ Profissional

**Menu com Fundo Escuro**
- ✅ Fundo: #484848
- ✅ Texto: Branco
- ✅ Hover: Ciano #5CE1E6
- ✅ Contraste perfeito

---

## 📞 Precisa de Ajuda?

Se ainda estiver com muitos itens no menu após seguir este guia:

1. **Remova manualmente** os itens indesejados
2. **Mantenha apenas**: Início, Cursos, Loja, Blog, Sobre, Contato
3. **Use o submenu** para organizar por categorias

**Dica**: Menos é mais! Um menu limpo é mais profissional.

