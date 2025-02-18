import { useState, useEffect } from "react";
import { useSpring, animated } from "@react-spring/web";

const Banner = () => {
  const [index, setIndex] = useState(0);

  const images = [
    { src: "/src/assets/banner3.png", text: "SIÊU ƯU ĐÃI THÁNG 2" },
    { src: "/src/assets/banner3.png", text: "SĂN DEAL CỰC SỐC" },
  ];

  const slideAnimation = useSpring({
    transform: `translateX(-${index * 100}%)`,
    config: { tension: 200, friction: 30 },
  });

  const textAnimation = useSpring({
    opacity: 1,
    from: { opacity: 0 },
    reset: true,
    config: { duration: 1000 },
  });

  useEffect(() => {
    const interval = setInterval(() => {
      setIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
    }, 4000); // 4 giây đổi ảnh

    return () => clearInterval(interval);
  }, [images.length]);

  return (
    <div className="relative w-full h-[600px] overflow-hidden">
      <animated.div style={slideAnimation} className="flex w-[200%] h-full">
        {images.map((item, i) => (
          <div key={i} className="w-1/2 h-full flex-shrink-0 relative">
            <img
              src={item.src}
              alt={`Slide ${i + 1}`}
              className="w-full h-full object-cover"
            />

            {i === index && (
              <animated.div
                style={textAnimation}
                className="absolute inset-0 flex justify-center items-center bg-black bg-opacity-30"
              >
                <h1 className="text-white text-5xl md:text-7xl font-extrabold drop-shadow-2xl text-center">
                  {item.text}
                </h1>
              </animated.div>
            )}
          </div>
        ))}
      </animated.div>
    </div>
  );
};

export default Banner;
