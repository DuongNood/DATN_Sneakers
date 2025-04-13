import React, { useEffect, useState } from 'react'
import { useSearchParams } from 'react-router-dom'
import SearchBar from '../components/SearchBar'
import axios from 'axios'

interface Product {
  id: number
  product_name: string
  product_code: string
  description: string
  original_price: number
  discounted_price: number | null
  images: { image_path: string }[]
}

const Products: React.FC = () => {
  const [searchParams, setSearchParams] = useSearchParams()
  const [products, setProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchProducts = async () => {
    try {
      setLoading(true)
      const search = searchParams.get('search') || ''
      const response = await axios.get(`/api/products?search=${encodeURIComponent(search)}`)
      setProducts(response.data.data)
      setError(null)
    } catch (err) {
      setError('Không thể tải danh sách sản phẩm')
      console.error(err)
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchProducts()
  }, [searchParams])

  const handleSearch = (searchTerm: string) => {
    setSearchParams({ search: searchTerm })
  }

  if (loading) {
    return <div className='flex justify-center items-center h-screen'>Loading...</div>
  }

  if (error) {
    return <div className='text-red-500 text-center p-4'>{error}</div>
  }

  return (
    <div className='container mx-auto px-4 py-8'>
      <div className='mb-8'>
        <SearchBar onSearch={handleSearch} className='max-w-2xl mx-auto' />
      </div>

      <div className='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6'>
        {products.map((product) => (
          <div key={product.id} className='bg-white rounded-lg shadow-md overflow-hidden'>
            {product.images.length > 0 && (
              <img
                src={`/storage/${product.images[0].image_path}`}
                alt={product.product_name}
                className='w-full h-48 object-cover'
              />
            )}
            <div className='p-4'>
              <h3 className='text-lg font-semibold mb-2'>{product.product_name}</h3>
              <p className='text-gray-600 mb-4 line-clamp-2'>{product.description}</p>
              <div className='flex justify-between items-center'>
                <span className='text-red-500 font-bold'>
                  {product.discounted_price
                    ? product.discounted_price.toLocaleString('vi-VN')
                    : product.original_price.toLocaleString('vi-VN')}
                  đ
                </span>
                <button className='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600'>Xem chi tiết</button>
              </div>
            </div>
          </div>
        ))}
      </div>

      {products.length === 0 && (
        <div className='text-center text-gray-500 py-8'>Không tìm thấy sản phẩm nào phù hợp với tiêu chí tìm kiếm.</div>
      )}
    </div>
  )
}

export default Products
