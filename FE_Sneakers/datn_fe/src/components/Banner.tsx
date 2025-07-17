import React, { useState, useEffect } from "react";

const Banner = () => {
    const [currentSlide, setCurrentSlide] = useState(0);

    const slides = [
        "https://bizweb.dktcdn.net/100/413/756/collections/jordan-2.jpg?v=1617462460240",
        "https://cdn.shopify.com/s/files/1/0456/5070/6581/files/top-5-doi-giay-sneaker-hot-trend-2024-10.jpg?v=1703604123",
        "https://file.hstatic.net/1000008082/file/puma_2_6b9f0cfd4c1c41b1ad017f6b285e8c7d.jpg",
    ];

    useEffect(() => {
        const timer = setInterval(() => {
            setCurrentSlide((prev) => (prev + 1) % slides.length);
        }, 2000);

        return () => clearInterval(timer);
    }, [slides.length]);

    return (
        <div className="relative w-full h-[580px] overflow-hidden">
            {slides.map((slide, index) => (
                <div
                    key={index}
                    className={`absolute top-0 left-0 w-full h-full transition-opacity duration-1000 ease-in-out ${
                        index === currentSlide ? "opacity-100" : "opacity-0"
                    }`}
                >
                    <img
                        src={slide}
                        alt={`Slide ${index + 1}`}
                        className="w-full h-full object-cover"
                    />
                </div>
            ))}

            <div className="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                {slides.map((_, index) => (
                    <button
                        key={index}
                        className={`w-3 h-3 rounded-full ${
                            index === currentSlide ? "bg-white" : "bg-gray-400"
                        }`}
                        onClick={() => setCurrentSlide(index)}
                    ></button>
                ))}
            </div>
        </div>
    );
};

export default Banner;
