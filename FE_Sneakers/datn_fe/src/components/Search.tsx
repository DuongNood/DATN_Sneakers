import React, { useState } from 'react'
import { useTranslation } from 'react-i18next'
import { useNavigate } from 'react-router-dom'

interface SearchInputProps {
  value: string
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void
  onSearch: () => void
}

const SearchInput: React.FC<SearchInputProps> = ({ value, onChange, onSearch }) => {
  const handleKeyPress = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter') {
      console.log('Enter pressed, calling onSearch')
      onSearch()
    }
  }
  const { t, i18n } = useTranslation()
  return (
    <input
      type='text'
      value={value}
      onChange={onChange}
      onKeyPress={handleKeyPress}
      className='w-64 px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full 
      focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
      placeholder={t('search_placeholder')}
    />
  )
}

const SearchContainer: React.FC = () => {
  const [searchTerm, setSearchTerm] = useState('')
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const navigate = useNavigate()

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(e.target.value)
  }

  const handleSearch = async () => {
    if (!searchTerm.trim()) {
      console.log('Search term is empty, skipping')
      return
    }

    setLoading(true)
    setError(null)
    console.log('Starting search with term:', searchTerm)

    try {
      const response = await fetch(`http://localhost:8000/api/products?query=${encodeURIComponent(searchTerm)}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        }
      })

      if (!response.ok) {
        throw new Error('API response not ok')
      }

      console.log('API call successful, navigating to /search')
      navigate(`/search?query=${encodeURIComponent(searchTerm)}`)
    } catch (err) {
      setError('Có lỗi xảy ra khi tìm kiếm')
      console.error('Search error:', err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className='search-container'>
      <SearchInput value={searchTerm} onChange={handleInputChange} onSearch={handleSearch} />
      {loading && <p>Đang tìm kiếm...</p>}
      {error && <p className='text-red-500'>{error}</p>}
    </div>
  )
}

export default SearchContainer
