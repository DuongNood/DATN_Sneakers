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

const RoutesConfig = () => {
  return (
    <>
      <Header />
      <Banner />
      <Legit />
      <TitleWithEffect />
      <Routes>
        <Route path='/' />
        <Route path='/product' element={<h1>Sản phẩm</h1>} />
        <Route path='/gioi-thieu' element={<h1>Giới thiệu</h1>} />
        <Route path='/contact' element={<ContactPage />} />
        <Route path='/login' element={<Login />} />
        <Route path='/register' element={<Register />} />
        <Route path='/cart' element={<Cart />} />
      </Routes>
      <Footer />
    </>
  )
}

export default RoutesConfig
