import { useState, useEffect } from 'react'
import { useTranslation } from 'react-i18next'
import ProductCard from '../components/ProductCard'
import Loading from '../components/Loading'
import ReactPaginate from 'react-paginate'

interface Product {
  id: number
  product_name: string
  original_price: string
  discounted_price: string
  image: string
  rating: number
}

const ProductSale = () => {
  const { t } = useTranslation()
  const [products, setProducts] = useState<Product[]>([])
  const [filteredProducts, setFilteredProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(true)
  const [sortBy, setSortBy] = useState('default')
  const [page, setPage] = useState(0)
  const [totalPages, setTotalPages] = useState(0)
  const itemsPerPage = 5

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true)
        const response = await fetch('http://localhost:8000/api/home-products')
        if (!response.ok) throw new Error(t('api_error'))
        const data = await response.json()
        const allProducts = Array.isArray(data) ? data : data.data || []

        // Lọc sản phẩm có giảm giá >= 20%
        const filtered = allProducts.filter((product: Product) => {
          const originalPrice = Number(product.original_price)
          const discountedPrice = Number(product.discounted_price)
          if (isNaN(originalPrice) || isNaN(discountedPrice) || originalPrice <= 0) return false
          const discountPercentage = ((originalPrice - discountedPrice) / originalPrice) * 100
          return discountPercentage >= 20
        })

        setProducts(filtered)
        setFilteredProducts(filtered)
        setTotalPages(Math.ceil(filtered.length / itemsPerPage))
      } catch (error: any) {
        console.error('Error fetching products:', error)
        setProducts([])
        setFilteredProducts([])
      } finally {
        setLoading(false)
      }
    }

    fetchProducts()
  }, [t])

  useEffect(() => {
    // Sắp xếp sản phẩm khi sortBy thay đổi
    let sortedProducts = [...products]
    switch (sortBy) {
      case 'price-asc':
        sortedProducts.sort((a, b) => Number(a.discounted_price) - Number(b.discounted_price))
        break
      case 'price-desc':
        sortedProducts.sort((a, b) => Number(b.discounted_price) - Number(a.discounted_price))
        break
      case 'newest':
        sortedProducts.sort((a, b) => b.id - a.id) // Giả sử ID lớn hơn là sản phẩm mới hơn
        break
      default:
        sortedProducts = [...products]
    }
    setFilteredProducts(sortedProducts)
    setPage(0) // Reset về trang đầu khi thay đổi sắp xếp
  }, [sortBy, products])

  const handlePageChange = ({ selected }: { selected: number }) => {
    setPage(selected)
  }

  const handleSortChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setSortBy(e.target.value)
  }

  const getCurrentPageProducts = () => {
    const start = page * itemsPerPage
    const end = start + itemsPerPage
    return filteredProducts.slice(start, end)
  }

  if (loading) {
    return <Loading />
  }

  return (
    <div className='min-h-screen bg-gray-50'>
      {/* Banner Section */}
      <div
        className='relative h-[100px] bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 shadow-lg'
        style={{ margin: '0 190px' }}
      >
        <div className='absolute inset-0 bg-[radial-gradient(circle,rgba(255,255,255,0.1)_1px,transparent_1px)] bg-[length:20px_20px] opacity-20'></div>
        <div className='absolute inset-0 flex items-center justify-center'>
          <h1 className='text-3xl font-bold text-white tracking-wide'>
            {t('sale_products')} <span className='text-yellow-300 ml-2'>({t('discount_20_percent')})</span>
          </h1>
        </div>
      </div>

      {/* Main Content */}
      <div className='max-w-[1200px] mx-auto px-4 py-8'>
        {/* Sort Section */}
        <div className='mb-8 flex justify-start'>
          <select
            value={sortBy}
            onChange={handleSortChange}
            className='px-4 py-2 bg-white rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-[200px]'
          >
            <option value='default'>{t('sort_by_default')}</option>
            <option value='price-asc'>{t('sort_by_price_low_to_high')}</option>
            <option value='price-desc'>{t('sort_by_price_high_to_low')}</option>
            <option value='newest'>{t('sort_by_newest')}</option>
          </select>
        </div>

        {/* Products Grid */}
        {filteredProducts.length === 0 ? (
          <div className='text-center py-12'>
            <h3 className='text-xl font-medium text-gray-600'>{t('no_products_found')}</h3>
            <p className='text-gray-500 mt-2'>{t('try_different_filters')}</p>
          </div>
        ) : (
          <>
            <div className='flex flex-wrap gap-6 justify-center'>
              {getCurrentPageProducts().map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>

            {/* Pagination */}
            <div className='mt-8 flex justify-center'>
              <ReactPaginate
                previousLabel='Previous'
                nextLabel='Next'
                pageCount={totalPages}
                onPageChange={handlePageChange}
                forcePage={page}
                containerClassName='flex items-center gap-2'
                pageClassName='px-3 py-1 rounded border hover:bg-gray-100'
                previousClassName='px-3 py-1 rounded border hover:bg-gray-100'
                nextClassName='px-3 py-1 rounded border hover:bg-gray-100'
                activeClassName='!bg-blue-500 text-white border-blue-500'
                disabledClassName='opacity-50 cursor-not-allowed'
              />
            </div>
          </>
        )}
      </div>
    </div>
  )
}

export default ProductSale
