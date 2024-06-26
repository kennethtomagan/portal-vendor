<template>
  <div>

    <Heading :level="1" v-text="heading.name" v-if="heading.showTitle"/>

    <p>
        <Heading :level="1" v-text="greeting" />
        <input type="text" v-model="greeting">
    </p>

      <form
          v-if="panels"
          @submit="submitViaUpdateResource"
          @change="onUpdateFormStatus"
          :data-form-unique-id="formUniqueId"
          autocomplete="off"
          ref="form"
      >
          <div class="mb-8 space-y-4">
              <component
                  v-for="panel in panels"
                  :key="panel.id"
                  :is="'form-' + panel.component"
                  @update-last-retrieved-at-timestamp="updateLastRetrievedAtTimestamp"
                  @field-changed="onUpdateFormStatus"
                  @file-upload-started="handleFileUploadStarted"
                  @file-upload-finished="handleFileUploadFinished"
                  :panel="panel"
                  :name="panel.name"
                  :resource-id="resourceId"
                  :resource-name="resourceName"
                  :fields="panel.fields"
                  :form-unique-id="formUniqueId"
                  mode="form"
                  :validation-errors="validationErrors"
                  :via-resource="viaResource"
                  :via-resource-id="viaResourceId"
                  :via-relationship="viaRelationship"
                  :show-help-text="true"
              />
          </div>

          <!-- Update Button -->
          <div
              class="flex flex-col md:flex-row md:items-center justify-center md:justify-end space-y-2 md:space-y-0 space-x-3"
          >
              <CancelButton
                  dusk="cancel-update-button"
                  type="button"
                  align="center"
                  @click="cancelUpdatingResource"
              />

              <LoadingButton
                  dusk="update-and-continue-editing-button"
                  @click="submitViaUpdateResourceAndContinueEditing"
                  :disabled="isWorking"
                  align="center"
                  :processing="wasSubmittedViaUpdateResourceAndContinueEditing"
              >
                  {{ __('Update & Continue Editing') }}
              </LoadingButton>

              <LoadingButton
                  dusk="update-button"
                  type="submit"
                  :disabled="isWorking"
                  align="center"
                  :processing="wasSubmittedViaUpdateResource"
              >
                  Update
              </LoadingButton>
          </div>
      </form>

    <TtcCard
        :msg="greeting"
    />

  </div>
</template>

<script type="module">
import { Errors } from 'laravel-nova';
import TtcCard from "../components/TtcCard";
import UpdateView from "@/views/Update";

export default {

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
            formUniqueId:"adfasdfads0f98uaddf"
        }

    },
    mixins: [
        // HandlesValidationErrors,
        // DependentFormField
    ],
    components: {
        TtcCard,
        UpdateView
        // TextField
    //     // Card,
    //     Heading
    },

    async created() {
        await this.getFields();
    },

    methods:{
        async getFields() {
            this.loading = true;
            this.fields = [];
            const params = { editing: true, editMode: 'update' };
            // if (this.pageId) params.path = this.pageId;
            await Nova.request()
                .get('/nova-vendor/global-config/update-fields')
                .then(response => {
                    this.panels = response.data.panels;
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
        async update(submitEvent) {
            // console.log(submitEvent.target);
            // console.log(this.form);
            try {
                this.isUpdating = true;
                const response = await this.updateRequest(submitEvent.target);
                if (response && response.data) {
                    if (response.data.reload === true) {
                        //location.reload();
                        return;
                    } else if (response.data.redirect && response.data.redirect.length > 0) {
                        //location.replace(response.data.redirect);
                        return;
                    }
                }
                Nova.success("Success");

                // Reset the form by refetching the fields
                await this.getFields();
                this.isUpdating = false;
                this.validationErrors = new Errors();
            } catch (error) {
                console.error(error);
                this.isUpdating = false;
                if (error && error.response && error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors);
                    Nova.error(this.__('There was a problem submitting the form.'));
                }
            }
        },
        updateRequest(form) {
            return Nova.request().post('/nova-vendor/tests', this.formData(form));
        },
    },

    mounted() {

    },
}
</script>

