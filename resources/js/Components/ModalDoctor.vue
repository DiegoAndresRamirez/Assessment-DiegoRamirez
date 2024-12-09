<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded shadow-lg w-96 relative">
        <button @click="$emit('close')" class="absolute top-2 right-2 text-gray-600">✖</button>
        <slot></slot>
      </div>
    </div>
  </template>
  
  <script>
  export default {};
  </script>
  
  <style scoped>
  </style>
  