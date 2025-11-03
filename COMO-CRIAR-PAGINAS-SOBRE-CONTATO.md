# 📄 Como Criar as Páginas "Sobre" e "Contato"

## ✅ Status da Implementação

Criei templates prontos para as páginas:
- ✅ `page-sobre.php` - Template para página Sobre
- ✅ `page-contato.php` - Template para página Contato
- ✅ Estilos CSS completos
- ✅ Sistema detecta automaticamente se as páginas existem

## 🚀 Como Criar as Páginas no WordPress

### Passo 1: Criar Página "Sobre"

1. Acesse: **Páginas → Adicionar Nova**
2. **Título**: "Sobre" (exatamente assim)
3. **Slug**: Certifique-se que o slug seja `sobre` (sem acento)
   - Edite o permalink clicando no título
   - Altere para: `sobre`
4. **Template**: Selecione **"Página Sobre"** no painel lateral direito
5. **Conteúdo** (opcional - ou deixe em branco para usar conteúdo padrão):
   ```
   <h2>Sobre o SG Jurídico</h2>
   <p>O SG Jurídico é uma plataforma especializada em cursos preparatórios para concursos públicos...</p>
   ```
6. Clique em **"Publicar"**

### Passo 2: Criar Página "Contato"

1. Acesse: **Páginas → Adicionar Nova**
2. **Título**: "Contato" (exatamente assim)
3. **Slug**: Certifique-se que o slug seja `contato` (sem acento)
   - Edite o permalink clicando no título
   - Altere para: `contato`
4. **Template**: Selecione **"Página Contato"** no painel lateral direito
5. **Conteúdo** (opcional - ou deixe em branco para usar conteúdo padrão)
6. Clique em **"Publicar"**

## 🎨 Conteúdo Padrão

### Página "Sobre" - Conteúdo Incluído
- ✅ Título principal
- ✅ Missão da empresa
- ✅ Cursos oferecidos
- ✅ Equipe especializada
- ✅ Design profissional

### Página "Contato" - Conteúdo Incluído
- ✅ Informações de contato
- ✅ E-mail
- ✅ WhatsApp
- ✅ Horário de atendimento
- ✅ Redes sociais
- ✅ Formulário de contato completo
- ✅ Validação de campos
- ✅ Design profissional

## 📝 Como Editar o Conteúdo

### Opção 1: Editar no WordPress Admin
1. Vá em **Páginas → Todas as páginas**
2. Clique em "Sobre" ou "Contato"
3. Edite o conteúdo
4. Salve as alterações

### Opção 2: Editar os Templates (Avançado)
Os templates estão em:
- `public_html/wp-content/themes/sg-juridico/page-sobre.php`
- `public_html/wp-content/themes/sg-juridico/page-contato.php`

## ✅ Verificar se Funcionou

### Teste os Links no Menu
1. Acesse o site
2. Clique em "Sobre" no menu
3. Se a página existir: → Vai para a página
4. Se a página não existir: → Mostra mensagem de erro

### Erro? Veja a Solução
Se ao clicar aparece um alerta:
- **Ação**: Criar as páginas conforme os passos acima
- **Slug correto**: `sobre` e `contato`

## 🔧 Estrutura de URLs

Após criar as páginas, as URLs serão:
- **Sobre**: `http://localhost/sg-juridico/public_html/sobre`
- **Contato**: `http://localhost/sg-juridico/public_html/contato`

## 📋 Checklist

### Para Página "Sobre"
- [ ] Página criada com título "Sobre"
- [ ] Slug configurado como "sobre"
- [ ] Template "Página Sobre" selecionado
- [ ] Página publicada
- [ ] Conteúdo personalizado (opcional)
- [ ] Link no menu funcionando

### Para Página "Contato"
- [ ] Página criada com título "Contato"
- [ ] Slug configurado como "contato"
- [ ] Template "Página Contato" selecionado
- [ ] Página publicada
- [ ] Conteúdo personalizado (opcional)
- [ ] Link no menu funcionando

## 🎨 Personalização

### Editar Informações de Contato
No arquivo `page-contato.php`, procure por:
```php
contato@sgjuridico.com.br
+55 (00) 00000-0000
```
Substitua pelos dados reais.

### Editar Redes Sociais
No mesmo arquivo, procure por:
```php
<a href="#" target="_blank">Facebook</a>
```
Substitua `#` pelos links reais.

### Personalizar Cores
Os estilos estão em `style.css`:
```css
.about-content h2 {
    color: var(--sg-color-primary); /* Ciano #5CE1E6 */
}
```

## ⚡ Solução Rápida

Se precisar criar rapidamente:
1. **WordPress Admin** → Páginas → Adicionar Nova
2. Título: "Sobre" ou "Contato"
3. Template: Selecione o template correto
4. Slug: Digite `sobre` ou `contato`
5. Publicar

**Pronto!** Os links no menu agora funcionam!

## 🎯 Resultado Final

Após criar as páginas:
- ✅ Menu "Sobre" funcional
- ✅ Menu "Contato" funcional
- ✅ Design profissional
- ✅ Conteúdo pré-formatado
- ✅ Responsivo
- ✅ Formulário de contato incluído

---

**📌 Dica**: Use o sistema de alerta temporário para criar as páginas rapidamente quando clicar nos links!

