import { useLocation, Link, useParams } from "react-router-dom";
import { useState, useEffect } from "react";

function Breadcrumb() {
    const location = useLocation();
    const { slug } = useParams(); 
    const pathnames = location.pathname.split("/").filter((x) => x);

    useEffect(() => {
        if (slug) {
            const formattedName = slug
                .split("-")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
                .join(" ");
            setProductName(formattedName);
        }
    }, [slug]);

    const [productName, setProductName] = useState("");

    return (
        <nav className="p-2">
            <ol className="flex items-center space-x-2 text-1xl mt-5 ml-[170px] bg-gray-100 font-semibold w-[1350px] h-[40px]">
                <li>
                    <Link to="/" className="text-gray-700 hover:text-blue-500">
                        Trang Chủ
                    </Link>
                </li>
                <li className="text-gray-700">→</li>
                <li>
                    <Link
                        to="/products"
                        className="text-gray-700 hover:text-blue-500"
                    >
                        Sản Phẩm
                    </Link>
                </li>
                {slug && (
                    <>
                        <li className="text-gray-700">→</li>
                        <li className="text-blue-500">Giày {productName}</li>
                    </>
                )}
            </ol>
        </nav>
    );
}

export default Breadcrumb;
