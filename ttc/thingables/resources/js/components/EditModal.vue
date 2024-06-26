<template>
    <Modal
        :show="show"
        @close-via-escape="handlePreventModalAbandonmentOnClose"
        data-testid="confirm-action-modal"
        tabindex="-1"
        role="dialog"
        :size="'2xl'"
    >
    <form
      ref="theForm"
      autocomplete="off"
      @submit.prevent.stop="submitForm"
      class="bg-white  rounded-lg shadow-lg overflow-hidden space-y-6 mt-8">
      <div class="space-y-6">
        <ModalHeader v-text="'Edit Message'"/>
        <component
            v-for="field in fieldsData"
            :key="field.uniqueKey +'-'+ message.id"
            :is="'form-' + field.component"
            :id="field.uniqueKey +'-'+ message.id"
            :fieldAttribute="field.attribute"
            :resource-name="resourceName"
            :resource-id="resourceId  +'-'+ message.id"
            :field="field"
            :showHelpText="'field.showHelpText'"
            :errors="validationErrors"
            :value.sync="status"
        >
        </component>
        
    </div>
    <ModalFooter>
        <div class="flex items-center ml-auto">
          <CancelButton
            component="button"
            type="button"
            dusk="cancel-action-button"
            class="ml-auto mr-3"
            @click="$emit('close')"
          >
            Cancel
          </CancelButton>

          <LoadingButton
            type="submit"
            ref="runButton"
            dusk="confirm-action-button"
            :disabled="working"
            :loading="working"
          >
            Update
          </LoadingButton>
        </div>
      </ModalFooter>
    </form>
  </Modal>
</template>

<script>
import { Errors } from 'form-backend-validation'
export default {
    
    props: {
        show: { type: Boolean, default: false },
        message: { 
            type: Array,
            required: true
        },
        fields: { 
            type: Array,
            required: true
        },
        resourceName: {
            type: String,
            required: true
        },
        resourceId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            formData:  new URLSearchParams(),
            validationErrors: new Errors(),
            working: false,
        }
    },
    computed: {
        fieldsData() {
            return this.fields.map(element => {
            if (element.attribute === 'status') {
                return { ...element, value: this.message.status };
            } else if (element.attribute === 'content') {
                return { ...element, value: this.message.content };
            }
            return element;
        });
        }
    },
    methods: {
        submitForm(){
            this.fill();
            this.working = true;
            Nova.request().put('/nova-api/ticket/messages/' + this.message.id + '/update-message', this.formData)
            .then(response => {
                Nova.success('Message updated successfully');
                this.working = false;
                this.validationErrors = new Errors()
                this.$emit('updated', response.data.data)
            })
            .catch(error => {
                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                    Nova.error('Error:'+ error.response.data.message)
                }
                this.working = false;
            });
        },
        handlePreventModalAbandonmentOnClose(event) {
            this.handlePreventModalAbandonment( () => { this.$emit('close') },
                () => {
                event.stopPropagation()
                }
            )
        },
        setFieldValue: function(evt, field){
            const fieldElement = document.querySelector("#"+field.uniqueKey +'-'+ this.message.id);
            let valueToAdd = "";
            if(typeof field.checked !== "undefined"){
                valueToAdd = field.checked;
            }else{
                const fieldTrix = fieldElement.querySelector("trix-editor");
                if(fieldTrix){
                    valueToAdd = fieldTrix.value;
                } else {
                    const parentElement = document.getElementById(field.uniqueKey +'-'+ this.message.id);
                    const fieldElement = parentElement.querySelector("#"+field["attribute"]);
                    valueToAdd = fieldElement.value;
                }
            }
            this.formData.append(field.attribute, valueToAdd);
        },
        fill() {
            for (let i = 0; i < this.fieldsData.length; i++) {
                const field = this.fieldsData[i];
                this.setFieldValue(null, field)
            }
        },
    }
}
</script>

