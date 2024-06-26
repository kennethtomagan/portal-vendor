<template>
    <div>
        <IndexPage
            v-if="!tenantId && !id"
        />
        <ShowPage
            v-else
            :ticketId="id"
            :tenantId="tenantId"
            :messageFields="messageFields"
            :auth="auth"
        />
    </div>
</template>

<script>
import IndexPage from "./Index";
import ShowPage from "./Show";


export default {
    components:{
        IndexPage,
        ShowPage
    },
    props: {
        id: {
            type: Number,
        },
        tenantId: {
            type: String,
        },
        messageFields: {
            type: Array,
        },
        auth: {
            type: Object,
        }

    },
    data() {
        return {}
    },
    mounted() {
    }
}
</script>

<style>
/* Scoped Styles */
</style>
