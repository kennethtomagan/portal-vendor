<template>
  <div
      class="h-8 w-full leading-normal h-full flex items-center box-content border-b border-gray-200 dark:border-gray-700">
    <button dusk="collapse-group" type="button"
            class="group-control btn border-r border-gray-200 dark:border-gray-700 w-8 h-8 block" title="Collapse">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
           height="16" class="inline-block align-top" role="presentation">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path>
      </svg>
    </button>
    <p class="text-80 grow px-4"><span class="mr-3 font-semibold">#{{index}}</span></p>
    <div class="flex">
      <button dusk="move-up-group" type="button"
              class="group-control btn border-l border-gray-200 dark:border-gray-700 w-8 h-8 block" title="Move up">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
             height="16" class="inline-block align-top" role="presentation">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
      </button>
      <button dusk="move-down-group" type="button"
              class="group-control btn border-l border-gray-200 dark:border-gray-700 w-8 h-8 block" title="Move down">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
             height="16" class="inline-block align-top" role="presentation">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
      </button>
      <button dusk="delete-group" type="button"
              class="group-control btn border-l border-gray-200 dark:border-gray-700 w-8 h-8 block" title="Delete">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16"
             height="16" class="inline-block" role="presentation">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
      </button>
    </div>
  </div>
</template>
<script>
export default {
  name: 'row-header',
  props: {
    index: {}
  }
}
</script>