import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-config-field', IndexField)
  app.component('detail-config-field', DetailField)
  app.component('form-config-field', FormField)
})
