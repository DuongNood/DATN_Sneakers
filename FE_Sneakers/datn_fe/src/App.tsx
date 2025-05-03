import { BrowserRouter as Router } from 'react-router-dom'
import RoutesConfig from './routes/Routes'
import { ToastContainer } from 'react-toastify'
import { ProductProvider } from './contexts/ProductContext'
import { CartProvider } from './contexts/CartContext'
import 'react-toastify/dist/ReactToastify.css'

function App() {
  console.log('App component rendered')
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
