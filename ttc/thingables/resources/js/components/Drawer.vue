<template>
    <transition name="slide">

        <card
            class="p-6 drawer"
            :class="['thisisdraw',  isOpen ? 'open' : '' ]"
            :style="{ zIndex: zIndex }"
        >
            <slot></slot>
            <button @click="toggle">Toggle</button>
            <button @click="close">Close</button>
        </card>
    </transition>
</template>

<script>
import Card from "../../../../../vendor/laravel/nova/resources/js/components/Card.vue";

export default {
    components: {Card},
    props: {
        zIndex: {
            type: Number,
            required: true
        },
        isOpen: Boolean
    },
    data() {
        return {
            isOpen: false,
        };
    },
    emits: ['remove'],
    methods: {
        toggle() {
            this.isOpen = !this.isOpen;
        },
        close() {
            this.$emit('remove', this.zIndex); // Emit zIndex or some unique identifier
            this.isOpen = false;
        }
    },
    mounted() {
        this.isOpen = true
    },
};
</script>

<style scoped>
.drawer {
    position: absolute;
    top: 0;
    right: -40%;
    width: 33.333%;
    height: 100%;
    background: white;
    transition: left 0.3s;
}

.drawer.open {
    right: 0;
}

.slide-enter-active, .slide-leave-active {
    transition: left 0.3s;
}

.slide-enter, .slide-leave-to /* .slide-leave-active in <2.1.8 */ {
    left: -300px;
}
</style>
