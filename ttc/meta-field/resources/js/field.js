import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-meta-field', IndexField)
  app.component('detail-meta-field', DetailField)
  app.component('form-meta-field', FormField)
})
