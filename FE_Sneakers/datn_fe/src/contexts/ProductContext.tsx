import React, { createContext, useContext, useState, useEffect } from "react";

interface Product {
    id: number;
    name: string;
    original_price: string;
    discounted_price: string;
    product_code: string;
    imageUrl: string | null;
    rating: number;
    description: string;
    quantity?: number;
    images?: string[];
}

const ProductContext = createContext<{
    products: Product[];
    currentProduct: Product | null;
    loading: boolean;
    error: string | null;
    setCurrentProductId: (id: number | null) => void;
    refreshProducts: () => void;
}>({
    products: [],
    currentProduct: null,
    loading: true,
    error: null,
    setCurrentProductId: () => {},
    refreshProducts: () => {},
});

export const ProductProvider: React.FC<{ children: React.ReactNode }> = ({
    children,
}) => {
    const [products, setProducts] = useState<Product[]>(() => {
        const cached = localStorage.getItem("products");
        return cached ? JSON.parse(cached) : [];
    });
    const [currentProduct, setCurrentProduct] = useState<Product | null>(null);
    const [loading, setLoading] = useState(!localStorage.getItem("products"));
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProducts = async () => {
            if (localStorage.getItem("products")) {
                setLoading(false);
                return;
            }
            setLoading(true);
            try {
                const response = await fetch(
                    "http://localhost:8000/api/products"
                );
                if (!response.ok) throw new Error("API không phản hồi");
                const data = await response.json();
                const newProducts = data.data || [];
                setProducts(newProducts);
                localStorage.setItem("products", JSON.stringify(newProducts));
                setError(null);
            } catch (err) {
                setError(
                    "Lỗi khi fetch danh sách sản phẩm: " +
                        (err as Error).message
                );
            } finally {
                setLoading(false);
            }
        };

        fetchProducts();
    }, []);

    const setCurrentProductId = async (id: number | null) => {
        if (!id) {
            setCurrentProduct(null);
            return;
        }
        setLoading(true);
        try {
            const response = await fetch(
                `http://localhost:8000/api/detail-product/${id}`
            );
            if (!response.ok) throw new Error("API không phản hồi");
            const data = await response.json();
            setCurrentProduct(data.data || data);
        } catch (err) {
            setError(
                "Lỗi khi fetch chi tiết sản phẩm: " + (err as Error).message
            );
        } finally {
            setLoading(false);
        }
    };

    const refreshProducts = () => {
        localStorage.removeItem("products");
        setProducts([]);
        setCurrentProduct(null);
        setLoading(true);
        setError(null);
    };

    return (
        <ProductContext.Provider
            value={{
                products,
                currentProduct,
                loading,
                error,
                setCurrentProductId,
                refreshProducts,
            }}
        >
            {children}
        </ProductContext.Provider>
    );
};

export const useProducts = () => useContext(ProductContext);
