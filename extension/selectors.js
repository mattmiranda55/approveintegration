// Selectors config - mirrors the Laravel config/approve-selectors.php
const SELECTORS = {
  product: {
    wrappers: [
      '.product',
      '#main .product',
      '.product-information__grid',
      '#ProductSection',
      '.product-details-full-main-content',
      '.summary.entry-summary',
    ],
    model_selectors: [
      '.product_title.entry-title',
      '.product-info__block-item h1',
      'h1[itemprop="name"]',
      '.h2.product-heading.heading-font',
      '.js-store-prod-name.js-product-name',
      '.product-card__title a',
      '.product__title',
    ],
    price_selectors: [
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
    qty_selectors: [
      'input[name="quantity"]',
      '.input-text.qty.text',
      '.quantity-input.product-quantity',
      '.t-store__prod__quantity-input',
      '.js-qty input',
    ],
    sku_selectors: [
      '.js-store-prod-sku.js-product-sku',
      '.sku',
      '[itemprop="sku"]',
    ],
    insert_after_selectors: [
      'form.cart',
      '.cart',
      'buy-buttons',
      '.pdp-action-wrapper',
      '.t-store__prod-popup__btn.t-btn.t-btn_sm',
      'form#addToCartForm-product-template',
      '.product-form__buttons',
    ],
    inc_qty_selectors: [
      'button[title="Increase"]',
      '.t-store__prod__quantity__plus',
      '.quantity-plus',
      '.qty-btn-inc',
    ],
    dec_qty_selectors: [
      'button[title="Decrease"]',
      '.t-store__prod__quantity__minus',
      '.quantity-minus',
      '.qty-btn-dec',
    ],
    options_selectors: [
      'select',
      'input[type="radio"]',
      'input[type="checkbox"]',
    ],
  },

  cart: {
    wrappers: [
      '.cart',
      '.cart-drawer__wrapper',
      '.cart-detailed-body',
      '.cart-items',
      '.woocommerce-cart-form',
    ],
    item_selectors: [
      '.order-summary__body tr',
      '.cart-items__table-row',
      '.cart-item',
      '.woocommerce-cart-form__cart-item',
      'tr.cart_item',
    ],
    item_model_selectors: [
      '.line-item__info div a span',
      '.cart-items__title',
      '.cart-item__title',
      '.product-name a',
      '.cart-item__name',
    ],
    item_price_selectors: [
      'sale-price',
      '.cart-items__price.cart-secondary-typography',
      '.cart-item__price',
      '.product-price',
      '.woocommerce-Price-amount',
    ],
    item_qty_selectors: [
      '.quantity-input',
      'input.qty',
      '.cart-item__qty input',
      '.quantity input',
    ],
    insert_after_selectors: [
      '.cart-form.rounded button[name="checkout"]',
      '.cart-summary-btn-paypal-express',
      '.checkout-button',
      '.wc-proceed-to-checkout',
      'button[name="checkout"]',
    ],
  },

  gallery: {
    wrappers: [
      '.collection__results',
      '#main .collection .collection__results product-list',
      '.facets-facet-browse-items',
      '.products',
      '.product-grid',
      '.collection-products',
    ],
    item_selectors: [
      'product-card',
      '.product-card',
      '.product-item',
      '.grid-item',
      '.collection-product',
    ],
    item_model_selectors: [
      '.product-card__title a',
      '.product-item__title',
      '.product-card__name',
      '.grid-item__title a',
    ],
    item_price_selectors: [
      'sale-price',
      '.price-list',
      '.product-card__price',
      '.product-item__price',
      '.price',
    ],
    insert_after_selectors: [
      '.price-list',
      '.product-card__footer',
      '.product-item__actions',
    ],
  },
};

// CMS detection signatures
const CMS_SIGNATURES = {
  'Shopify': [
    () => !!window.Shopify,
    () => !!document.querySelector('script[src*="cdn.shopify.com"]'),
    () => !!document.querySelector('link[href*="cdn.shopify.com"]'),
  ],
  'WooCommerce': [
    () => document.body.classList.contains('woocommerce'),
    () => !!document.querySelector('.woocommerce'),
    () => !!document.querySelector('script[src*="woocommerce"]'),
  ],
  'BigCommerce': [
    () => !!window.BCData,
    () => !!document.querySelector('script[src*="bigcommerce.com"]'),
  ],
  'Magento': [
    () => !!window.Mage,
    () => !!document.querySelector('script[src*="mage"]'),
    () => document.body.classList.contains('cms-index-index'),
  ],
  'Squarespace': [
    () => !!window.Static,
    () => !!document.querySelector('script[src*="squarespace.com"]'),
  ],
  'Wix': [
    () => !!window.wixBiSession,
    () => !!document.querySelector('meta[name="generator"][content*="Wix"]'),
  ],
  'PrestaShop': [
    () => !!window.prestashop,
    () => !!document.querySelector('meta[name="generator"][content*="PrestaShop"]'),
  ],
  'Tilda': [
    () => !!document.querySelector('[class*="t-store"]'),
    () => !!document.querySelector('script[src*="tilda"]'),
  ],
};
