import { Outlet } from "react-router-dom";
import Header from "../components/Header";
import Footer from "../components/Footer";
import Banner from "../components/Banner";

export default function Layout() {
  return (
    <div className="flex flex-col min-h-screen">
      <Header />
      <Banner />
      <div className="flex-grow pt-[30px]">
        <Outlet />
      </div>
      <Footer />
    </div>
  );
}
