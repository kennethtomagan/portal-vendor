import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-meta-fields', IndexField)
  app.component('detail-meta-fields', DetailField)
  app.component('form-meta-fields', FormField)
})
