<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
    <template #field>

        <loading-card :loading="loading" class="card relative overflow-hidden w-1/3">
            <!--Make form-->
        </loading-card>

        <SelectControl
            class="w-full block"
            size="sm"
            @change="productChange($event)"
            :options=field.selectOptions
            label="label"
        >
            <option value="" :selected="field.value == ''">Select {{ field.name }}</option>
        </SelectControl>

        <span v-for="(item, index) in group.fields">
            form-{{item.component}}
        </span>


        <component
            v-for="(item, index) in group.fields"
            :key="index"
            :is="'form-' + item.component"
            :resource-name="resourceName"
            :resource-id="resourceId"
            :field="item"
            :validation-errors="null"
        />

    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import TtcCard from "../../../../GlobalConfig/resources/js/components/TtcCard";

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['attribute', 'group', 'index', 'last', 'resource', 'resourceName', 'resourceId'],

    data() {

        return {
            panels:[],
            greeting:"hello",
            heading:{
                name: "Global Config",
                showTitle: true
            },
            initialLoading: true,
            loading: false,
            languages: {},
            authorizations: [],
            // validationErrors: new Errors(),
            isUpdating: false,
            selectOptions: {},
            loadResource: {},
            group: {
                fields:[]
            }
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

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        async getFields(resourceID) {
            this.loading = true;
            this.group.fields = [];
            const params = { editing: true, editMode: 'update' };
            console.log(this.field.loadResource)
            await Nova.request()
                .get(
                    '/nova-api/'+this.field.loadResource+'/'+resourceID+'/order-fields',
                    params
                )
                .then(response => {
                    console.log(response.data);
                    this.group.fields = response.data.fields;
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
        productChange(value){
            console.log(value);
            this.getFields(value);
        }
    },

    mounted() {
        // console.log("mounted");
        //nova-api/{resource}/creation-fields
        // console.log(this.field.selectOptions);
        // console.log(this.field);
    },

}
</script>
