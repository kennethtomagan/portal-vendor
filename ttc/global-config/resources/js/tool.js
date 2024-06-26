import Tool from './pages/Tool'

Nova.booting((app, store) => {
  Nova.inertia('GlobalConfig', Tool)
  // Nova.inertia('GlobalConfigEmail', Tool)
})
