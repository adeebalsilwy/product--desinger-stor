<template>
  <div>
    <h1>Test Designer Component</h1>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div v-else>
      <ProductDesigner 
        :product-type-id="1"
        @saved="onSaved"
        @changed="onChanged"
      />
    </div>
  </div>
</template>

<script>
import ProductDesigner from '@/Components/Designer/ProductDesigner.vue';

export default {
  name: 'TestDesigner',
  
  components: {
    ProductDesigner
  },
  
  data() {
    return {
      error: null
    };
  },
  
  mounted() {
    console.log('TestDesigner mounted');
    try {
      // Test if component is properly imported
      if (typeof ProductDesigner === 'undefined') {
        this.error = 'ProductDesigner component is not properly imported';
      } else {
        console.log('ProductDesigner component imported successfully');
      }
    } catch (err) {
      this.error = 'Error importing ProductDesigner: ' + err.message;
      console.error('Import error:', err);
    }
  },
  
  methods: {
    onSaved(design) {
      console.log('Design saved:', design);
    },
    
    onChanged() {
      console.log('Design changed');
    }
  }
};
</script>

<style scoped>
.error {
  color: red;
  padding: 1rem;
  border: 1px solid red;
  border-radius: 4px;
  background: #ffe6e6;
}
</style>