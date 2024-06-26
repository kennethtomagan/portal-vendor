<template>
          <FormPanel
              :panel="formpanel"
              :fields="formpanel.fields"
              formUniqueId="ed"
              :validationErrors="validationErrors"
          />
          <FormTextField
              :field=field
              fieldName ="This is the field name"
              showErrors true
              fullWidthContent:true
              :errors="validationErrors"
              :show-help-text="true"
          />
</template>
<script>
export default {
    name: 'TtcCard',
    props:{
      msg: {
          type: String,
          default:"hello"
      }
    }
}
</script>
