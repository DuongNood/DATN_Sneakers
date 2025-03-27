import { BrowserRouter as Router } from 'react-router-dom'
import RoutesConfig from './routes/Routes'
import { ToastContainer } from 'react-toastify'
import { ProductProvider } from './contexts/ProductContext'

function App() {
  return (
    <ProductProvider>
      <Router>
        <ToastContainer />
        <RoutesConfig />
      </Router>
    </ProductProvider>
  )
}

export default App
