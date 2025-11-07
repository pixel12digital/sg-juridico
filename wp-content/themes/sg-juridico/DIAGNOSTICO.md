# DIAGNÓSTICO COMPLETO - PRODUTOS RELACIONADOS

## PROBLEMA IDENTIFICADO
- Usuário quer 4 produtos em uma linha
- Script está removendo produtos válidos que têm imagens placeholder
- Grid não está sendo configurado corretamente para 4 colunas

## PLANO DE INVESTIGAÇÃO

### 1. Execute este script no console para diagnóstico completo:

```javascript
(function() {
    console.log('═══════════════════════════════════════════════════════');
    console.log('🔍 DIAGNÓSTICO COMPLETO - PRODUTOS RELACIONADOS');
    console.log('═══════════════════════════════════════════════════════');
    
    // 1. Verificar contexto
    var isProductPage = document.body.classList.contains('single-product');
    console.log('\n1️⃣ CONTEXTO:');
    console.log('   É página de produto?', isProductPage);
    
    if (!isProductPage) {
        console.error('❌ Não é página de produto!');
        return;
    }
    
    // 2. Verificar seção
    var relatedSection = document.querySelector('body.single-product .related.products');
    var productsList = relatedSection ? relatedSection.querySelector('ul.products') : null;
    
    console.log('\n2️⃣ SEÇÃO:');
    console.log('   .related.products existe?', !!relatedSection);
    console.log('   ul.products existe?', !!productsList);
    
    if (!productsList) {
        console.error('❌ Lista não encontrada!');
        return;
    }
    
    // 3. Analisar elementos
    var allLis = Array.from(productsList.querySelectorAll('li'));
    console.log('\n3️⃣ ELEMENTOS ENCONTRADOS:', allLis.length);
    
    allLis.forEach(function(li, index) {
        var title = li.querySelector('h2, .woocommerce-loop-product__title, .product-title');
        var link = li.querySelector('a[href]');
        var img = li.querySelector('img');
        
        var info = {
            index: index,
            isProduct: li.classList.contains('product'),
            hasTitle: !!title,
            titleText: title ? title.textContent.trim().substring(0, 50) : 'SEM TÍTULO',
            hasLink: !!link,
            linkHref: link ? (link.href || '').substring(0, 60) : 'SEM LINK',
            hasImage: !!img,
            imgSrc: img ? (img.src || '').substring(0, 70) : 'SEM IMAGEM',
            imgWidth: img ? (img.naturalWidth || img.width || 0) : 0,
            imgHeight: img ? (img.naturalHeight || img.height || 0) : 0
        };
        
        console.log('   📦 Elemento ' + index + ':', info);
    });
    
    // 4. Verificar CSS
    var computed = window.getComputedStyle(productsList);
    console.log('\n4️⃣ CSS APLICADO:');
    console.log('   display:', computed.display);
    console.log('   grid-template-columns:', computed.gridTemplateColumns);
    console.log('   gap:', computed.gap);
    console.log('   style inline:', productsList.getAttribute('style') || 'NENHUM');
    console.log('   classes:', productsList.className);
    
    // 5. Verificar scripts
    console.log('\n5️⃣ SCRIPTS:');
    console.log('   window.sgFixRelatedProducts:', typeof window.sgFixRelatedProducts);
    console.log('   window.sgRemovePlaceholders:', typeof window.sgRemovePlaceholders);
    
    // 6. Resumo
    var produtosValidos = allLis.filter(function(li) {
        var title = li.querySelector('h2, .woocommerce-loop-product__title, .product-title');
        var link = li.querySelector('a[href]');
        return (title && title.textContent.trim().length > 3) || 
               (link && link.href && link.href.length > 5);
    }).length;
    
    console.log('\n6️⃣ RESUMO:');
    console.log('   Total de elementos:', allLis.length);
    console.log('   Produtos válidos (com título ou link):', produtosValidos);
    console.log('   Grid configurado:', computed.gridTemplateColumns);
    console.log('   Problema identificado:', produtosValidos < 4 ? 'Poucos produtos válidos' : 'Grid incorreto');
    
    console.log('\n═══════════════════════════════════════════════════════');
})();
```

### 2. Execute este script para CORRIGIR o problema:

```javascript
(function() {
    console.log('🔧 CORREÇÃO DEFINITIVA...');
    
    var productsList = document.querySelector('body.single-product .related.products ul.products');
    if (!productsList) {
        console.error('❌ Lista não encontrada!');
        return;
    }
    
    // REMOVER apenas elementos completamente vazios
    var allLis = Array.from(productsList.querySelectorAll('li'));
    var removed = 0;
    
    allLis.forEach(function(li, index) {
        var title = li.querySelector('h2, .woocommerce-loop-product__title, .product-title');
        var link = li.querySelector('a[href]');
        var cardText = li.textContent.trim();
        
        var hasTitle = title && title.textContent.trim().length > 3;
        var hasValidLink = link && link.href && link.href.trim() !== '' && !link.href.includes('#') && link.href.length > 5;
        var isCompletelyEmpty = !hasTitle && !hasValidLink && cardText.length < 5;
        
        // MANTER se tem título OU link válido
        if (hasTitle || hasValidLink) {
            console.log('✅ Mantendo:', hasTitle ? title.textContent.trim().substring(0, 40) : 'link válido');
        } else if (isCompletelyEmpty) {
            console.log('❌ Removendo vazio:', index);
            li.remove();
            removed++;
        }
    });
    
    // Limitar a 4 produtos
    var remaining = Array.from(productsList.querySelectorAll('li'));
    if (remaining.length > 4) {
        for (var i = 4; i < remaining.length; i++) {
            remaining[i].remove();
        }
    }
    
    // SEMPRE aplicar grid de 4 colunas
    productsList.removeAttribute('style');
    productsList.classList.remove('columns-4', 'columns-3', 'columns-2', 'columns-1');
    productsList.style.setProperty('display', 'grid', 'important');
    productsList.style.setProperty('grid-template-columns', 'repeat(4, 1fr)', 'important');
    productsList.style.setProperty('gap', '18px', 'important');
    
    console.log('✅ Concluído! Produtos:', productsList.querySelectorAll('li').length);
})();
```

## PRÓXIMOS PASSOS

1. Execute o script de diagnóstico primeiro
2. Compartilhe os resultados
3. Execute o script de correção
4. Verifique se funcionou

