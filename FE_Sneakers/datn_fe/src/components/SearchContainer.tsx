import React, { useState, useEffect } from 'react'
import { useTranslation } from 'react-i18next'
import { useNavigate } from 'react-router-dom'

interface SearchInputProps {
  value: string
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void
  onSearch: () => void
  categories: { id: number; category_name: string }[]
  sizes: { id: number; name: string }[]
  onCategoryChange: (e: React.ChangeEvent<HTMLSelectElement>) => void
  onSizeChange: (e: React.ChangeEvent<HTMLSelectElement>) => void
}

const SearchInput: React.FC<SearchInputProps> = ({
  value,
  onChange,
  onSearch,
  categories,
  sizes,
  onCategoryChange,
  onSizeChange
}) => {
  const { t } = useTranslation()

  const handleKeyPress = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      onSearch()
    }
  }

  return (
    <div className='flex flex-col md:flex-row gap-2'>
      <input
        type='text'
        value={value}
        onChange={onChange}
        onKeyPress={handleKeyPress}
        className='w-full md:w-64 px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full 
        focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
        placeholder={t('search_placeholder')}
      />
      <select
        onChange={onCategoryChange}
        className='w-full md:w-40 px-4 py-2 bg-gray-100 border border-gray-300 rounded-full'
      >
        <option value=''>{t('all_categories')}</option>
        {categories.map((category) => (
          <option key={category.id} value={category.id}>
            {category.category_name}
          </option>
        ))}
      </select>
      <select
        onChange={onSizeChange}
        className='w-full md:w-40 px-4 py-2 bg-gray-100 border border-gray-300 rounded-full'
      >
        <option value=''>{t('all_sizes')}</option>
        {sizes.map((size) => (
          <option key={size.id} value={size.id}>
            {size.name}
          </option>
        ))}
      </select>
      <button onClick={onSearch} className='px-4 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600'>
        {t('search')}
      </button>
    </div>
  )
}

const SearchContainer: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('')
  const [categoryId, setCategoryId] = useState('')
  const [sizeId, setSizeId] = useState('')
  const [categories, setCategories] = useState<{ id: number; category_name: string }[]>([])
  const [sizes, setSizes] = useState<{ id: number; name: string }[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const navigate = useNavigate()
  const { t } = useTranslation()

  // Lấy danh sách danh mục và kích thước khi component mount
  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/categories')
        const data = await response.json()
        setCategories(data.data || [])
      } catch (err) {
        console.error('Error fetching categories:', err)
      }
    }

    const fetchSizes = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/sizes')
        const data = await response.json()
        setSizes(data.data || [])
      } catch (err) {
        console.error('Error fetching sizes:', err)
      }
    }

    fetchCategories()
    fetchSizes()
  }, [])

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(e.target.value)
  }

  const handleCategoryChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setCategoryId(e.target.value)
  }

  const handleSizeChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
    setSizeId(e.target.value)
  }

  const handleSearch = async () => {
    if (!searchTerm.trim() && !categoryId && !sizeId) {
      setError(t('please_enter_search_term'))
      return
    }

    setLoading(true)
    setError(null)

    try {
      const queryParams = new URLSearchParams()
      if (searchTerm) queryParams.append('query', searchTerm)
      if (categoryId) queryParams.append('category_id', categoryId)
      if (sizeId) queryParams.append('size_id', sizeId)

      const response = await fetch(`http://localhost:8000/api/products?${queryParams.toString()}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('API response not ok')
      }

      navigate(`/search?${queryParams.toString()}`)
    } catch (err) {
      setError(t('search_error'))
      console.error('Search error:', err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className='search-container p-4'>
      <SearchInput
        value={searchTerm}
        onChange={handleInputChange}
        onSearch={handleSearch}
        categories={categories}
        sizes={sizes}
        onCategoryChange={handleCategoryChange}
        onSizeChange={handleSizeChange}
      />
      {loading && <p className='mt-2'>{t('loading')}</p>}
      {error && <p className='mt-2 text-red-500'>{error}</p>}
    </div>
  )
}

export default SearchContainer
