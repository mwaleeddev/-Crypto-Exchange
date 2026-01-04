mkdir -p resources/js
cat > resources/js/App.vue << 'EOF'
<template>
    <div id="app">
        <router-view />
    </div>
</template>

<script setup>
// Main app component
</script>

<style>
/* Global styles if needed */
</style>
EOF