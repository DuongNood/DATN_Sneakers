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

const RoutesConfig = () => {
  return (
    <>
      <Header />
      <Banner />
      <Legit />
      <TitleWithEffect />
      <ProductList />
      <TitleWithEffect1 />
      <ProductHot />

      <Routes>
        <Route path='/' />
        <Route path='/product-detail' element={<h1>Sản phẩm</h1>} />
        <Route path='/gioi-thieu' element={<h1>Giới thiệu</h1>} />
        <Route path='/product-sale' element={<ProductSale />} />
        <Route path='/contact' element={<ContactPage />} />
        <Route path='/login' element={<Login />} />
        <Route path='/register' element={<Register />} />
        <Route path='/cart' element={<Cart />} />
        <Route path='*' element={<NotFound />} />
      </Routes>
      <Footer />
    </>
  )
}

export default RoutesConfig
