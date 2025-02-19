const Footer = () => {
  return (
    <footer className="bg-gray-900 text-white py-12">
      <div className="bg-gray-800 py-8 px-6 rounded-t-lg">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between">
          <div className="text-center md:text-left mb-4 md:mb-0">
            <h3 className="text-2xl font-semibold">Đăng ký nhận tin tức</h3>
            <p className="text-gray-400 mt-1">
              Nhận thông tin khuyến mãi và cập nhật mới nhất!
            </p>
          </div>
          <div className="flex items-center w-full md:w-auto bg-white">
            <input
              type="email"
              placeholder="Nhập email của bạn"
              className="w-full md:w-80 px-4 py-3 rounded-l-lg focus:outline-none text-gray-900"
            />
            <button className="bg-blue-600 hover:bg-blue-500 px-6 py-3  text-white font-semibold">
              Đăng ký
            </button>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 mt-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div>
          <h4 className="text-xl font-semibold mb-4">Về chúng tôi</h4>
          <p className="text-gray-400 leading-relaxed">
            PoleSneaker cung cấp những đôi giày chính hãng với chất lượng hàng
            đầu.
          </p>
        </div>

        <div>
          <h4 className="text-xl font-semibold mb-4">Chính sách</h4>
          <ul className="text-gray-400 space-y-2">
            <li>
              <a href="/" className="hover:text-blue-500">
                Chính sách bảo mật
              </a>
            </li>
            <li>
              <a href="/" className="hover:text-blue-500">
                Chính sách đổi trả
              </a>
            </li>
            <li>
              <a href="/" className="hover:text-blue-500">
                Chính sách vận chuyển
              </a>
            </li>
          </ul>
        </div>

        <div>
          <h4 className="text-xl font-semibold mb-4">Hỗ trợ khách hàng</h4>
          <ul className="text-gray-400 space-y-2">
            <li>
              <a href="/" className="hover:text-blue-500">
                Hướng dẫn mua hàng
              </a>
            </li>
            <li>
              <a href="/" className="hover:text-blue-500">
                Câu hỏi thường gặp
              </a>
            </li>
            <li>
              <a href="/" className="hover:text-blue-500">
                Liên hệ hỗ trợ
              </a>
            </li>
          </ul>
        </div>

        <div>
          <h4 className="text-xl font-semibold mb-4">Liên hệ</h4>
          <p className="text-gray-400 leading-relaxed">
            Địa chỉ: Số 1 Trịnh Văn Bô - Hà Nội
          </p>
          <p className="text-gray-400 leading-relaxed">
            Điện thoại: 0399926033
          </p>
          <p className="text-gray-400 leading-relaxed">
            Email:hoanganhfullstack@gmail.com
          </p>
        </div>
      </div>

      <div className="mt-12 text-center text-gray-500 border-t border-gray-700 pt-4">
        <p>&copy; 2025 PoleSneaker. Thuộc toàn quyền sở hữu.</p>
      </div>
    </footer>
  );
};

export default Footer;
