import { Outlet } from "react-router-dom";
import Navbar from "../components/TopHeader";
import Header from "../components/Header";
import ScrollToTop from "../components/ScrollToTop";
import Footer from "../components/Footer";
import BackToTop from "../components/BackToTop";

const MainLayout = () => {
    return (
        <>
            <Navbar />
            <Header />
            <ScrollToTop />
            <Outlet />
            <Footer />
            <BackToTop />
        </>
    );
};

export default MainLayout;
