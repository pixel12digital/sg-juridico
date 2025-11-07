/**
 * SCRIPT DE DIAGNÓSTICO COMPLETO - PRODUTOS RELACIONADOS
 * Execute este script no console para identificar TODOS os problemas
 */

(function diagnosticoCompleto() {
    console.log('═══════════════════════════════════════════════════════');
    console.log('🔍 DIAGNÓSTICO COMPLETO - PRODUTOS RELACIONADOS');
    console.log('═══════════════════════════════════════════════════════');
    
    // 1. VERIFICAR SE ESTAMOS EM PÁGINA DE PRODUTO
    console.log('\n1️⃣ VERIFICAÇÃO DE CONTEXTO:');
    var isProductPage = document.body.classList.contains('single-product');
    console.log('   ✓ É página de produto?', isProductPage);
    if (!isProductPage) {
        console.error('   ❌ ERRO: Não é uma página de produto!');
        return;
    }
    
    // 2. VERIFICAR SEÇÃO DE PRODUTOS RELACIONADOS
    console.log('\n2️⃣ VERIFICAÇÃO DA SEÇÃO:');
    var relatedSection = document.querySelector('body.single-product .related.products');
    console.log('   ✓ Seção .related.products existe?', !!relatedSection);
    if (!relatedSection) {
        console.error('   ❌ ERRO: Seção de produtos relacionados não encontrada!');
        return;
    }
    
    var productsList = relatedSection.querySelector('ul.products');
    console.log('   ✓ Lista ul.products existe?', !!productsList);
    if (!productsList) {
        console.error('   ❌ ERRO: Lista ul.products não encontrada!');
        return;
    }
    
    // 3. CONTAR ELEMENTOS
    console.log('\n3️⃣ CONTAGEM DE ELEMENTOS:');
    var allLis = productsList.querySelectorAll('li');
    var productLis = productsList.querySelectorAll('li.product');
    console.log('   ✓ Total de <li>:', allLis.length);
    console.log('   ✓ Total de <li.product>:', productLis.length);
    
    // 4. ANALISAR CADA ELEMENTO
    console.log('\n4️⃣ ANÁLISE DETALHADA DE CADA ELEMENTO:');
    var elementos = [];
    allLis.forEach(function(li, index) {
        var img = li.querySelector('img');
        var title = li.querySelector('h2, .woocommerce-loop-product__title, .product-title');
        var link = li.querySelector('a[href]');
        
        var info = {
            index: index,
            hasClassProduct: li.classList.contains('product'),
            hasImage: !!img,
            imgSrc: img ? (img.src || '').substring(0, 60) : 'SEM IMAGEM',
            imgWidth: img ? (img.naturalWidth || img.width || 0) : 0,
            imgHeight: img ? (img.naturalHeight || img.height || 0) : 0,
            hasTitle: !!title,
            titleText: title ? title.textContent.trim().substring(0, 50) : 'SEM TÍTULO',
            hasLink: !!link,
            linkHref: link ? (link.href || '').substring(0, 60) : 'SEM LINK',
            innerHTML: li.innerHTML.substring(0, 100),
            textContent: li.textContent.trim().substring(0, 50)
        };
        
        elementos.push(info);
        console.log('   📦 Elemento ' + index + ':', info);
    });
    
    // 5. VERIFICAR CSS APLICADO
    console.log('\n5️⃣ VERIFICAÇÃO DE CSS:');
    var computedStyle = window.getComputedStyle(productsList);
    console.log('   ✓ display:', computedStyle.display);
    console.log('   ✓ grid-template-columns:', computedStyle.gridTemplateColumns);
    console.log('   ✓ gap:', computedStyle.gap);
    console.log('   ✓ width:', computedStyle.width);
    console.log('   ✓ max-width:', computedStyle.maxWidth);
    
    // Verificar estilos inline
    console.log('   ✓ style inline:', productsList.getAttribute('style') || 'NENHUM');
    
    // 6. VERIFICAR CLASSES
    console.log('\n6️⃣ VERIFICAÇÃO DE CLASSES:');
    console.log('   ✓ Classes do ul.products:', productsList.className);
    
    // 7. VERIFICAR CONFLITOS DE SCRIPTS
    console.log('\n7️⃣ VERIFICAÇÃO DE SCRIPTS:');
    console.log('   ✓ window.sgRemovePlaceholders existe?', typeof window.sgRemovePlaceholders);
    console.log('   ✓ window.applyRelatedProductsStyles existe?', typeof window.applyRelatedProductsStyles);
    
    // 8. VERIFICAR WOOCOMMERCE
    console.log('\n8️⃣ VERIFICAÇÃO WOOCOMMERCE:');
    var woocommerce = window.wc_add_to_cart_params || window.woocommerce_params;
    console.log('   ✓ WooCommerce carregado?', !!woocommerce);
    
    // 9. IDENTIFICAR PLACEHOLDERS
    console.log('\n9️⃣ IDENTIFICAÇÃO DE PLACEHOLDERS:');
    var placeholders = [];
    elementos.forEach(function(el, index) {
        var isPlaceholder = false;
        var reasons = [];
        
        if (el.imgSrc) {
            var imgSrcLower = el.imgSrc.toLowerCase();
            if (imgSrcLower.includes('placeholder')) {
                isPlaceholder = true;
                reasons.push('src contém "placeholder"');
            }
            if (imgSrcLower.includes('mountain')) {
                isPlaceholder = true;
                reasons.push('src contém "mountain"');
            }
            if (imgSrcLower.includes('woocommerce-placeholder')) {
                isPlaceholder = true;
                reasons.push('src contém "woocommerce-placeholder"');
            }
            if (el.imgWidth <= 100 && el.imgHeight <= 100) {
                isPlaceholder = true;
                reasons.push('imagem muito pequena (' + el.imgWidth + 'x' + el.imgHeight + ')');
            }
        }
        
        if (!el.hasTitle && !el.hasImage) {
            isPlaceholder = true;
            reasons.push('sem título e sem imagem');
        }
        
        if (isPlaceholder) {
            placeholders.push({ index: index, reasons: reasons, element: el });
            console.log('   ❌ Elemento ' + index + ' é PLACEHOLDER:', reasons);
        } else {
            console.log('   ✅ Elemento ' + index + ' é PRODUTO VÁLIDO');
        }
    });
    
    // 10. RESUMO E RECOMENDAÇÕES
    console.log('\n🔟 RESUMO E RECOMENDAÇÕES:');
    var produtosValidos = elementos.length - placeholders.length;
    console.log('   ✓ Total de elementos:', elementos.length);
    console.log('   ✓ Placeholders encontrados:', placeholders.length);
    console.log('   ✓ Produtos válidos:', produtosValidos);
    console.log('   ✓ Produtos necessários para 4 colunas: 4');
    
    if (produtosValidos < 4) {
        console.warn('   ⚠️ PROBLEMA: Apenas ' + produtosValidos + ' produtos válidos encontrados!');
        console.warn('   ⚠️ SOLUÇÃO: Verificar se o WooCommerce está retornando 4 produtos relacionados');
        console.warn('   ⚠️ Verificar filtro: woocommerce_output_related_products_args');
    }
    
    if (computedStyle.gridTemplateColumns && !computedStyle.gridTemplateColumns.includes('repeat(4')) {
        console.warn('   ⚠️ PROBLEMA: Grid não está configurado para 4 colunas!');
        console.warn('   ⚠️ Grid atual:', computedStyle.gridTemplateColumns);
    }
    
    console.log('\n═══════════════════════════════════════════════════════');
    console.log('✅ DIAGNÓSTICO CONCLUÍDO');
    console.log('═══════════════════════════════════════════════════════');
    
    return {
        isProductPage: isProductPage,
        elementos: elementos,
        placeholders: placeholders,
        produtosValidos: produtosValidos,
        gridColumns: computedStyle.gridTemplateColumns,
        needsFix: produtosValidos < 4 || !computedStyle.gridTemplateColumns.includes('repeat(4')
    };
})();

