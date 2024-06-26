<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
    <template #field>

        <div
            v-for="(row, index) in this.existingRows"
            class="flex flex-row flex-wrap border border-gray-200 mb-5"
            :class="'meta-row-'+index"
        >

            <row-header :index="row.key"/>

            <component
                v-for="(subfield, fieldindex) in row.fields"
                :key="subfield.uniqueKey"
                :is="'form-' + subfield.component"
                :id="subfield.validationKey"
                :resource-name="resourceName"
                :resource-id="resourceId"
                :field="subfield"
                showHelpText="subfield.showHelpText"
                class="md:w-1/4"
                :errors="this.errors"
                @change="handleChange($event, subfield, index, row.key)"
            >
            </component>

        </div>

        <input
            type="button"
            value="Add Row"
            @click="toggleAddRow"
            class="shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900"
            :aria-label="__('Add Row')"
        />

    </template>
  </DefaultField>
</template>

<script>
import {
    FormField,
    HandlesValidationErrors,
} from 'laravel-nova'

import RowHeader from "./RowHeader";

export default {
    components: {RowHeader},
    mixins: [FormField, HandlesValidationErrors],
    // props: ['resourceName', 'resourceId', 'field'],
    props: {
        // errors: {
        //     default: () => new Errors(),
        // },
        field:{},
        resourceName:{},
        resourceId:{},
        // validationErrors: {
        //     type: Object,
        //     required: true,
        // },
    },
    data() {

        return {
            authorizations: [],
            // validationErrors: new Errors(),
            isUpdating: false,
            existingRows: {},
            rowCount: 0,
            value: new FormData(),
            // errors: {
            //     default: () => new Errors(),
            // },
        }

    },
    created(){
        //console.log("created");
    },
    computed: {

    },

    methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.existingRows = this.field.existingRows;
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {

        for (const pair of this.value.entries()) {
            formData.append(pair[0], pair[1]);
        }

    },

    async getFields() {
        // const params = { editing: true, editMode: 'create' };
        await Nova.request()
            .get(
                '/nova-api/'+this.field.resource+'/'+this.field.resourceId+'/'+this.field.attribute+'/new-fields',
                {
                    params: {
                        editing: true,
                        editMode: this.editMode,
                        viaResource: this.viaResource,
                        viaResourceId: this.viaResourceId,
                        viaRelationship: this.viaRelationship,
                    }
                }
            )
            .then(response => {
                //maybe set this to an array?
                this.existingRows.push(response.data.row)

            })

            .catch(error => {
                if (error.response.status == 404) {
                    return;
                }
            });

        this.loading = false;
        this.initialLoading = false;
    },

    toggleAddRow(value){
        this.getFields(value);
    },

    testFill(value){
        this.fill(new FormData);
    },

    handleChange(evt, field, index, key){

        const fieldType = evt.target.type
        let fieldValue;
        if(fieldType === "checkbox"){
            fieldValue = evt.target.checked;
        }else{
            fieldValue = evt.target.value;
        }

        this.value.set(field.appendName, fieldValue)
    },

    subFieldHasError(subField) {

        if( typeof(this.errors) !== "undefined"){
            return false;
        }

        if( typeof(this.errors["errors"]) !== "undefined"){
            return false;
        }
        console.log(this.errors["errors"][subField]);

        return typeof(this.errors["errors"][subField]) !== "undefined";
    },
  },

    mounted() {

    },
}
</script>
