import { Link } from "react-router-dom";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";

const Footer = () => {
    const { t } = useTranslation();

    const handleSubmit = (event) => {
        event.preventDefault();
        const email = event.target.elements.email.value;

        const formData = new URLSearchParams();
        formData.append("entry.873095472", email);

        fetch(
            "https://docs.google.com/forms/d/e/1FAIpQLScPO7qu7vfekGcxPL2J3hgwU7XB3QQIfKW7y0hj0rPBbzG2Cw/formResponse",
            {
                method: "POST",
                body: formData,
                mode: "no-cors",
            }
        )
            .then(() => {
                toast.success(t("subscription_success"), {
                    autoClose: 1000,
                });
            })
            .catch((error) => {
                console.error("Error sending data:", error);
            });

        event.target.reset();
    };

    return (
        <footer className="bg-gray-900 text-gray-300 py-12 mt-12">
            <div className="container mx-auto px-6 md:px-12">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <h2 className="text-2xl font-bold text-white">
                            {t("footer_brand")}
                        </h2>
                        <p className="mt-3 text-sm">
                            {t("footer_brand_description")}
                        </p>
                    </div>

                    <div>
                        <h3 className="text-lg font-semibold text-white mb-4">
                            {t("about_us")}
                        </h3>
                        <ul className="space-y-3">
                            <li>
                                <Link
                                    to="/about"
                                    className="hover:text-white transition"
                                >
                                    {t("about")}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    to="/contact"
                                    className="hover:text-white transition"
                                >
                                    {t("contact")}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    to="/privacy-policy"
                                    className="hover:text-white transition"
                                >
                                    {t("privacy_policy")}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 className="text-lg font-semibold text-white mb-4">
                            {t("support")}
                        </h3>
                        <ul className="space-y-3">
                            <li>
                                <Link
                                    to="/faq"
                                    className="hover:text-white transition"
                                >
                                    {t("faq")}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    to="/return-policy"
                                    className="hover:text-white transition"
                                >
                                    {t("return_policy")}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    to="/how-to-buy"
                                    className="hover:text-white transition"
                                >
                                    {t("how_to_buy")}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h3 className="text-lg font-semibold text-white mb-4">
                            {t("subscribe")}
                        </h3>
                        <p className="text-sm mb-3">
                            {t("subscribe_description")}
                        </p>
                        <div className="flex">
                            <form className="flex" onSubmit={handleSubmit}>
                                <input
                                    type="email"
                                    name="email"
                                    required
                                    placeholder={t("email_placeholder")}
                                    className="w-full p-2 text-gray-900 rounded-l-md focus:outline-none"
                                />
                                <button
                                    type="submit"
                                    className="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-r-md transition"
                                >
                                    {t("submit")}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div className="border-t border-gray-700 my-6"></div>

                <div className="flex flex-col md:flex-row items-center justify-between">
                    <p className="text-sm">
                        {t("copyright", { year: new Date().getFullYear() })}
                    </p>
                    <div className="flex space-x-4 mt-4 md:mt-0">
                        <a
                            href="https://www.facebook.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="hover:text-white transition"
                        >
                            {t("facebook")}
                        </a>
                        <a
                            href="https://www.instagram.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="hover:text-white transition"
                        >
                            {t("instagram")}
                        </a>
                        <a
                            href="https://www.twitter.com"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="hover:text-white transition"
                        >
                            {t("twitter")}
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default Footer;
