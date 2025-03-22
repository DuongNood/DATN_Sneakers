import { Routes, Route } from 'react-router-dom'
import Header from '../components/Header'
import Footer from '../components/Footer'
import Register from '../pages/Register'
import Login from '../pages/Login'
import Cart from '../pages/Cart'
import ContactPage from '../components/contact'
import Banner from '../components/Banner'
import Legit from '../components/Letgit'
import TitleWithEffect from '../components/TitleProduct'
import ProductList from '../pages/ProductList'
import TitleWithEffect1 from '../components/TitleProduct1'
import ProductHot from '../pages/ProductHot'
import ProductSale from '../pages/ProductSale'
import NotFound from '../components/NotFound'
import ForgotPassword from '../pages/ForgotPassword'
import ProfilePage from '../components/Profile'
import ChangePasswordPage from '../pages/ChangePasswordPage'
import ProtectedRoute from '../components/ProtectedRoute'
import Navbar from '../components/TopHeader'
import ProductDetail from '../pages/DetailsProduct'

const RoutesConfig = () => {
  return (
    <>
      <Navbar />
      <Header />
      <Routes>
        <Route
          path='/'
          element={
            <>
              <Banner />
              <Legit />
              <TitleWithEffect />
              <ProductList />
              <TitleWithEffect1 />
              <ProductHot />
              {/* <ProductDetail /> */}
            </>
          }
        />
        <Route path='/products/:id' element={<ProductList />} />
        <Route path='/product-sale' element={<ProductSale />} />
        <Route path='/contact' element={<ContactPage />} />
        <Route path='/forgot-password' element={<ForgotPassword />} />
        <Route path='/login' element={<Login />} />
        <Route path='/register' element={<Register />} />
        <Route path='/detail-product/:id' element={<ProductDetail />} />
        <Route path='/cart' element={<Cart />} />
        <Route path='*' element={<NotFound />} />
        {/* Bảo vệ router */}
        <Route element={<ProtectedRoute />}>
          <Route path='/profile' element={<ProfilePage />} />
          <Route path='/change-password' element={<ChangePasswordPage />} />
        </Route>
      </Routes>
      <Footer />
    </>
  )
}

export default RoutesConfig
