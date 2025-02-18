import {
  FaSearch,
  FaUser,
  FaShoppingCart,
  FaTimes,
  FaChevronDown,
} from "react-icons/fa";
import { motion, AnimatePresence } from "framer-motion";
import { useState, useEffect } from "react";
import { Link } from "react-router-dom";

const Header = () => {
  const [menuOpen, setMenuOpen] = useState(false);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [newsDropdownOpen, setNewsDropdownOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [placeholder, setPlaceholder] = useState("Tìm kiếm...");

  useEffect(() => {
    const interval = setInterval(() => {
      setPlaceholder("Tìm kiếm...");
    }, 1500);
    return () => clearInterval(interval);
  }, []);

  return (
    <header className="w-full bg-white text-white fixed top-0 left-0 z-50 ">
      <div className="max-w-7xl mx-auto px-6 lg:px-10 py-4 flex justify-between items-center">
        <h1 className="text-2xl font-bold relative overflow-hidden">
          <Link to="/" className="relative block text-black">
            PoleSneaker
          </Link>
        </h1>

        <nav className="hidden lg:flex space-x-6">
          <Link to="/" className="hover:text-blue-400 transition text-black">
            Trang chủ
          </Link>

          <Link
            to="/contact"
            className="hover:text-blue-400 transition text-black"
          >
            Giới thiệu
          </Link>

          <div
            className="relative group"
            onMouseEnter={() => setDropdownOpen(true)}
            onMouseLeave={() => setDropdownOpen(false)}
          >
            <button className="flex items-center hover:text-blue-400 transition text-black">
              Sản phẩm
              <FaChevronDown
                className={`ml-1 transform ${dropdownOpen ? "rotate-180" : ""}`}
              />
            </button>
            <AnimatePresence>
              {dropdownOpen && (
                <motion.div
                  initial={{ opacity: 0, y: -10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className="absolute bg-gray-800 mt-2 rounded-lg shadow-lg w-48"
                >
                  <Link
                    to="/fdfd"
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    Giày Adidas
                  </Link>
                  <Link
                    to="/fdfd"
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    Giày Nike
                  </Link>
                  <Link
                    to="/fdfd"
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    Giày Puma
                  </Link>
                </motion.div>
              )}
            </AnimatePresence>
          </div>

          <div
            className="relative group"
            onMouseEnter={() => setNewsDropdownOpen(true)}
            onMouseLeave={() => setNewsDropdownOpen(false)}
          >
            <button className="flex items-center hover:text-blue-400 transition text-black">
              Tin tức
              <FaChevronDown
                className={`ml-1 transform ${
                  newsDropdownOpen ? "rotate-180" : ""
                }`}
              />
            </button>
            <AnimatePresence>
              {newsDropdownOpen && (
                <motion.div
                  initial={{ opacity: 0, y: -10 }}
                  animate={{ opacity: 1, y: 0 }}
                  exit={{ opacity: 0, y: -10 }}
                  className="absolute bg-gray-800 mt-2 rounded-lg shadow-lg w-48"
                >
                  <Link
                    to="/tin-chinh-tri"
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    Giày mới ra
                  </Link>
                  <Link
                    to="*"
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    ádasd
                  </Link>
                  <Link
                    to=""
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    đâs
                  </Link>
                  <Link
                    to=""
                    className="block px-4 py-2 hover:bg-gray-700 text-white"
                  >
                    Tin xã hội
                  </Link>
                </motion.div>
              )}
            </AnimatePresence>
          </div>

          <Link
            to="/contact"
            className="hover:text-blue-400 transition text-black"
          >
            Liên hệ
          </Link>
        </nav>

        <div className="flex items-center space-x-4">
          <button
            onClick={() => setSearchOpen(!searchOpen)}
            className="text-xl text-black hover:text-blue-400 transition"
          >
            <FaSearch />
          </button>

          <Link
            to="/profile"
            className="flex items-center text-xl text-black hover:text-blue-400 transition"
          >
            <FaUser />
          </Link>

          <Link
            to="/cart"
            className="flex items-center text-xl text-black hover:text-blue-400 transition"
          >
            <FaShoppingCart />
          </Link>

          <button
            onClick={() => setMenuOpen(!menuOpen)}
            className="lg:hidden relative w-8 h-8 flex flex-col justify-between items-center"
          >
            <motion.span
              initial={false}
              animate={
                menuOpen
                  ? { rotate: 45, y: 10, backgroundColor: "#000000" }
                  : { rotate: 0, y: 0 }
              }
              transition={{ type: "spring", stiffness: 260, damping: 20 }}
              className="bg-black block h-1 w-8 rounded"
            />
            <motion.span
              initial={false}
              animate={menuOpen ? { opacity: 0 } : { opacity: 1 }}
              transition={{ duration: 0.2 }}
              className="bg-black block h-1 w-8 rounded"
            />
            <motion.span
              initial={false}
              animate={
                menuOpen
                  ? { rotate: -45, y: -10, backgroundColor: "#000000" }
                  : { rotate: 0, y: 0 }
              }
              transition={{ type: "spring", stiffness: 260, damping: 20 }}
              className="bg-black block h-1 w-8 rounded"
            />
          </button>
        </div>
      </div>

      <AnimatePresence>
        {searchOpen && (
          <>
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 0.8 }}
              exit={{ opacity: 0 }}
              className="fixed inset-0 bg-white z-40"
            />
            <motion.div
              initial={{ opacity: 0, y: -50 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -50 }}
              className="fixed inset-0 flex justify-center items-center z-50"
            >
              <input
                type="text"
                placeholder={placeholder}
                className="w-96 px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400 text-lg"
              />

              <button
                onClick={() => setSearchOpen(false)}
                className="absolute right-4 top-1/2 transform -translate-y-1/2 text-xl text-gray-500 hover:text-black"
              >
                <FaTimes />
              </button>
            </motion.div>
          </>
        )}
      </AnimatePresence>

      <AnimatePresence>
        {menuOpen && (
          <motion.nav
            initial={{ scaleY: 0, opacity: 0 }}
            animate={{ scaleY: 1, opacity: 1 }}
            exit={{ scaleY: 0, opacity: 0 }}
            className="lg:hidden bg-white origin-top"
          >
            <ul>
              <li className="border-b border-gray-700">
                <Link to="/" className="block px-4 py-3 text-black">
                  Trang chủ
                </Link>
              </li>
              <li className="border-b border-gray-700">
                <Link to="/" className="block px-4 py-3 text-black">
                  Giới thiệu
                </Link>
              </li>
              <li className="border-b border-gray-700">
                <button
                  className="w-full text-left px-4 py-3 flex justify-between items-center text-black"
                  onClick={() => setNewsDropdownOpen(!newsDropdownOpen)}
                >
                  Sản phẩm <span>{newsDropdownOpen ? "−" : "+"}</span>
                </button>
                <AnimatePresence>
                  {newsDropdownOpen && (
                    <motion.div
                      initial={{ height: 0 }}
                      animate={{ height: "auto" }}
                      exit={{ height: 0 }}
                    >
                      <Link
                        to="/tin-chinh-tri"
                        className="block px-6 py-2 bg-gray-500 text-white"
                      >
                        Giày Adidas
                      </Link>
                      <Link
                        to="/tin-kinh-te"
                        className="block px-6 py-2 bg-gray-500 text-white"
                      >
                        Giày Nike
                      </Link>
                      <Link
                        to="/tin-giao-duc"
                        className="block px-6 py-2 bg-gray-500 text-white"
                      >
                        Giày Puma
                      </Link>
                      <Link
                        to="/tin-xa-hoi"
                        className="block px-6 py-2 bg-gray-500 text-white"
                      >
                        Tin xã hội
                      </Link>
                    </motion.div>
                  )}
                </AnimatePresence>
              </li>
              <li className="border-b border-gray-700">
                <Link to="/" className="block px-4 py-3 text-black">
                  Tin tức
                </Link>
              </li>
              <li className="border-b border-gray-500">
                <Link to="/contact" className="block px-4 py-3 text-black">
                  Liên hệ
                </Link>
              </li>
            </ul>
          </motion.nav>
        )}
      </AnimatePresence>
    </header>
  );
};

export default Header;
