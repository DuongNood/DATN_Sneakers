// ProductContext.tsx
import React, { createContext, useContext, useState, useEffect } from 'react'

interface Product {
  id: number
  name: string
  original_price: string
  discounted_price: string
  product_code: string
  imageUrl: string | null
  rating: number
  description: string
  quantity?: number
  images?: string[]
}

const ProductContext = createContext<{ products: Product[] }>({ products: [] })

export const ProductProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const [products, setProducts] = useState<Product[]>([])

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/products')
        if (!response.ok) throw new Error('API không phản hồi')
        const data = await response.json()
        setProducts(data.data || [])
      } catch (error) {
        // console.error('Lỗi khi fetch danh sách sản phẩm:', error)
      }
    }

    fetchProducts()
  }, [])

  return <ProductContext.Provider value={{ products }}>{children}</ProductContext.Provider>
}

export const useProducts = () => useContext(ProductContext)
