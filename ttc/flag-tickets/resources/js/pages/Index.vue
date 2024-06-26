<template>
    <div>
        <Head title="Flag Tickets" />
        <Heading class="mb-6">Flag Tickets</Heading>
        <div class="flex mb-6">
            <index-search-input
                @update:keyword="searchTickets"
            />
        </div>
        <Card class="p-3">
            <TicketsTable
                :tickets="tickets"
                :loading="loading"
                :pagination="pagination"
                @nextPage="nextPage"
                @previousPage="previousPage"
                @reload="getTickets"
            />
        </Card>
    </div>
</template>

<script>
import TicketsTable from "../components/TicketsTable";
import Search from "../components/Search";
import debounce from 'lodash.debounce'; 


export default {
    components:{
        TicketsTable,
        Search
    },
    props: {
        id: {
            type: Number,
        },
        tenantId: {
            type: String,
        },
    },
    data() {
        return {
            tickets: [],
            loading: false,
            search: null,
            pagination: { 
                current_page: 1, 
                per_page: 10
            }
        }
    },
    mounted() {
        this.getTickets();
    },
    methods: {
        getTickets() {
            this.loading = true;
            Nova.request().get('/api/flag-tickets',  {
                params: {
                    page: this.pagination.current_page,
                    per_page: this.pagination.per_page,
                    order_by: 'desc',
                    search: this.search
                }
            })
            .then(response => {
                this.tickets = response.data.data;
                this.pagination =  response.data.meta;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
            });
        },
        nextPage() {
            this.pagination.current_page++;
            this.getTickets();
        },
        previousPage() {
            this.pagination.current_page--;
            this.getTickets();
        },

        searchTickets: debounce(function (value) {
            this.search = value
            this.getTickets();
        }, 500),
    }
}
</script>

