import { BrowserRouter as Router } from 'react-router-dom'
import RoutesConfig from './routes/Routes'
import { ToastContainer } from 'react-toastify'
import { ProductProvider } from './contexts/ProductContext'
import { CartProvider } from './contexts/CartContext'

function App() {
  return (
    <CartProvider>
      <ProductProvider>
        <Router>
          <ToastContainer />
          <RoutesConfig />
        </Router>
      </ProductProvider>
    </CartProvider>
  )
}

export default App
