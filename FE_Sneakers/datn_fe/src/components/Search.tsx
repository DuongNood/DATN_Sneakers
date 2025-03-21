import React from 'react'

interface SearchInputProps {
  value: string
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void
}

const SearchInput: React.FC<SearchInputProps> = ({ value, onChange }) => {
  return (
    <input
      type='text'
      value={value}
      onChange={onChange}
      className='w-64 px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-full 
      focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-300'
      placeholder='Tìm kiếm sản phẩm...'
    />
  )
}

export default SearchInput
