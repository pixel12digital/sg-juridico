# 📊 Planejamento: Organização do Dashboard SG Jurídico

## 🎯 Objetivo
Criar um dashboard personalizado, limpo e focado nas informações essenciais que o administrador precisa para gerenciar o site de cursos jurídicos.

---

## 📋 Análise do Site SG Jurídico

### Características Identificadas:
- **Tipo de Negócio**: Plataforma de cursos e materiais jurídicos
- **E-commerce**: WooCommerce ativo (venda de cursos/produtos)
- **Eventos**: Sistema de eventos de concursos (ETN)
- **Conteúdo**: Posts (blog), Páginas (Sobre, Contato)
- **Foco**: Carreiras jurídicas (Magistratura, MP, Delegado, ENAM, etc.)

---

## 🎨 Estrutura Proposta do Dashboard

### **ZONA 1: VISÃO GERAL RÁPIDA** (Coluna Esquerda - Principal)

#### Widget 1: **Resumo do Negócio**
**O que mostrar:**
- Total de cursos/produtos ativos
- Total de pedidos (hoje/semana/mês)
- Receita (período atual)
- Alunos/Clientes cadastrados
- Taxa de conversão (opcional)

**Ações rápidas:**
- Botão "Adicionar Novo Curso"
- Link "Ver Todos os Produtos"
- Link "Ver Pedidos"

---

#### Widget 2: **Estatísticas de Conteúdo**
**O que mostrar:**
- Total de Posts publicados
- Total de Páginas
- Posts em rascunho
- Páginas pendentes de revisão

**Ações rápidas:**
- Botão "Criar Novo Post"
- Link "Ver Todos os Posts"
- Link "Criar Nova Página"

---

#### Widget 3: **Próximos Eventos de Concursos**
**O que mostrar:**
- Lista dos 5 próximos eventos (ETN)
- Data e categoria de cada evento
- Link para editar/criar eventos

**Ações rápidas:**
- Botão "Adicionar Novo Evento"
- Link "Ver Calendário Completo"

---

### **ZONA 2: ATIVIDADES RECENTES** (Coluna Direita)

#### Widget 4: **Pedidos Recentes**
**O que mostrar:**
- Últimos 5 pedidos recebidos
- Status do pedido (Processando, Concluído, Pendente)
- Valor e data
- Link para ver detalhes

**Ações rápidas:**
- Link "Ver Todos os Pedidos"
- Link "Ir para WooCommerce → Pedidos"

---

#### Widget 5: **Conteúdo Recente**
**O que mostrar:**
- Últimos posts publicados (3-5)
- Últimas páginas editadas
- Com data de publicação

**Ações rápidas:**
- Link "Editar" em cada item
- Link "Ver Todos os Posts"

---

#### Widget 6: **Configurações Rápidas**
**O que mostrar:**
- Links para editar Header (logo, menu, etc.)
- Links para editar Footer
- Links para página de Contato
- Links para páginas institucionais:
  - Sobre Nós
  - Política de Privacidade
  - Termos de Uso
  - Outras páginas importantes
- Configurações gerais do site
- Customizer (Aparência)

**Ações rápidas:**
- Botão "Personalizar Tema"
- Links diretos para cada seção
- Status de páginas criadas (criada/não criada)

---

#### Widget 7: **Comentários Recentes** (Se aplicável)
**O que mostrar:**
- Últimos comentários no blog (se houver)
- Status (aprovado/pendente)
- Link para moderar

---

### **ZONA 3: ALERTAS E NOTIFICAÇÕES** (Topo)

#### Widget 7: **Alertas Importantes**
**O que mostrar:**
- Pendências de pagamento
- Produtos sem estoque (se aplicável)
- Eventos sem data
- Atualizações pendentes do WordPress/Plugins

**Estilo:**
- Boxes coloridos (amarelo para avisos, vermelho para críticos)

---

## 🗑️ ELEMENTOS A REMOVER

### Remover Completamente:
1. ❌ Widget "Atividade" (padrão WordPress)
2. ❌ Widget "Notícias do WordPress"
3. ❌ Widget "Eventos do WordPress"
4. ❌ Widget "Rascunho Rápido"
5. ❌ Widget "Status de Diagnóstico" do WooCommerce (genérico)
6. ❌ Banner de "Boas-vindas" padrão
7. ❌ Widget de atualizações do WordPress (mover para notificação)

---

## 📊 INFORMAÇÕES ESSENCIAIS PARA O ADMINISTRADOR

### O que o admin precisa saber/controlar:

1. **Vendas e Negócio:**
   - Quantos pedidos recebeu hoje/semana/mês
   - Quais produtos/cursos vendem mais
   - Receita total
   - Status dos pedidos pendentes

2. **Conteúdo:**
   - Quais posts/páginas foram publicados recentemente
   - O que precisa ser atualizado
   - Conteúdo em rascunho

3. **Eventos:**
   - Quais concursos estão chegando
   - Precisam criar/criar eventos novos

4. **Manutenção:**
   - Atualizações necessárias
   - Problemas técnicos
   - Backups realizados

---

## 🎨 DESIGN E LAYOUT

### Layout Proposto:
```
┌─────────────────────────────────────────────────────┐
│  [Logo SG Jurídico]  Painel                         │
├─────────────────────────────────────────────────────┤
│                                                       │
│  ┌────────────────────────┐  ┌────────────────────┐ │
│  │  Resumo do Negócio     │  │  Pedidos Recentes  │ │
│  │  [Estatísticas]        │  │  [Lista]           │ │
│  └────────────────────────┘  └────────────────────┘ │
│                                                       │
│  ┌────────────────────────┐  ┌────────────────────┐ │
│  │  Estatísticas Conteúdo  │  │  Conteúdo Recente │ │
│  │  [Posts/Páginas]        │  │  [Lista]          │ │
│  └────────────────────────┘  └────────────────────┘ │
│                                                       │
│  ┌────────────────────────┐  ┌────────────────────┐ │
│  │  Próximos Eventos      │  │  Comentários      │ │
│  │  [Calendário]          │  │  [Se houver]      │ │
│  └────────────────────────┘  └────────────────────┘ │
│                                                       │
└─────────────────────────────────────────────────────┘
```

### Cores:
- **Primária**: #5CE1E6 (Ciano) - para destaques e ações
- **Fundo**: Branco/Cinza claro
- **Bordas**: Cinza claro (#F0F0F0)
- **Textos**: Preto (#000) e Cinza escuro (#484848)

---

## 🔧 FUNCIONALIDADES TÉCNICAS

### Widgets Customizados a Criar:

1. **SG: Resumo do Negócio**
   - Query para produtos WooCommerce
   - Query para pedidos
   - Cálculo de receita
   - Contagem de clientes

2. **SG: Próximos Eventos**
   - Query para eventos ETN
   - Ordenação por data
   - Filtro de eventos futuros

3. **SG: Estatísticas de Conteúdo**
   - Contagem de posts/páginas
   - Status de publicação
   - Rascunhos pendentes

4. **SG: Pedidos Recentes** (pode usar widget WooCommerce customizado)
   - Query para últimos pedidos
   - Status visual
   - Links de ação rápida

---

## ✅ CHECKLIST DE IMPLEMENTAÇÃO

### Fase 1: Limpeza
- [ ] Remover widgets desnecessários
- [ ] Remover banners padrão
- [ ] Limpar área de widgets vazios

### Fase 2: Widgets Essenciais
- [ ] Criar widget "Resumo do Negócio"
- [ ] Criar widget "Estatísticas de Conteúdo"
- [ ] Criar widget "Próximos Eventos"
- [ ] Criar widget "Pedidos Recentes"
- [ ] Criar widget "Conteúdo Recente"
- [ ] Criar widget "Configurações Rápidas" (Header, Footer, Páginas)

### Fase 3: Estilização
- [ ] Aplicar paleta de cores do projeto
- [ ] Estilizar cards/widgets
- [ ] Adicionar ícones apropriados
- [ ] Tornar responsivo

### Fase 4: Funcionalidades
- [ ] Adicionar links de ação rápida
- [ ] Implementar queries eficientes
- [ ] Adicionar cache (se necessário)
- [ ] Testar performance

---

## 🚀 BENEFÍCIOS

### Para o Administrador:
✅ **Visão clara** do negócio em um só lugar
✅ **Acesso rápido** às informações essenciais
✅ **Ações rápidas** sem navegar por várias páginas
✅ **Foco** no que realmente importa
✅ **Dashboard limpo** e profissional

### Para o Negócio:
✅ **Monitoramento** de vendas em tempo real
✅ **Gestão eficiente** de conteúdo
✅ **Planejamento** de eventos futuros
✅ **Identificação rápida** de problemas

---

## 📝 OBSERVAÇÕES FINAIS

- Os widgets devem ser **arrastáveis** (usar API de widgets do WordPress)
- Manter **performance** otimizada (queries eficientes)
- **Priorizar** informações mais usadas no topo
- Permitir **customização** pelo usuário (se quiser reorganizar)
- **Responsivo** para acesso mobile do admin

---

**Próximo Passo**: Implementar os widgets customizados seguindo este planejamento.

