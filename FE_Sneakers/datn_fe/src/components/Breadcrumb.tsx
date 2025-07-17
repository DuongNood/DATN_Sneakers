import { useLocation, Link, useParams } from "react-router-dom";
import { useState, useEffect } from "react";

function Breadcrumb() {
    const location = useLocation();
    const { id } = useParams(); // Lấy id nếu có
    const [productName, setProductName] = useState("");
    const pathnames = location.pathname.split("/").filter((x) => x);

    useEffect(() => {
        // Tách tên sản phẩm từ URL
        if (pathnames.length > 1) {
            const lastPart = pathnames[pathnames.length - 1]; // Lấy phần cuối (ví dụ: "1-jordan")
            const namePart = lastPart.split("-").pop() || lastPart; // Lấy phần sau dấu "-" hoặc toàn bộ
            setProductName(
                namePart
                    .replace(/-/g, " ")
                    .replace(/\b\w/g, (c) => c.toUpperCase())
            ); // Chuyển thành tên đẹp
        }
    }, [location.pathname]);

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
                {pathnames.length > 1 && (
                    <>
                        <li className="text-gray-700">→</li>
                        <li className="text-red-600">{productName}</li>
                    </>
                )}
            </ol>
        </nav>
    );
}

export default Breadcrumb;
