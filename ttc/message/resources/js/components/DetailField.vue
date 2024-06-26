<template>

    <div  class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="ready">
        <div class="md:w-1/4 md:py-3">
            <h4 class="font-bold md:font-normal"><span>{{field.name}}</span></h4>
        </div>
        <div v-html="field.html" class="md:w-3/4 md:py-3"></div>
    </div>

</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],
}
</script>
