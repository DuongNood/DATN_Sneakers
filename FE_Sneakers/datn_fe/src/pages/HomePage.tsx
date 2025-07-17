import Banner from "../components/Banner";
import Legit from "../components/Letgit";
import TitleWithEffect from "../components/TitleProduct";
import ProductList from "../pages/ProductList";
import TitleWithEffect1 from "../components/TitleProduct1";
import ProductHot from "../pages/ProductHot";
import FakeBanner from "../components/FakeBanner";
import CouponPopup from "../components/CouponPopup";
import PurchaseNotification from "../components/PurchaseNotification";

const HomePage = () => {
    return (
        <>
            <Banner />
            <Legit />
            <TitleWithEffect />
            <ProductList />
            <TitleWithEffect1 />
            <ProductHot />
            <FakeBanner />
            <CouponPopup />
            <PurchaseNotification />
        </>
    );
};

export default HomePage;
