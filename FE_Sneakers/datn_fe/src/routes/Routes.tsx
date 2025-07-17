import { Routes, Route } from "react-router-dom";
import MainLayout from "./MainLayout";
import HomePage from "../pages/HomePage";
import Register from "../pages/Register";
import Login from "../pages/Login";
import Cart from "../pages/Cart";
import ContactPage from "../components/contact";
import ProductSale from "../pages/ProductSale";
import NotFound from "../components/NotFound";
import ForgotPassword from "../pages/ForgotPassword";
import ProfilePage from "../components/Profile";
import ChangePasswordPage from "../pages/ChangePasswordPage";
import ProtectedRoute from "../components/ProtectedRoute";
import ProductDetail from "../pages/DetailsProduct";
import SearchResults from "../components/SearchResults";
import AboutUs from "../components/Footer_Components/About";
import Checkout from "../pages/Checkout";
import WarrantyPolicy from "../components/Footer_Components/PrivacyPolicy";
import NewsDetail from "../pages/DetailsNew";
import NewsList from "../pages/New";
import ProductCate from "../components/product_categories/ShoeCategories";
import Payment from "../pages/Payment";
import MomoCallback from "../pages/MomoCallback";
import OrderSuccess from "../components/SuccesOrder";
import OrdersPage from "../pages/OrdersPage";
import Wishlist from "../components/Wishlist";
import ReturnPolicy from "../components/Footer_Components/ReturnPolicy";
import FAQ from "../components/Footer_Components/FAQ";
import HowToBuy from "../components/Footer_Components/HowToBuy";
import ProductByBrand from "../pages/ProductByBrand";

const RoutesConfig = () => {
    return (
        <Routes>
            <Route element={<MainLayout />}>
                <Route path="/" element={<HomePage />} />
                <Route path="/product-sale" element={<ProductSale />} />
                <Route path="/contact" element={<ContactPage />} />
                <Route path="/forgot-password" element={<ForgotPassword />} />
                <Route path="/login" element={<Login />} />
                <Route path="/register" element={<Register />} />
                <Route path="/:slug" element={<ProductDetail />} />
                <Route path="/checkout" element={<Checkout />} />
                <Route path="/news" element={<NewsList />} />
                <Route path="/category/:id" element={<ProductCate />} />
                <Route path="/news/:id" element={<NewsDetail />} />
                <Route path="/cart" element={<Cart />} />
                <Route path="/wishlist" element={<Wishlist />} />
                <Route path="/orders" element={<OrdersPage />} />
                <Route path="/payment" element={<Payment />} />
                <Route
                    path="/productbybrand/:id"
                    element={<ProductByBrand />}
                />
                <Route path="/search" element={<SearchResults />} />
                <Route path="/return-policy" element={<ReturnPolicy />} />
                <Route path="/faq" element={<FAQ />} />
                <Route path="/how-to-buy" element={<HowToBuy />} />
                <Route path="/momo-callback" element={<MomoCallback />} />
                <Route path="/order-success" element={<OrderSuccess />} />
                <Route path="/about" element={<AboutUs />} />
                <Route path="/privacy-policy" element={<WarrantyPolicy />} />
                <Route element={<ProtectedRoute />}>
                    <Route path="/profile" element={<ProfilePage />} />
                    <Route
                        path="/change-password"
                        element={<ChangePasswordPage />}
                    />
                </Route>
            </Route>

            <Route path="*" element={<NotFound />} />
        </Routes>
    );
};

export default RoutesConfig;
