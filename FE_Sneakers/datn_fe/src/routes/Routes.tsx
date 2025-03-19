import { Routes, Route } from 'react-router-dom'
import Header from '../components/Header' // Import Header từ thư mục components

const RoutesConfig = () => {
  return (
    <>
      <Header /> {/* Header sẽ xuất hiện trên tất cả các trang */}
      <Routes>
        <Route path='/' element={<h1>Trang chủ</h1>} />
        <Route path='/san-pham' element={<h1>Sản phẩm</h1>} />
        <Route path='/gioi-thieu' element={<h1>Giới thiệu</h1>} />
        <Route path='/lien-he' element={<h1>Liên hệ</h1>} />
        <Route path='/cart' element={<h1>Giỏ hàng</h1>} />
      </Routes>
    </>
  )
}

export default RoutesConfig
