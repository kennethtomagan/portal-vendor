<template>
    <!--        Inertia linking. so we dont have to have a full page refresh -->

    <nav>
        <ul>
            <li><Link href="resources/admins">View Admins</Link></li>
            <li>
                <!-- use preserve-scroll if you want to post data but not scroll back to the top of the page -->
                <Link
                    href="global-config/example-post"
                    :data="{foo:'bar'}"
                    method="post"
                    as="button"
                    preserve-scroll
                >
                    Get fields
                </Link>
            </li>
        </ul>
    </nav>
</template>

<script>
export default {
    name: "ConfigNav"
}
</script>

<style scoped>

</style>
