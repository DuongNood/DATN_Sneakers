import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'

interface SearchBarProps {
  onSearch?: (searchTerm: string) => void
  className?: string
}

const SearchBar: React.FC<SearchBarProps> = ({ onSearch, className = '' }) => {
  const [searchTerm, setSearchTerm] = useState('')
  const navigate = useNavigate()

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault()
    if (onSearch) {
      onSearch(searchTerm)
    } else {
      navigate(`/products?search=${encodeURIComponent(searchTerm)}`)
    }
  }

  return (
    <form onSubmit={handleSearch} className={`flex items-center ${className}`}>
      <div className='relative flex-grow'>
        <input
          type='text'
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          placeholder='Tìm kiếm sản phẩm...'
          className='w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500'
        />
        <button type='submit' className='absolute right-0 top-0 h-full px-4 text-gray-500 hover:text-gray-700'>
          <svg
            xmlns='http://www.w3.org/2000/svg'
            className='h-5 w-5'
            fill='none'
            viewBox='0 0 24 24'
            stroke='currentColor'
          >
            <path
              strokeLinecap='round'
              strokeLinejoin='round'
              strokeWidth={2}
              d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'
            />
          </svg>
        </button>
      </div>
    </form>
  )
}

export default SearchBar
