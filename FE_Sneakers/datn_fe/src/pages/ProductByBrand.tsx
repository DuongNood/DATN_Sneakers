import { useState, useEffect } from 'react'
import { useParams } from 'react-router-dom'
import axios from 'axios'
import { useTranslation } from 'react-i18next'
import ProductCard from '../components/ProductCard'
import Loading from '../components/Loading'
import ReactPaginate from 'react-paginate'

interface Product {
  id: number
  product_name: string
  original_price: number
  discounted_price: number
  image: string
  brand: {
    brand_name: string
  }
}

const ProductByBrand = () => {
  const { id } = useParams()
  const { t } = useTranslation()
  const [products, setProducts] = useState<Product[]>([])
  const [loading, setLoading] = useState(true)
  const [brandName, setBrandName] = useState('')
  const [sortBy, setSortBy] = useState('default')

  const [page, setPage] = useState(0)
  const [totalPages, setTotalPages] = useState(0)
  const itemsPerPage = 10

  // Filtered products state
  const [filteredProducts, setFilteredProducts] = useState<Product[]>([])

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true)
        // console.log('Fetching products for brand ID:', id)
        const response = await axios.get(`http://localhost:8000/api/productbybrand/${id}`)

        if (response.data && response.data.success) {
          const productsData = response.data.data
          setProducts(productsData)
          setFilteredProducts(productsData)
          setTotalPages(Math.ceil(productsData.length / itemsPerPage))

          if (productsData.length > 0 && productsData[0].brand) {
            setBrandName(productsData[0].brand.brand_name)
          }
        }
      } catch (error: any) {
        console.error('Error fetching products:', error)
        setProducts([])
        setFilteredProducts([])
      } finally {
        setLoading(false)
      }
    }

    if (id) {
      fetchProducts()
    }
  }, [id])

  // Apply sorting
  useEffect(() => {
    let result = [...products]

    // Apply sorting
    switch (sortBy) {
      case 'price-asc':
        result.sort((a, b) => (a.discounted_price || a.original_price) - (b.discounted_price || b.original_price))
        break
      case 'price-desc':
        result.sort((a, b) => (b.discounted_price || b.original_price) - (a.discounted_price || a.original_price))
        break
      case 'newest':
        result.sort((a, b) => b.id - a.id)
        break
      default:
        break
    }

    setFilteredProducts(result)
    setTotalPages(Math.ceil(result.length / itemsPerPage))
    setPage(0)
  }, [products, sortBy])

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
            {brandName ? (
              <>
                {t('brands')}: <span className='text-yellow-300 ml-2'>{brandName}</span>
              </>
            ) : (
              t('products')
            )}
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

export default ProductByBrand
