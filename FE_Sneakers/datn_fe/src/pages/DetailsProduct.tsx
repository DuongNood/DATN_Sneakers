import React, { useState } from 'react'
import 'bootstrap/dist/css/bootstrap.min.css'
import '../assets/style/css/productDetail.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import '../assets/style/css/news.css'
import 'bootstrap-icons/font/bootstrap-icons.css'

const relatedProducts = [
  {
    id: 1,
    name: 'Diamond Halo Stud Ligula',
    price: '$732.00',
    imageUrl: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    discount: 10
  },
  {
    id: 2,
    name: 'Diamond Halo Stud Ligula',
    price: '$732.00',
    imageUrl: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    discount: 15
  },
  {
    id: 3,
    name: 'Diamond Halo Stud Ligula',
    price: '$732.00',
    imageUrl: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    discount: 5
  }
]

const categories = ['Tin khuyến mãi', 'Tin khuyến mãi', 'Tin khuyến mãi', 'Tin khuyến mãi', 'Tin khuyến mãi']

const recentPosts = [
  {
    img: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    title: 'Lorem ipsum dolor sit amet consectetur adipiscing',
    date: '25/02/2020'
  },
  {
    img: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    title: 'Lorem ipsum dolor sit amet consectetur adipiscing',
    date: '25/02/2020'
  },
  {
    img: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400',
    title: 'Lorem ipsum dolor sit amet consectetur adipiscing',
    date: '25/02/2020'
  }
]
const keywords = ['Thể thao', 'Xu hướng', 'Trang sức', 'Man', 'Giày', 'Giày thể thao', 'Sport']

const DetailsProduct = () => {
  const [selectedImage, setSelectedImage] = useState(
    'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400'
  )
  const [selectedColor, setSelectedColor] = useState('red')

  const colorImages = [
    {
      color: 'red',
      imageUrl: 'https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400'
    },
    {
      color: 'blue',
      imageUrl: 'https://product.hstatic.net/1000150581/product/1124a5577-4__9__d16ebe37003d42c6ab0ebdb4aa299630.jpg'
    },
    {
      color: 'green',
      imageUrl: 'https://product.hstatic.net/1000150581/product/1124a5577-4__9__d16ebe37003d42c6ab0ebdb4aa299630.jpg'
    },
    {
      color: 'yellow',
      imageUrl: 'https://images.pexels.com/photos/2529157/pexels-photo-2529157.jpeg?auto=compress&cs=tinysrgb&w=400'
    }
  ]

  const handleColorChange = (color: string) => {
    setSelectedColor(color)
    const selectedColorImage = colorImages.find((c) => c.color === color)
    if (selectedColorImage) {
      setSelectedImage(selectedColorImage.imageUrl)
    }
  }

  const [quantity, setQuantity] = useState(1)
  const [selectedSize, setSelectedSize] = useState('')
  const sizes = ['36', '37', '38', '39', '40', '41', '42']

  const handleSizeChange = (size: string | number) => {
    setSelectedSize(size.toString())
  }

  const [activeTab, setActiveTab] = useState('mieuTa')

  const handleTabChange = (tab: string) => {
    setActiveTab(tab)
  }

  return (
    <div>
      <div className='container mt-5'>
        <div className='row'>
          <div className='col-md-9'>
            <div className='row'>
              <div className='col-md-6'>
                <img
                  src={selectedImage}
                  alt='Product'
                  className='img-fluid'
                  style={{ border: '2px solid #ddd', maxWidth: '100%', height: 'auto' }}
                />

                {/* Chọn màu sản phẩm với hiệu ứng zoom */}
                <div className='d-flex mt-3'>
                  {colorImages.map((colorObj, index) => (
                    <button
                      key={index}
                      className={`btn border border-dark btn-sm me-2 p-0 color-thumbnail ${
                        selectedColor === colorObj.color ? 'border-3 border-primary' : ''
                      }`}
                      onClick={() => handleColorChange(colorObj.color)}
                      style={{ width: '70px', height: '70px', padding: 0 }}
                    >
                      <img
                        src={colorObj.imageUrl}
                        alt={colorObj.color}
                        className='img-fluid'
                        style={{ width: '100%', height: '100%' }}
                      />
                    </button>
                  ))}
                </div>
              </div>

              <div className='col-md-6'>
                <h3>Diamond Halo Stud Aenean</h3>
                <h5 className='text-warning'>$552.00</h5>
                <p className='text-muted'>Mã Sản Phẩm: E-00037</p>
                <p>Tình trạng: Còn hàng</p>
                <p>
                  <strong>Mô tả:</strong> Giày thể thao nam đẹp, thoải mái, phù hợp cho nhiều dịp. Cấu trúc hỗ trợ tốt
                  cho việc vận động và tập luyện.
                </p>

                <div className='mt-3'>
                  <label className='mb-2'>
                    <strong>Chọn Kích Cỡ:</strong>
                  </label>
                  <div className='d-flex flex-wrap'>
                    {sizes.map((size) => (
                      <button
                        key={size}
                        className='border border-dark rounded btn btn-outline-secondary btn-sm me-2 mb-2'
                        onClick={() => handleSizeChange(size)}
                        style={{ width: '60px', height: '40px' }}
                      >
                        {size}
                      </button>
                    ))}
                  </div>
                </div>

                <div className='mt-4 d-flex align-items-center'>
                  <div className='d-flex align-items-center border border-dark rounded-3 p-1 ms-1'>
                    <button
                      className='btn btn-outline-secondary btn-sm rounded-circle'
                      onClick={() => setQuantity(quantity > 1 ? quantity - 1 : 1)}
                      style={{ width: '30px', height: '35px' }}
                    >
                      -
                    </button>
                    <span className='mx-3' style={{ fontSize: '1.2rem', fontWeight: 'bold' }}>
                      {quantity}
                    </span>
                    <button
                      className='btn btn-outline-secondary btn-sm rounded-circle'
                      onClick={() => setQuantity(quantity + 1)}
                      style={{ width: '35px', height: '35px' }}
                    >
                      +
                    </button>
                  </div>
                  <button className='btn btn-warning ms-2'>Thêm vào giỏ</button>
                  <button className='btn btn-primary ms-2'>Mua ngay</button>
                </div>
              </div>
            </div>

            <div>
              <div className='d-flex mt-3'>
                <button
                  className={`btn ${activeTab === 'mieuTa' ? 'btn-success' : 'btn-outline-secondary'} me-3 tab-button`}
                  onClick={() => handleTabChange('mieuTa')}
                >
                  Mô Tả
                </button>
                <button
                  className={`btn ${activeTab === 'danhGia' ? 'btn-success' : 'btn-outline-secondary'} tab-button`}
                  onClick={() => handleTabChange('danhGia')}
                >
                  Đánh Giá
                </button>
              </div>

              <div className='tab-content mt-3'>
                {activeTab === 'mieuTa' && (
                  <div className='tab-pane active'>
                    <h4>Mô Tả Sản Phẩm</h4>
                    <p>
                      Giày thể thao nam đẹp, thoải mái, phù hợp cho nhiều dịp. Cấu trúc hỗ trợ tốt cho việc vận động và
                      tập luyện.
                    </p>
                  </div>
                )}
                {activeTab === 'danhGia' && (
                  <div className='tab-pane active'>
                    <h4>Đánh Giá Sản Phẩm</h4>
                    <p>Đây là phần đánh giá của khách hàng về sản phẩm này.</p>
                  </div>
                )}
              </div>
              <div className='mt-3'>
                <div className='mb-3'>
                  <textarea
                    className='form-control'
                    rows={3}
                    placeholder='Viết bình luận của bạn về sản phẩm...'
                  ></textarea>
                </div>
                <button className='btn btn-primary'>Gửi Bình Luận</button>
              </div>
            </div>

            <div className='mt-5 mb-3'>
              <h4>Sản phẩm cùng danh mục</h4>
              <div className='row mt-3'>
                {relatedProducts.map((product) => (
                  <div key={product.id} className='col-12 col-sm-6 col-md-4 col-lg-3 mb-3'>
                    <div className='card h-100' style={{ borderRadius: '8px' }}>
                      <img
                        src={product.imageUrl}
                        className='card-img-top'
                        alt={product.name}
                        style={{ height: '200px', objectFit: 'cover' }}
                      />
                      <div className='card-body'>
                        <h5 className='card-title'>{product.name}</h5>
                        <p className='card-text'>{product.price}</p>
                        {product.discount > 0 && (
                          <span
                            className='badge bg-success'
                            style={{
                              position: 'absolute',
                              top: '10px',
                              left: '10px',
                              zIndex: 10
                            }}
                          >
                            -{product.discount}%
                          </span>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className='col-md-3'>
            <div className='category-box p-3 rounded shadow-sm'>
              <h5 className='fw-bold'>THƯ MỤC</h5>
              <div className='category-line'></div>
              <ul className='list-group'>
                {categories.map((category, index) => (
                  <li key={index} className='list-group-item d-flex justify-content-between align-items-center'>
                    {category}
                    <button className='btn btn-outline-secondary btn-sm'>
                      <i className='bi bi-plus'></i>
                    </button>
                  </li>
                ))}
              </ul>
            </div>

            <div className='recent-posts mt-4 p-3 rounded shadow-sm'>
              <h5 className='fw-bold'>BÀI ĐĂNG GẦN ĐÂY</h5>
              <div className='category-line'></div>
              {recentPosts.map((post, index) => (
                <div key={index} className='d-flex align-items-start mb-3'>
                  <img src={post.img} className='img-thumbnail me-3' alt='Post' width='70' />
                  <div>
                    <p className='mb-1 post-title'>{post.title}</p>
                    <p className='text-muted mb-0'>
                      <i className='bi bi-calendar'></i> {post.date}
                    </p>
                  </div>
                </div>
              ))}
            </div>

            <div className='keywords mt-4 mb-2 p-3 rounded shadow-sm'>
              <h5 className='fw-bold'>TỪ KHÓA</h5>
              <div className='category-line'></div>
              <div className='d-flex flex-wrap'>
                {keywords.map((keyword, index) => (
                  <span key={index} className='badge bg-light text-dark keyword-badge'>
                    {keyword}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

export default DetailsProduct
