<template>
    <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
        <template #field>


                <component
                    :key="field.uniqueKey"
                    :is="'form-' + field.component"
                    :id="field.validationKey"
                    :field="field"
                    showHelpText="subfield.showHelpText"
                    class="md:w-1/4"
                    :errors="this.errors"
                    @change="handleChange($event, field)"
                >
                </component>


        </template>
    </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  methods: {
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
      formData.append(this.fieldAttribute, this.value || '')
    },
  },
    mounted() {
        console.info(this.field)
    },
}
</script>
