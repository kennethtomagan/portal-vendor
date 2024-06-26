<template>
  <div>
    <div
        class="messageInfo mb-1" :id="[thing.type+'_'+thing.id]"
    >
        <strong>{{ thing.owner_name }}</strong>
        <span class="ml-2" v-if="thing.human_created_at">{{ thing.human_created_at }}</span>
        <span class="ml-2" v-else>{{ thing.created_at }}</span>
    </div>
    <div class="bg-gray-200 text-black p-2 rounded message" v-html="thing.content"></div>
      <div v-if="thing.stream_count" role="button" @click="addDrawer(thing.id)" class="bg-gray-200 text-black p-2 ml-2" tabindex="0">
            <span class="flex flex-row">
                <span class=""><svg width="24px" height="24px" viewBox="0 0 24 24" class=""><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H4V4h16v12zM6 12h8v2H6zm0-3h12v2H6zm0-3h12v2H6z"></path><path fill="none" d="M0 0h24v24H0V0z"></path></svg></span>
                {{thing.stream_count}} reply
            </span>

      </div>
  </div>


</template>
<script>
export default {
  name: 'thing',
    components: {},
    props: {
        thing: {
            type: Array,
            default: [],
        },
        addDrawer: Function,
        showSteamContent: String
    },
    data() {

    },
    mounted() {
        // console.log(this.thing)
    },
}
</script>
