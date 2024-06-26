import UserPermission from './components/UserPermission'

Nova.booting((app, store) => {
  app.component('UserPermissions', UserPermission)
})
