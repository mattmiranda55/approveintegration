// State
let selectedType = null;

// DOM Elements
const cmsDisplay = document.getElementById('cms-display');
const analyzeBtn = document.getElementById('analyze-btn');
const resultsSection = document.getElementById('results-section');
const resultsTextarea = document.getElementById('results');
const copyBtn = document.getElementById('copy-btn');
const copyFeedback = document.getElementById('copy-feedback');
const errorSection = document.getElementById('error-section');
const errorMessage = document.getElementById('error-message');
const toggleBtns = document.querySelectorAll('.toggle-btn');

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  detectCMS();
  setupToggleButtons();
  setupAnalyzeButton();
  setupCopyButton();
});

// Toggle button handling
function setupToggleButtons() {
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const type = btn.dataset.type;
      
      // Toggle selection
      if (selectedType === type) {
        btn.classList.remove('active');
        selectedType = null;
        analyzeBtn.disabled = true;
        analyzeBtn.textContent = 'Select a page type';
      } else {
        toggleBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        selectedType = type;
        analyzeBtn.disabled = false;
        analyzeBtn.textContent = `Analyze ${type.charAt(0).toUpperCase() + type.slice(1)} Page`;
      }
    });
  });
}

// CMS Detection
async function detectCMS() {
  try {
    const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
    
    const results = await chrome.scripting.executeScript({
      target: { tabId: tab.id },
      func: detectCMSInPage
    });

    const cms = results[0]?.result || 'Unknown';
    cmsDisplay.textContent = cms;
    if (cms !== 'Unknown') {
      cmsDisplay.classList.add('detected');
    }
  } catch (err) {
    cmsDisplay.textContent = 'Unable to detect';
    console.error('CMS detection error:', err);
  }
}

// Function injected into page for CMS detection
function detectCMSInPage() {
  // Shopify
  if (window.Shopify) return 'Shopify';
  if (document.querySelector('script[src*="cdn.shopify.com"]')) return 'Shopify';
  if (document.querySelector('link[href*="cdn.shopify.com"]')) return 'Shopify';
  if (document.querySelector('meta[name="shopify-checkout-api-token"]')) return 'Shopify';
  
  // WooCommerce
  if (document.body.classList.contains('woocommerce')) return 'WooCommerce';
  if (document.querySelector('.woocommerce')) return 'WooCommerce';
  if (document.querySelector('script[src*="woocommerce"]')) return 'WooCommerce';
  
  // BigCommerce
  if (window.BCData) return 'BigCommerce';
  if (document.querySelector('script[src*="bigcommerce.com"]')) return 'BigCommerce';
  
  // Magento
  if (window.Mage) return 'Magento';
  if (document.querySelector('script[src*="mage"]')) return 'Magento';
  if (document.body.classList.contains('cms-index-index')) return 'Magento';
  
  // Squarespace
  if (window.Static) return 'Squarespace';
  if (document.querySelector('script[src*="squarespace.com"]')) return 'Squarespace';
  
  // Wix
  if (window.wixBiSession) return 'Wix';
  if (document.querySelector('meta[name="generator"][content*="Wix"]')) return 'Wix';
  
  // PrestaShop
  if (window.prestashop) return 'PrestaShop';
  if (document.querySelector('meta[name="generator"][content*="PrestaShop"]')) return 'PrestaShop';
  
  // Tilda
  if (document.querySelector('[class*="t-store"]')) return 'Tilda';
  if (document.querySelector('script[src*="tilda"]')) return 'Tilda';
  
  return 'Unknown';
}

// Analyze button
function setupAnalyzeButton() {
  analyzeBtn.addEventListener('click', async () => {
    if (!selectedType) return;

    // Show loading state
    analyzeBtn.classList.add('loading');
    analyzeBtn.disabled = true;
    hideError();
    hideResults();

    try {
      const [tab] = await chrome.tabs.query({ active: true, currentWindow: true });
      
      const selectorsForType = SELECTORS[selectedType];
      
      const results = await chrome.scripting.executeScript({
        target: { tabId: tab.id },
        func: analyzePageSelectors,
        args: [selectorsForType, selectedType]
      });

      const matchedSelectors = results[0]?.result;
      
      if (matchedSelectors && Object.keys(matchedSelectors).length > 0) {
        showResults(matchedSelectors);
      } else {
        showError('No matching selectors found on this page.');
      }
    } catch (err) {
      showError(`Analysis failed: ${err.message}`);
      console.error('Analysis error:', err);
    } finally {
      analyzeBtn.classList.remove('loading');
      analyzeBtn.disabled = false;
    }
  });
}

// Function injected into page for selector analysis
function analyzePageSelectors(selectors, pageType) {
  const matched = {};

  for (const [category, selectorList] of Object.entries(selectors)) {
    for (const selector of selectorList) {
      try {
        const elements = document.querySelectorAll(selector);
        if (elements.length > 0) {
          if (!matched[category]) {
            matched[category] = [];
          }
          
          const firstEl = elements[0];
          let sampleText = firstEl.textContent?.trim().substring(0, 50) || '';
          if (sampleText.length === 50) sampleText += '...';

          matched[category].push({
            selector,
            count: elements.length,
            tag: firstEl.tagName.toLowerCase(),
            sample: sampleText
          });
        }
      } catch (e) {
        // Invalid selector, skip
      }
    }
  }

  // Add metadata
  return {
    pageType,
    url: window.location.href,
    timestamp: new Date().toISOString(),
    selectors: matched
  };
}

// Show results
function showResults(data) {
  resultsTextarea.value = JSON.stringify(data, null, 2);
  resultsSection.classList.remove('hidden');
}

// Hide results
function hideResults() {
  resultsSection.classList.add('hidden');
  resultsTextarea.value = '';
}

// Show error
function showError(message) {
  errorMessage.textContent = message;
  errorSection.classList.remove('hidden');
}

// Hide error
function hideError() {
  errorSection.classList.add('hidden');
}

// Copy button
function setupCopyButton() {
  copyBtn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(resultsTextarea.value);
      copyFeedback.classList.remove('hidden');
      setTimeout(() => {
        copyFeedback.classList.add('hidden');
      }, 2000);
    } catch (err) {
      console.error('Copy failed:', err);
    }
  });
}
