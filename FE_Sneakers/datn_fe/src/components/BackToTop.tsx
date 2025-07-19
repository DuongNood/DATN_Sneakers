import { useState, useEffect } from "react";
import { FaArrowUp } from "react-icons/fa";
import { motion } from "framer-motion";

const BackToTop = () => {
    const [isVisible, setIsVisible] = useState(false);

    useEffect(() => {
        const toggleVisibility = () => {
            if (window.scrollY > 300) {
                setIsVisible(true);
            } else {
                setIsVisible(false);
            }
        };

        window.addEventListener("scroll", toggleVisibility);

        return () => window.removeEventListener("scroll", toggleVisibility);
    }, []);

    const scrollToTop = () => {
        const scrollStep = -window.scrollY / (1000 / 15);
        let scrollCount = 0;
        const scrollInterval = setInterval(() => {
            if (window.scrollY !== 0) {
                window.scrollBy(0, scrollStep);
                scrollCount += 15;
                if (scrollCount >= 1000) {
                    clearInterval(scrollInterval);
                    window.scrollTo(0, 0);
                }
            } else {
                clearInterval(scrollInterval);
            }
        }, 15);
    };

    const buttonVariants = {
        hidden: {
            y: 100,
            opacity: 0,
        },
        visible: {
            y: 0,
            opacity: 1,
            transition: {
                duration: 0.5,
                ease: "easeInOut",
            },
        },
    };

    return (
        <motion.button
            onClick={scrollToTop}
            className="fixed bottom-40 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 z-50"
            variants={buttonVariants}
            initial="hidden"
            animate={isVisible ? "visible" : "hidden"}
            aria-label="Back to Top"
        >
            <FaArrowUp size={27} />
        </motion.button>
    );
};

export default BackToTop;
