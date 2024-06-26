import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('index-product-order', IndexField)
  app.component('detail-product-order', DetailField)
  app.component('form-product-order', FormField)
})
