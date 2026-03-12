import { ref } from 'vue';

// Global settings store
const settings = ref({});

export const brandSettings = {
  install(app) {
    // Fetch settings on plugin install
    this.fetchSettings();
    
    // Provide settings to all components
    app.config.globalProperties.$brandSettings = settings;
    app.provide('brandSettings', settings);
  },
  
  async fetchSettings() {
    try {
      const response = await fetch('/api/settings');
      if (response.ok) {
        const data = await response.json();
        settings.value = data.settings;
        
        // Apply brand colors to CSS custom properties for consistent theming
        this.applyBrandColors(data.settings);
      }
    } catch (error) {
      console.error('Error fetching brand settings:', error);
      // Set default values
      settings.value = {
        site_name: "Ahlam's Girls",
        brand_primary_color: '#6B1138',  // Deep burgundy
        brand_secondary_color: '#1a1a2e', // Navy
        brand_accent_color: '#FFD700',   // Gold
        brand_bg_color: '#0f3460',       // Midnight blue
        brand_text_color: '#ffffff',     // White
        brand_gold: '#FFD700',           // Gold
        brand_rose: '#FF6B6B',           // Rose
        brand_lavender: '#E6E6FA',       // Lavender
        site_description: "Elegance, Sewn to Perfection",
        site_email: 'contact@ahlamsgirls.com',
        site_phone: '+966 50 123 4567',
        site_address: 'Riyadh, Saudi Arabia',
        facebook_url: 'https://facebook.com/ahlamsgirls',
        twitter_url: 'https://twitter.com/ahlamsgirls',
        instagram_url: 'https://instagram.com/ahlamsgirls',
        brand_tagline: 'Elegance, Sewn to Perfection'
      };
      
      this.applyBrandColors(settings.value);
    }
  },
  
  applyBrandColors(brandSettings) {
    // Apply brand colors as CSS custom properties
    const root = document.documentElement;
    
    // Map the database settings to CSS variables
    root.style.setProperty('--brand-primary', brandSettings.brand_primary_color || '#6B1138');
    root.style.setProperty('--brand-secondary', brandSettings.brand_secondary_color || '#1a1a2e');
    root.style.setProperty('--brand-accent', brandSettings.brand_accent_color || '#FFD700');
    root.style.setProperty('--brand-bg', brandSettings.brand_bg_color || '#0f3460');
    root.style.setProperty('--brand-text', brandSettings.brand_text_color || '#ffffff');
    root.style.setProperty('--brand-gold', brandSettings.brand_gold || '#FFD700');
    root.style.setProperty('--brand-rose', brandSettings.brand_rose || '#FF6B6B');
    root.style.setProperty('--brand-lavender', brandSettings.brand_lavender || '#E6E6FA');
    
    // Also update the root CSS variables that might be defined in the CSS
    root.style.setProperty('--neumorphic-bg', brandSettings.brand_bg_color ? this.adjustBrightness(brandSettings.brand_bg_color, 20) : '#f0f4f8');
  },
  
  // Helper function to adjust brightness of a hex color
  adjustBrightness(hex, percent) {
    // Convert hex to RGB
    let r = parseInt(hex.substring(1, 3), 16);
    let g = parseInt(hex.substring(3, 5), 16);
    let b = parseInt(hex.substring(5, 7), 16);

    // Adjust brightness
    r = Math.min(255, Math.max(0, r + r * (percent / 100)));
    g = Math.min(255, Math.max(0, g + g * (percent / 100)));
    b = Math.min(255, Math.max(0, b + b * (percent / 100)));

    // Convert back to hex
    return '#' + Math.round(r).toString(16).padStart(2, '0') + 
           Math.round(g).toString(16).padStart(2, '0') + 
           Math.round(b).toString(16).padStart(2, '0');
  },
  
  getSettings() {
    return settings.value;
  }
};