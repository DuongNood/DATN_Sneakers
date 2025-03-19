import { BrowserRouter as Router } from 'react-router-dom'
import RoutesConfig from './routes/Routes'
import { ToastContainer } from 'react-toastify'

function App() {
  return (
    <Router>
      <ToastContainer />
      <RoutesConfig />
    </Router>
  )
}

export default App
