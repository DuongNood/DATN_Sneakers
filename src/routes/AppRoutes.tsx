import { Routes, Route } from "react-router-dom";
import Home from "../pages/Home";
import Login from "../pages/Login";

import Layout from "../layouts/MainLayout";
import Register from "../pages/Register";
import ForgotPassword from "../pages/ForgotPassword";
import NotFoud from "../pages/NotFoud";

export default function AppRoutes() {
  return (
    <Routes>
      <Route path="/" element={<Layout />}>
        <Route index element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route path="/forgot-password" element={<ForgotPassword />} />
        <Route path="*" element={<NotFoud />} />
      </Route>
    </Routes>
  );
}
