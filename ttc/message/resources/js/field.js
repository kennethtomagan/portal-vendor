import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-message', IndexField)
  app.component('detail-message', DetailField)
  app.component('form-message', FormField)
})
