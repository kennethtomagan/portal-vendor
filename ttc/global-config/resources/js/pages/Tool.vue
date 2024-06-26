<template>
  <div>

    <Heading :level="1" v-text="heading.name" v-if="heading.showTitle"/>

    <ConfigNav />

    <p>
        <Heading :level="1" v-text="greeting" />
        <input type="text" v-model="greeting">
    </p>

      <form ref="mainform" method="post" v-if="panels && panels.length" @submit.prevent="update" autocomplete="off" dusk="nova-settings-form">

          <div class="mb-8 space-y-4">
              <component
                  v-for="panel in panels"
                  :key="panel.id"
                  :panel="panel"
                  :name="panel.name"
                  :is="'form-' + panel.component"
                  :fields="panel.fields"
                  :form-unique-id="formUniqueId"
                  :resource-name="resourceName"
                  :resource-id="pageId"
                  mode="form"
                  class="mb-6"
                  :validation-errors="validationErrors"
                  @file-deleted="$emit('update-last-retrieved-at-timestamp')"
                  @file-upload-started="$emit('file-upload-started')"
                  @file-upload-finished="$emit('file-upload-finished')"
                  :show-help-text="showHelpText"
                  @field-changed="logChangeEvent"

              />
          </div>

          <!--             Update Button -->
          <div class="flex items-center mb-6" v-if="authorizations.authorizedToUpdate">
              <LoadingButton type="submit" class="ml-auto" :disabled="isUpdating" :processing="isUpdating">
                  Save
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
import ConfigNav from "../Shared/ConfigNav";
import UpdateView from "@/views/Update";

// import FormTabs from '../../../../../vendor/eminiarts/nova-tabs/resources/js/components/FormTabs.vue'
// import Tab from "../Shared/ConfigNav";
import tap from "lodash/tap";
import each from "lodash/each";

//app.config.globalProperties.$store = this;

export default {

    //read only data
    props:{
        showHelpText: {
            type: Boolean,
            default: false,
        },
    },

    data() {

        return {
            panels:[],
            greeting:"hello",
            formData: new FormData(),
            heading:{
                name: "Global Config",
                showTitle: true
            },
            pageId: "general",
            resourceName:"ttc/global-config",
            initialLoading: true,
            loading: false,
            processing:false,
            languages: {},
            authorizations: {
                authorizedToUpdate: true
            },
            validationErrors: new Errors(),
            isUpdating: false,
            formUniqueId:"adfasdfads0f98uaddf"
        }

    },
    mixins: [
        // HandlesValidationErrors,
        // DependentFormField
    ],
    components: {
        ConfigNav,
        TtcCard,
    },

    async created() {
        await this.getFields()
        .then(response => {

        });

        Nova.$on("InputEvent", function(evt, val){
            console.log(evt, val);
        })

        Nova.$on("field-changed", function(evt, val){
            console.log(evt, val);
        })

    },

    methods:{
        logChangeEvent(e){
            console.log(e)
        },

        async getFields() {
            this.loading = true;
            this.fields = [];
            const params = { editing: true, editMode: 'update' };
            // if (this.pageId) params.path = this.pageId;
            await Nova.request()
                .get('/nova-vendor/global-config/update-fields')
                .then(response => {
                    this.panels = response.data.panels;
                    return response;
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

        /**
         * Create the form data for creating the resource.
         */
        createResourceFormData() {
            return tap(new FormData(), formData => {
                each(this.panels, panel => {
                    each(panel.fields, component => {
                        // console.log(component);
                        // if(typeof(component.field) !== "undefined"){
                        //     console.log(component.field);
                        // }

                        component.fill(formData)
                    })
                })
            })
        },

        async update() {

            try {
                this.isUpdating = true;
                const response = await this.updateRequest();
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
            // return Inertia.post('/nova-vendor/tests', form);
            return Nova.request().post('/nova-vendor/global-config/save', this.createResourceFormData());
        },

        handleChange(evt){
            console.log("INPUT CHANGE", evt);
        },

    },

    mounted() {

        Nova.$on('change', (payload) => {
            console.log(payload);
        })

        Nova.$on('field-changed', (payload) => {
            console.log(payload);
        })

        console.log(this.panels);

    },

    computed: {

    },
}
</script>

