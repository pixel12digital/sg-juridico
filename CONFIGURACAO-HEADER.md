# 📋 Guia de Configuração do Header SG Jurídico

## ✅ Status da Implementação
- [x] Estrutura HTML completa
- [x] Estilos CSS responsivos
- [x] JavaScript interativo
- [x] Integração WooCommerce
- [ ] ⬇️ **Configurações do WordPress (Siga os passos abaixo)**

---

## 🚀 Próximos Passos para Ativar o Header

### 1️⃣ Configurar o Menu "Primary Menu"

1. Acesse o painel admin: `http://seu-site/wp-admin`
2. Vá em **Aparência → Menus**
3. Se não existir um menu, clique em **"Criar um novo menu"**
4. Dê um nome: `Menu Principal`
5. Adicione as páginas/links desejados (ex: Início, Cursos, Categorias, Sobre, Blog)
6. Marque o local **"Primary Menu"** na seção de localização
7. Clique em **Salvar menu**

**Links sugeridos para incluir:**
- Início (Home)
- Cursos
- Categorias
- Sobre
- Blog
- Contato

---

### 2️⃣ Configurar Logo da Marca

1. Vá em **Aparência → Personalizar**
2. No menu lateral, clique em **"Identidade do Site"**
3. Faça upload da logo na seção **"Logo do Site"**
   - Dimensões recomendadas: 350x100px (altura máxima: 50px)
   - Formatos aceitos: PNG, JPG, SVG
4. Clique em **"Publicar"**

**Ou via código (se preferir):**
```php
// Execute este código no functions.php ou via plugin
set_theme_mod('custom_logo', ID_DA_IMAGEM);
```

---

### 3️⃣ Habilitar Registro de Usuários (WooCommerce)

1. Vá em **WooCommerce → Configurações**
2. Clique na aba **"Conta"**
3. Marque a opção **"Permitir que os clientes registrem uma conta na página "Conta" "**
4. Se desejar, marque também **"Conectar clientes existentes"**
5. Clique em **"Salvar alterações"**

---

### 4️⃣ Verificar Funcionamento do Carrinho

1. Crie ou verifique se existe uma página de **Carrinho**:
   - Vá em **WooCommerce → Configurações**
   - Aba **"Avançado"**
   - Verifique se a página de Carrinho está configurada
2. Se não estiver configurada, o WooCommerce criará automaticamente quando você criar um produto

---

## 🧪 Testes para Validar Funcionamento

### Teste 1: Visual Desktop
- [ ] Logo aparece corretamente no canto esquerdo
- [ ] Barra de pesquisa aparece centralizada
- [ ] Botões "Entrar", "Cadastrar" e "Comece Agora" visíveis
- [ ] Menu principal aparece abaixo do header

### Teste 2: Usuário Não Logado
- [ ] Clicar em "Entrar" redireciona para login
- [ ] Clicar em "Cadastrar" redireciona para registro
- [ ] Clicar em "Comece Agora" redireciona para contas
- [ ] Barra de pesquisa funciona e busca cursos/produtos

### Teste 3: Usuário Logado
- [ ] Carrinho aparece com ícone
- [ ] Contador de itens aparece quando há produtos
- [ ] Dropdown do perfil mostra avatar e nome
- [ ] Dropdown abre ao clicar
- [ ] Links "Minha Conta" e "Meus Cursos" funcionam
- [ ] Botão "Sair" faz logout

### Teste 4: Responsividade
- [ ] Menu hambúrguer aparece em mobile (< 768px)
- [ ] Nome do usuário desaparece em tablet
- [ ] CTA "Comece Agora" desaparece em mobile pequeno
- [ ] Elementos reorganizam em coluna no mobile
- [ ] Barra de pesquisa ocupa largura total em mobile

### Teste 5: Funcionalidades WooCommerce
- [ ] Adicionar produto ao carrinho atualiza contador
- [ ] Clicar no carrinho abre página de checkout
- [ ] Contador atualiza via AJAX sem recarregar página

---

## 🎨 Personalização de Cores (Opcional)

A paleta de cores está definida em `css/palette.css`. Para alterar:

```css
:root {
    --sg-color-primary: #5CE1E6;      /* Cor principal (ciano) */
    --sg-color-primary-dark: #4BC4C8; /* Hover de botões */
    --sg-color-gray: #808080;         /* Cinza neutro */
}
```

**Botões do header usam:**
- Botão Login: Transparente com borda
- Botão Cadastro: Cor primária (#5CE1E6)
- CTA "Comece Agora": Cor primária com sombra

---

## 🔧 Solução de Problemas

### Problema: Menu não aparece
**Solução:** Verifique se o menu "Primary Menu" está atribuído em Aparência → Menus → Localizações do Menu

### Problema: Carrinho não mostra contador
**Solução:** Verifique se WooCommerce está instalado e ativo. A função `sg_cart_fragments_count()` depende do WooCommerce.

### Problema: Dropdown não abre
**Solução:** Verifique se o arquivo `navigation.js` está carregando. Abra o Console do navegador (F12) e verifique erros JavaScript.

### Problema: Botões de login/cadastro não aparecem
**Solução:** Isso é normal se o WooCommerce não está ativo. O header verifica `class_exists('WooCommerce')` antes de mostrar alguns elementos.

---

## 📱 Compatibilidade de Navegadores

- ✅ Chrome/Edge (últimas versões)
- ✅ Firefox (últimas versões)
- ✅ Safari (últimas versões)
- ✅ Opera (últimas versões)
- ✅ Mobile Chrome/Safari
- ✅ Tablet iOS/Android

---

## 📝 Arquivos Modificados

```
public_html/wp-content/themes/sg-juridico/
├── header.php           ✓ Atualizado com nova estrutura
├── functions.php        ✓ Adicionadas funções WooCommerce
├── style.css           ✓ Estilos completos e responsivos
└── js/navigation.js    ✓ Dropdown interativo
```

---

## ✨ Recursos Implementados

### Funcionalidades
- [x] Header sticky (fixo no topo ao rolar)
- [x] Barra de pesquisa com ícone
- [x] Carrinho com contador dinâmico
- [x] Dropdown de perfil
- [x] Botões responsivos
- [x] Menu mobile com toggle
- [x] Integração WooCommerce completa
- [x] Suporte a usuários logados/não logados

### Design
- [x] Paleta de cores oficial (#5CE1E6)
- [x] Transições suaves
- [x] Hover states
- [x] Focus states para acessibilidade
- [x] Ícones SVG inline
- [x] Layout moderno e profissional

### UX/UI
- [x] Hierarquia visual clara
- [x] CTAs destacados
- [x] Feedback visual em interações
- [x] Espaçamento adequado
- [x] Cores contrastantes

---

## 🎯 Próximas Melhorias Sugeridas

1. **Mini carrinho dropdown**: Mostrar produtos ao passar o mouse no carrinho
2. **Busca com autocomplete**: Sugestões de produtos ao digitar
3. **Breadcrumbs**: Navegação estrutural abaixo do header
4. **Banner promocional**: Barra de promoção acima do header
5. **Menu megamenu**: Categorias em dropdown expansivo

---

**📌 Pronto para uso após concluir os 3 passos principais acima!**

