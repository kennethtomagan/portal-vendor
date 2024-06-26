import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-resource-loader', IndexField)
  app.component('detail-resource-loader', DetailField)
  app.component('form-resource-loader', FormField)
})