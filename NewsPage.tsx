import React, { useState } from "react";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.bundle.min.js";
import "../assets/css/news.css"; // Custom CSS file
import "bootstrap-icons/font/bootstrap-icons.css"; // Bootstrap Icons


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

const NewsPage = () => {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 3;

  // Calculate total number of pages
  const totalPages = Math.ceil(articles.length / itemsPerPage);

  // Slice the articles based on the current page
  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentArticles = articles.slice(indexOfFirstItem, indexOfLastItem);

  // Change page
  const goToPage = (page: number) => {
    if (page >= 1 && page <= totalPages) {
      setCurrentPage(page);
    }
  };
  

  return (
    <div>
      
      <div className="container mt-5">
        <div className="row">
          {/* Left column - Categories */}
          <div className="col-md-3">
            <div className="category-box p-3 rounded shadow-sm">
              <h5 className="fw-bold">THƯ MỤC</h5>
              <div className="category-line"></div>
              <ul className="list-group">
                {categories.map((category, index) => (
                  <li
                    key={index}
                    className="list-group-item d-flex justify-content-between align-items-center"
                  >
                    {category}
                    <button className="btn btn-outline-secondary btn-sm">
                      <i className="bi bi-plus"></i>
                    </button>
                  </li>
                ))}
              </ul>
            </div>

            {/* Recent Posts */}
            <div className="recent-posts mt-4 p-3 rounded shadow-sm">
              <h5 className="fw-bold">BÀI ĐĂNG GẦN ĐÂY</h5>
              <div className="category-line"></div>
              {recentPosts.map((post, index) => (
                <div key={index} className="d-flex align-items-start mb-3">
                  <img
                    src={post.img}
                    className="img-thumbnail me-3"
                    alt="Post"
                    width="70"
                  />
                  <div>
                    <p className="mb-1 post-title">{post.title}</p>
                    <p className="text-muted mb-0">
                      <i className="bi bi-calendar"></i> {post.date}
                    </p>
                  </div>
                </div>
              ))}
            </div>

            {/* Keywords */}
            <div className="keywords mt-4 p-3 rounded shadow-sm">
              <h5 className="fw-bold">TỪ KHÓA</h5>
              <div className="category-line"></div>
              <div className="d-flex flex-wrap">
                {keywords.map((keyword, index) => (
                  <span
                    key={index}
                    className="badge bg-light text-dark keyword-badge"
                  >
                    {keyword}
                  </span>
                ))}
              </div>
            </div>
          </div>

          {/* Right column - Articles List */}
          <div className="col-md-9">
            <div className="row">
              {currentArticles.map((article, index) => (
                <div key={index} className="col-md-4 mb-2">
                  <div className="card news-card">
                    <img
                      src={article.img}
                      className="card-img-top"
                      alt="News"
                    />
                    <div className="card-body">
                      <h6 className="card-title fw-bold">{article.title}</h6>
                      <p className="card-text">{article.description}</p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Pagination */}
        <div className="pagination-container">
          <ul className="pagination justify-content-end">
            <li className={`page-item ${currentPage === 1 ? "disabled" : ""}`}>
              <button
                className="page-link"
                onClick={() => goToPage(currentPage - 1)}
                aria-label="Previous page"
              >
                Trước
              </button>
            </li>
            {[...Array(totalPages)].map((_, index) => (
              <li
                key={index}
                className={`page-item ${currentPage === index + 1 ? "active" : ""}`}
              >
                <button
                  className="page-link"
                  onClick={() => goToPage(index + 1)}
                >
                  {index + 1}
                </button>
              </li>
            ))}
            <li
              className={`page-item ${currentPage === totalPages ? "disabled" : ""}`}
            >
              <button
                className="page-link"
                onClick={() => goToPage(currentPage + 1)}
                aria-label="Next page"
              >
                Kế tiếp
              </button>
            </li>
          </ul>
        </div>
      </div>

    </div>
  );
};

export default NewsPage;
