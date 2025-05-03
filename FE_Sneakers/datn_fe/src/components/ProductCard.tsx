import React from 'react'
import { useNavigate } from 'react-router-dom'
import { formatCurrency } from '../utils/formatCurrency'

interface Product {
  id: number
  product_name: string
  original_price: number
  discounted_price: number
  image: string
}

interface ProductCardProps {
  product: Product
}

const ProductCard: React.FC<ProductCardProps> = ({ product }) => {
  const navigate = useNavigate()

  const createSlug = (str: string) => {
    return str
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[đĐ]/g, 'd')
      .replace(/([^0-9a-z-\s])/g, '')
      .replace(/(\s+)/g, '-')
      .replace(/-+/g, '-')
      .replace(/^-+|-+$/g, '')
  }

  const handleProductClick = () => {
    const slug = createSlug(product.product_name)
    navigate(`/${slug}`, { state: { id: product.id } })
  }

  const calculateDiscount = () => {
    if (!product.original_price || !product.discounted_price) return 0
    return Math.round(((product.original_price - product.discounted_price) / product.original_price) * 100)
  }

  const discount = calculateDiscount()

  return (
    <div onClick={handleProductClick} className='block w-[200px] bg-white p-2 rounded-lg cursor-pointer'>
      <div className='relative'>
        {/* Discount badge */}
        {discount > 0 && (
          <div className='absolute top-2 left-2 bg-red-600 text-white text-xs font-medium px-2 py-1 rounded'>
            -{discount}%
          </div>
        )}

        {/* Product Image */}
        <div className='w-full h-[180px] mb-4 rounded-lg overflow-hidden'>
          <img src={product.image} alt={product.product_name} className='w-full h-full object-contain' />
        </div>

        {/* Product Info */}
        <div className='px-1'>
          {/* Product Name */}
          <h3 className='text-sm font-medium text-gray-900 line-clamp-2 min-h-[40px] mb-1'>{product.product_name}</h3>

          {/* Price Section */}
          {discount > 0 ? (
            <div className='flex items-center gap-2'>
              <div className='text-gray-500 text-sm line-through'>{formatCurrency(product.original_price)}</div>
              <div className='text-red-600 font-bold'>{formatCurrency(product.discounted_price)}</div>
            </div>
          ) : (
            <div className='text-gray-900 font-bold'>{formatCurrency(product.original_price)}</div>
          )}
        </div>
      </div>
    </div>
  )
}

export default ProductCard
