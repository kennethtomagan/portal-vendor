<template>

        <div class="flex flex-col md:flex-row remove-bottom-border">

            <div class="flex-auto" v-for="(item, index) in group.fields">
                <component
                    :key="item.id"
                    :is="'form-' + item.component"
                    :resource-name="resourceName"
                    :resource-id="resourceId"
                    :field="item"
                    :validation-errors="null"
                    @change="handleChange($event)"
                />
            </div>

            <div class="flex-auto">
                <div class="flex flex-col">
                    <div class="px-6 md:px-8 mt-2 md:mt-0 md:pt-2 w-full">
                        <label class="inline-block pt-2 leading-tight">Price</label>
                    </div>
                    <div class="mt-1 md:mt-0 pb-5 px-6 md:px-8 md:w-3/5 w-full md:pt-2 ">
                        <p style="line-height: 36px;">{{ formattedPrice }}</p>
                    </div>
                </div>
            </div>

        </div>

</template>

<script>

import { FormField, HandlesValidationErrors } from 'laravel-nova'
import slugify from '../../../../../vendor/laravel/nova/resources/js/util/slugify'

export default {
    mixins: [FormField, HandlesValidationErrors, slugify],
    props: ['attribute', 'group', 'index', 'last', 'resource', 'resourceName', 'resourceId'],

    data() {

        return {
            initialLoading: true,
            loading: false,
            languages: {},
            authorizations: [],
            selectOptions: {},
            loadedResource: {},
            group: {
                fields : [
                    this.loadNovaField("Product", {
                        attribute : "product",
                        component : "select-field",
                        type : "select",
                        options:this.field.selectOptions,
                    }),
                    this.loadNovaField("Qty", {
                        attribute : "qty",
                        component : "text-field",
                        indexName : "Quantity",
                        name : "Quantity",
                        max:2,
                        min:1,
                        type : "number",
                        value: 1,
                    }),
                    // this.loadNovaField("Price", {
                    //     attribute : "price",
                    //     component : "text-field",
                    //     type : "input",
                    // })
                ]
            },
            productPrice: 0.00,
            productQty: 1,
            formattedPrice: 0.00
        }

    },

    async created() {
        // await this.getFields();
    },

    methods:{
        /*
       * Set the initial, internal value for the field.
       */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        loadNovaField(name, args){
            let id = slugify(name);
            const defaults = {
                attribute : id,
                component : "text-field",
                dependentComponentKey : "",
                dependsOn : {},
                displayedAs : null,
                fill : function (){},
                helpText : null,
                indexName : name,
                name : name,
                nullable : false,
                panel : "",
                prefixComponent : true,
                readonly : false,
                required : true,
                sortable : false,
                sortableUriKey :  "",
                stacked : true,
                suggestions : null,
                textAlign : "right",
                // uniqueKey : "qty-update-product--text-field",
                usesCustomizedDisplay :  false,
                validationKey : id,
                value : null,
                visible : true,
                wrapping : false,
            }
            return Object.assign( {}, defaults, args );
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        async getProduct(resourceID) {
            this.loading = true;
            // this.group.fields = [];
            const params = { editing: true, editMode: 'update' };
            console.log(this.field.loadResource)
            await Nova.request()
                .get(
                    '/nova-api/'+this.field.loadResource+'/'+resourceID,
                    params
                )
                .then(response => {
                    console.log(response.data);
                    this.loadedResource = response.data;
                    let buyField = response.data.resource.fields.find(x => x.attribute === 'buy');
                    if(buyField){
                        this.productPrice = buyField.value;

                    }else{
                        this.productPrice = 0;
                    }

                    this.priceUpdate();

                    // this.group.fields = response.data.fields;
                    // this.productPrice = response.data.fields;
                    // this.fields = response.data.fields;
                    // this.authorizations = response.data.authorizations;
                })

                .catch(error => {
                    if (error.response.status == 404) {
                        // this.$router.push({ name: '404' });
                        return;
                    }
                });

            this.loading = false;
            this.initialLoading = false;
        },
        handleChange(evt){
            console.log(evt);
            if(evt.target.tagName === "SELECT"){
                this.getProduct(evt.target.value);
            }

            if(evt.target.type === "number"){
                this.productQty = evt.target.value;
            }

            this.priceUpdate();

        },
        priceUpdate(){
            console.log(this.productQty)
            console.log(this.productPrice)
            this.formattedPrice = Intl.NumberFormat('en-AU', {style:"currency", currency:"AUD"}).format(parseFloat(this.productQty) * parseFloat(this.productPrice))
        }
    },

    mounted() {
        console.log("mounted");
        //nova-api/{resource}/creation-fields
        console.log(this.field.selectOptions);
        console.log(this.field);
    },

}
</script>
