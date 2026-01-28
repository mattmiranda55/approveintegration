<?php

/**
 * Common CSS Selectors from Approve Payment Integrations
 * Extracted from approvepayments/integration_code repository - integrations folder
 */

return [

    // =========================================================================
    // PRODUCT PAGE INTEGRATION SELECTORS
    // =========================================================================

    'product' => [
        // Product page wrapper selectors (used to identify product pages)
        'wrappers' => [
            '.product',
            '#main .product',
            '.product-information__grid',
            '#ProductSection',
            '.product-details-full-main-content',
            '.summary.entry-summary',
        ],

        // Product model/title element selectors
        'model_selectors' => [
            '.product_title.entry-title',
            '.product-info__block-item h1',
            'h1[itemprop="name"]',
            '.h2.product-heading.heading-font',
            '.js-store-prod-name.js-product-name',
            '.product-card__title a',
            '.product__title',
        ],

        // Product price element selectors
        'price_selectors' => [
            'sale-price',
            '.price.price--regular',
            '.price-item.actual-price.h3',
            '.woocommerce-Price-amount.amount bdi',
            '.summary.entry-summary .woocommerce-Price-amount.amount bdi',
            '.rightpress_product_price_live_update .price span bdi',
            '.js-product-price.js-store-prod-price-val',
            '.price-list',
            '#productPrice-product-template .visually-hidden',
        ],

        // Product quantity input selectors
        'qty_selectors' => [
            'input[name="quantity"]',
            '.input-text.qty.text',
            '.quantity-input.product-quantity',
            '.t-store__prod__quantity-input',
            '.js-qty input',
        ],

        // Product SKU element selectors
        'sku_selectors' => [
            '.js-store-prod-sku.js-product-sku',
            '.sku',
            '[itemprop="sku"]',
        ],

        // Button insert after element selectors (product page)
        'insert_after_selectors' => [
            'form.cart',
            '.cart',
            'buy-buttons',
            '.pdp-action-wrapper',
            '.t-store__prod-popup__btn.t-btn.t-btn_sm',
            'form#addToCartForm-product-template',
            '.product-form__buttons',
        ],

        // Product quantity increment/decrement button selectors
        'inc_qty_selectors' => [
            'button[title="Increase"]',
            '.t-store__prod__quantity__plus',
            '.quantity-plus',
            '.qty-btn-inc',
        ],

        'dec_qty_selectors' => [
            'button[title="Decrease"]',
            '.t-store__prod__quantity__minus',
            '.quantity-minus',
            '.qty-btn-dec',
        ],

        // Product options wrapper selectors
        'options_selectors' => [
            'select',
            'input[type="radio"]',
            'input[type="checkbox"]',
        ],
    ],

    // =========================================================================
    // CART PAGE INTEGRATION SELECTORS
    // =========================================================================

    'cart' => [
        // Cart URL pathnames
        'urls' => [
            '/cart',
            '/cart/',
            '/shopping-cart',
        ],

        // Cart wrapper element selectors
        'wrappers' => [
            '.cart',
            '.cart-drawer__wrapper',
            '.cart-detailed-body',
            '.cart-items',
            '.woocommerce-cart-form',
        ],

        // Cart item row element selectors
        'item_selectors' => [
            '.order-summary__body tr',
            '.cart-items__table-row',
            '.cart-item',
            '.woocommerce-cart-form__cart-item',
            'tr.cart_item',
        ],

        // Cart item model/title element selectors
        'item_model_selectors' => [
            '.line-item__info div a span',
            '.cart-items__title',
            '.cart-item__title',
            '.product-name a',
            '.cart-item__name',
        ],

        // Cart item price element selectors
        'item_price_selectors' => [
            'sale-price',
            '.cart-items__price.cart-secondary-typography',
            '.cart-item__price',
            '.product-price',
            '.woocommerce-Price-amount',
        ],

        // Cart item quantity element selectors
        'item_qty_selectors' => [
            '.quantity-input',
            'input.qty',
            '.cart-item__qty input',
            '.quantity input',
        ],

        // Cart button insert after element selectors
        'insert_after_selectors' => [
            '.cart-form.rounded button[name="checkout"]',
            '.cart-summary-btn-paypal-express',
            '.checkout-button',
            '.wc-proceed-to-checkout',
            'button[name="checkout"]',
        ],
    ],

    // =========================================================================
    // MINI CART INTEGRATION SELECTORS
    // =========================================================================

    'mini_cart' => [
        // Mini cart wrapper selectors
        'wrappers' => [
            '.cart-drawer__wrapper',
            '.mini-cart',
            '.cart-drawer',
            '#mini-cart',
            '.side-cart',
            '.drawer-cart',
        ],

        // Mini cart item selectors
        'item_selectors' => [
            '.cart-items__table-row',
            '.mini-cart-item',
            '.cart-drawer__item',
            '.side-cart__item',
        ],

        // Mini cart item model selectors
        'item_model_selectors' => [
            '.cart-items__title',
            '.mini-cart-item__title',
            '.cart-drawer__item-title',
        ],

        // Mini cart item price selectors
        'item_price_selectors' => [
            '.cart-items__price.cart-secondary-typography',
            '.mini-cart-item__price',
            '.cart-drawer__item-price',
        ],
    ],

    // =========================================================================
    // GALLERY PAGE INTEGRATION SELECTORS
    // =========================================================================

    'gallery' => [
        // Gallery wrapper selectors
        'wrappers' => [
            '.collection__results',
            '#main .collection .collection__results product-list',
            '.facets-facet-browse-items',
            '.products',
            '.product-grid',
            '.collection-products',
        ],

        // Gallery item card selectors
        'item_selectors' => [
            'product-card',
            '.product-card',
            '.product-item',
            '.grid-item',
            '.collection-product',
        ],

        // Gallery item model/title selectors
        'item_model_selectors' => [
            '.product-card__title a',
            '.product-item__title',
            '.product-card__name',
            '.grid-item__title a',
        ],

        // Gallery item price selectors
        'item_price_selectors' => [
            'sale-price',
            '.price-list',
            '.product-card__price',
            '.product-item__price',
            '.price',
        ],

        // Gallery item insert after selectors
        'insert_after_selectors' => [
            '.price-list',
            '.product-card__footer',
            '.product-item__actions',
        ],
    ],
];
