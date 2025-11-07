# Guia de Verificação no DevTools - Página de Produto

## Passo 1: Abrir DevTools e Limpar Cache

1. Abra a página do produto no navegador
2. Pressione **F12** ou **Ctrl+Shift+I** (Windows) / **Cmd+Option+I** (Mac)
3. Vá na aba **Network** (Rede)
4. Marque a opção **"Disable cache"** (Desabilitar cache)
5. Pressione **Ctrl+Shift+R** (Windows) / **Cmd+Shift+R** (Mac) para fazer hard refresh

## Passo 2: Verificar se o CSS está Carregado

1. Na aba **Network**, filtre por **CSS**
2. Procure por `style.css` e verifique se está carregado (status 200)
3. Clique em `style.css` e veja o conteúdo - procure por `.single-product-content` e verifique se tem `padding-top: 0`

## Passo 3: Inspecionar o Container Principal

1. Na aba **Elements** (Elementos), clique no ícone de inspeção (canto superior esquerdo)
2. Clique no container branco que envolve a imagem e o título (`.single-product-content`)
3. No painel direito, vá na aba **Computed** (Computado)
4. Procure por estas propriedades e verifique os valores:

### O que verificar:

- **`padding-top`**: Deve ser **0px** (não 20px ou outro valor)
- **`align-items`**: Deve ser **start** ou **flex-start**
- **`grid-template-columns`**: Deve mostrar algo como **minmax(560px, 640px)**
- **`margin-top`**: Deve ser **0px**

### Se não estiver correto:

- Vá na aba **Styles** (Estilos)
- Procure por `.single-product-content` e veja qual regra está sendo aplicada
- Se houver uma regra riscada (tachada), significa que está sendo sobrescrita
- Procure por regras com `!important` - elas devem estar ativas

## Passo 4: Verificar a Coluna da Galeria (Esquerda)

1. Inspecione o elemento `.woocommerce-product-gallery`
2. Na aba **Computed**, verifique:

- **`padding-top`**: Deve ser **0px**
- **`margin-top`**: Deve ser **0px**
- **`display`**: Deve ser **flex**
- **`flex-direction`**: Deve ser **column**

3. Inspecione `.woocommerce-product-gallery__wrapper` e verifique:
- **`padding-top`**: Deve ser **0px**
- **`margin-top`**: Deve ser **0px**

4. Inspecione `.woocommerce-product-gallery__image` e verifique:
- **`margin-top`**: Deve ser **0px**
- **`padding`**: Deve ser **16px** (ou 18px/20px em telas maiores)
- **`max-width`**: Deve ser **640px** (em telas ≥1200px)

## Passo 5: Verificar a Coluna do Título (Direita)

1. Inspecione o elemento `.summary.entry-summary`
2. Na aba **Computed**, verifique:

- **`padding-top`**: Deve ser **1px** (para evitar colapso do margin-top do H1)
- **`margin-top`**: Deve ser **0px**

3. Inspecione o elemento `.product_title` (H1)
4. Na aba **Computed**, verifique:

- **`margin-top`**: Deve ser **0px**
- **`padding-top`**: Deve ser **0px**

## Passo 6: Verificar Alinhamento Visual

1. Na aba **Elements**, selecione `.single-product-content`
2. No painel direito, vá em **Layout** (se disponível) ou use a aba **Computed**
3. Verifique as coordenadas:

- **`top`**: Anote o valor do container
- Inspecione `.woocommerce-product-gallery__image` e veja seu `top`
- Inspecione `.product_title` e veja seu `top`
- Os valores de `top` devem ser muito próximos (diferença de poucos pixels)

## Passo 7: Verificar Estilos Inline

1. Na aba **Elements**, selecione `.single-product-content`
2. Veja se há um atributo `style` no elemento HTML
3. Se houver, veja o que está definido - pode estar sobrescrevendo o CSS

## Passo 8: Verificar Console por Erros

1. Vá na aba **Console**
2. Procure por erros em vermelho
3. Se houver erros relacionados a CSS ou JavaScript, anote-os

## Passo 9: Forçar Aplicação via Console

Se os estilos não estiverem sendo aplicados, execute no Console:

```javascript
// Forçar alinhamento no topo
document.querySelector('.single-product-content').style.setProperty('padding-top', '0', 'important');
document.querySelector('.single-product-content').style.setProperty('align-items', 'start', 'important');
document.querySelector('.woocommerce-product-gallery').style.setProperty('padding-top', '0', 'important');
document.querySelector('.woocommerce-product-gallery').style.setProperty('margin-top', '0', 'important');
document.querySelector('.summary.entry-summary').style.setProperty('padding-top', '1px', 'important');
document.querySelector('.product_title').style.setProperty('margin-top', '0', 'important');
```

Se isso funcionar visualmente, significa que o CSS não está sendo aplicado corretamente.

## Passo 10: Verificar Ordem de Carregamento do CSS

1. Na aba **Network**, filtre por **CSS**
2. Veja a ordem de carregamento dos arquivos CSS
3. O `style.css` deve ser carregado DEPOIS do CSS do WooCommerce
4. Se não estiver, pode ser problema de ordem de enfileiramento

## O que me enviar:

1. Screenshot da aba **Computed** do `.single-product-content` mostrando `padding-top`, `align-items`, `margin-top`
2. Screenshot da aba **Computed** do `.woocommerce-product-gallery` mostrando `padding-top` e `margin-top`
3. Screenshot da aba **Computed** do `.product_title` mostrando `margin-top`
4. Screenshot da aba **Styles** do `.single-product-content` mostrando quais regras estão ativas/riscadas
5. Qualquer erro do Console relacionado a CSS


