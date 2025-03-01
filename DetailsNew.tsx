import React, { useState } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import "../assets/css/newDetail.css"
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "../assets/css/news.css"; // Đường dẫn tới file CSS tùy chỉnh
import "bootstrap-icons/font/bootstrap-icons.css"; // Import Bootstrap Icons


const categories = [
  "Tin khuyến mãi",
  "Tin khuyến mãi",
  "Tin khuyến mãi",
  "Tin khuyến mãi",
  "Tin khuyến mãi",
];

const recentPosts = [
  {
    img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
    title: "Lorem ipsum dolor sit amet consectetur adipiscing",
    date: "25/02/2020",
  },
  {
    img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
    title: "Lorem ipsum dolor sit amet consectetur adipiscing",
    date: "25/02/2020",
  },
  {
    img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
    title: "Lorem ipsum dolor sit amet consectetur adipiscing",
    date: "25/02/2020",
  },
];
const keywords = [
    "Thể thao", "Xu hướng", "Trang sức", "Man", "Giày", "Giày thể thao", "Sport"
  ];

  

  const articles = [
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },
 
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },

    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },

    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },

    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },

    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },

    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description:
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },
  ];

  const relatedPosts = [
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    },
    {
      img: "https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400",
      title: "Lorem Ipsum dolor sit amet consectetur adipiscing",
      description: "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been...",
    }
  ];
const DetailsNew = () => {
    const [comment, setComment] = useState({
        name: "",
        email: "",
        phone: "",
        message: "",
      });
    
      const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        setComment({ ...comment, [e.target.name]: e.target.value });
      };
      
      const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        console.log("Bình luận đã gửi:", comment);
        alert("Bình luận của bạn đã được gửi!");
        setComment({ name: "", email: "", phone: "", message: "" });
      };
      
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 3;
  
    // Tính tổng số trang
    const totalPages = Math.ceil(articles.length / itemsPerPage);
  
    // Chia danh sách bài viết theo trang
    const indexOfLastItem = currentPage * itemsPerPage;
    const indexOfFirstItem = indexOfLastItem - itemsPerPage;
    const currentArticles = articles.slice(indexOfFirstItem, indexOfLastItem);
  
    // Chuyển trang
    
  return (
   <div>

<div className="container mt-5">
      <div className="row">
        {/* Cột bên trái - Danh mục Tin Tức */}
        <div className="col-md-3">
          <div className="category-box p-3 rounded shadow-sm">
            <h5 className="fw-bold">THƯ MỤC</h5>
            <div className="category-line"></div>
            <ul className="list-group">
              {categories.map((category, index) => (
                <li key={index} className="list-group-item d-flex justify-content-between align-items-center">
                  {category}
                  <button className="btn btn-outline-secondary btn-sm">
                    <i className="bi bi-plus"></i>
                  </button>
                </li>
              ))}
            </ul>
          </div>

          {/* Bài đăng gần đây */}
          <div className="recent-posts mt-4 p-3 rounded shadow-sm">
            <h5 className="fw-bold">BÀI ĐĂNG GẦN ĐÂY</h5>
            <div className="category-line"></div>
            {recentPosts.map((post, index) => (
              <div key={index} className="d-flex align-items-start mb-3">
                <img src={post.img} className="img-thumbnail me-3" alt="Post" width="70" />
                <div>
                  <p className="mb-1 post-title">{post.title}</p>
                  <p className="text-muted mb-0">
                    <i className="bi bi-calendar"></i> {post.date}
                  </p>
                </div>
              </div>
            ))}
          </div>

          <div className="keywords mt-4 p-3 rounded shadow-sm">
            <h5 className="fw-bold">TỪ KHÓA</h5>
            <div className="category-line"></div>
            <div className="d-flex flex-wrap">
              {keywords.map((keyword, index) => (
                <span key={index} className="badge bg-light text-dark keyword-badge">{keyword}</span>
              ))}
            </div>
          </div>
        </div>

        {/* Cột bên phải - Danh sách bài viết */}
        <div className="col-md-9">
            <div className="product-detail">
              {/* Ảnh lớn sản phẩm */}
              <img
                src="https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400"
                alt="Product"
                className="img-fluid main-product-image"
              />

              {/* Tiêu đề sản phẩm */}
              <h2 className="product-title mt-3">
                Lorem ipsum dolor sit amet consectetur adipiscing
              </h2>

              {/* Nội dung mô tả */}
              <p className="product-description">
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
                has been the industry's standard dummy industry's standard. It has been used since the
                1500s, making it one of the oldest known dummy texts in existence. This example product
                represents a modern take on traditional shoe design with high-tech materials and
                innovative manufacturing.
              </p>

              <p>
                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
                has been the industry's standard dummy text. Proin tincidunt, ipsum nec vehicula euismod,
                neque nibh pretium ipsum, et morbi risus sem et risus. Curabitur pellentesque id.
              </p>

              {/* Hình ảnh bổ sung */}
              <div className="additional-images mt-4">
                <img
                  src="https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400"
                  alt="Product detail"
                  className="img-fluid me-2"
                />
                <img
                  src="https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg?auto=compress&cs=tinysrgb&w=400"
                  alt="Product detail"
                  className="img-fluid"
                />
              </div>
            </div>
            <div className="related-posts mt-5 mb-3">
                <h4 className="fw-bold mb-4">Bài đăng tương tự</h4>
                <div className="row">
                  {relatedPosts.map((post, index) => (
                    <div key={index} className="col-md-6">
                      <div className="card">
                        <img src={post.img} className="card-img-top" alt={post.title} />
                        <div className="card-body">
                          <h6 className="card-title fw-bold">{post.title}</h6>
                          <p className="card-text">{post.description}</p>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
                </div>


                <div className="comment-form mt-3 mb-3">
                <h4 className="fw-bold">Để lại bình luận</h4>
                <form onSubmit={handleSubmit}>
                  <div className="row">
                    <div className="col-md-4">
                      <input
                        type="text"
                        name="name"
                        value={comment.name}
                        onChange={handleChange}
                        className="form-control"
                        placeholder="Tên của bạn (*)"
                        required
                      />
                    </div>
                    <div className="col-md-4">
                      <input
                        type="email"
                        name="email"
                        value={comment.email}
                        onChange={handleChange}
                        className="form-control"
                        placeholder="Email của bạn (*)"
                        required
                      />
                    </div>
                    <div className="col-md-4">
                      <input
                        type="text"
                        name="phone"
                        value={comment.phone}
                        onChange={handleChange}
                        className="form-control"
                        placeholder="Điện thoại (*)"
                        required
                      />
                    </div>
                  </div>
                  <div className="mt-3">
                    <textarea
                      name="message"
                      value={comment.message}
                      onChange={handleChange}
                      className="form-control"
                      rows={5}
                      placeholder="Gõ bình luận..."
                      required
                    ></textarea>
                  </div>
                  <button type="submit" className="btn btn-warning mt-3">GỬI BÌNH LUẬN</button>
                </form>
              </div>
          </div>

         
        </div>
    </div>
  
   </div>
  );
};

export default DetailsNew;
