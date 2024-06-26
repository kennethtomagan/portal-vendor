import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-laraberg', IndexField)
  app.component('detail-laraberg', DetailField)
  app.component('form-laraberg', FormField)
})
