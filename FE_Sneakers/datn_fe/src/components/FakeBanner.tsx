import React, { useEffect, useRef, useState } from "react";

const FakeBanner = () => {
    const [buyers, setBuyers] = useState(0);
    const [sold, setSold] = useState(0);
    const [startCounting, setStartCounting] = useState(false);
    const [showVideo, setShowVideo] = useState(false);

    const bannerRef = useRef<HTMLDivElement | null>(null);

    const buyersTarget = 667392;
    const soldTarget = 1349841;

    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setStartCounting(true);
                    setShowVideo(true);
                    observer.disconnect();
                }
            },
            { threshold: 0.7 }
        );

        if (bannerRef.current) {
            observer.observe(bannerRef.current);
        }

        return () => observer.disconnect();
    }, []);

    useEffect(() => {
        if (!startCounting) return;

        const interval = setInterval(() => {
            setBuyers((prev) =>
                prev < buyersTarget ? Math.min(prev + 8000, buyersTarget) : prev
            );
            setSold((prev) =>
                prev < soldTarget ? Math.min(prev + 15000, soldTarget) : prev
            );
        }, 30);

        return () => clearInterval(interval);
    }, [startCounting]);

    return (
        <div
            ref={bannerRef}
            className="flex flex-col md:flex-row bg-[#f5f5f5] w-full md:h-[500px] h-auto overflow-hidden"
        >
            <div className="md:w-2/3 w-full p-8 ml-[120px] flex flex-col justify-center items-center md:items-start text-center md:text-left">
                <p className="text-gray-700 text-base max-w-xl leading-relaxed">
                    Hơn 10 năm phát triển,{" "}
                    <span className="font-semibold">CodeFarm Sneakers</span>{" "}
                    luôn mang đến những mẫu giày chất lượng tốt nhất với giá cả
                    hợp lý nhất đến tay người tiêu dùng với hệ thống cửa hàng Số
                    1 Hà Nội và bán online khắp Việt Nam.
                </p>

                <div className="mt-10 space-y-6">
                    <div>
                        <h2 className="text-5xl font-bold tracking-wider text-black">
                            {sold.toLocaleString()}
                        </h2>
                        <p className="text-gray-600 mt-2 text-lg">
                            Số Sản Phẩm Đã Bán
                        </p>
                    </div>
                    <div>
                        <h2 className="text-5xl font-bold tracking-wider text-black">
                            {buyers.toLocaleString()}
                        </h2>
                        <p className="text-gray-600 mt-2 text-lg">
                            Khách Hàng Đã Mua
                        </p>
                    </div>
                </div>
            </div>

            <div className="md:w-2/3 w-[100px] h-[250px] md:h-[350px] flex justify-center items-center p-4 mr-[120px] mt-20">
                {showVideo && (
                    <iframe
                        className="w-full h-full rounded-lg shadow-md"
                        src="https://www.youtube.com/embed/yli_xeCmwmE?autoplay=1&mute=1&start=4&playlist=yli_xeCmwmE&loop=1"
                        title="YouTube video player"
                        allow="autoplay; encrypted-media"
                        allowFullScreen
                    ></iframe>
                )}
            </div>
        </div>
    );
};

export default FakeBanner;
