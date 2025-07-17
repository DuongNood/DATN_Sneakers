import React from "react";
import { useForm, SubmitHandler } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { toast } from "react-toastify";
import { useTranslation } from "react-i18next";

const schema = yup.object().shape({
    name: yup.string().required("name_required"),
    email: yup.string().email("email_invalid").required("email_required"),
    phone: yup.string().required("phone_required"),
    message: yup.string().required("message_required"),
});

type FormValues = {
    name: string;
    email: string;
    phone: string;
    message: string;
};

const ContactPage: React.FC = () => {
    const { t } = useTranslation();
    const navigate = useNavigate();
    const {
        register,
        handleSubmit,
        formState: { errors },
    } = useForm<FormValues>({
        resolver: yupResolver(schema),
    });

    const onSubmit: SubmitHandler<FormValues> = (data) => {
        const formData = new URLSearchParams();
        formData.append("entry.323050142", data.name);
        formData.append("entry.1296660199", data.email);
        formData.append("entry.1344814193", data.phone);
        formData.append("entry.162458856", data.message);

        fetch(
            "https://docs.google.com/forms/d/e/1FAIpQLSfHhg7NKSJvLnyiLjprM2uAEMtgDrZOGan0oTpm8bHGHNGYTw/formResponse",
            {
                method: "POST",
                body: formData,
                mode: "no-cors",
            }
        )
            .then(() => {
                toast.success(t("message_sent_success"), {
                    position: "top-right",
                });
                navigate("/");
            })
            .catch((error) => {
                console.error("Error sending data:", error);
            });
    };

    return (
        <motion.div
            className="container mx-auto p-4 md:p-20 shadow-md mt-5 mb-5"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.5 }}
        >
            <h1 className="text-4xl font-bold mb-8 text-center">
                {t("contact")}
            </h1>

            <div className="flex flex-col lg:flex-row gap-8">
                <motion.div
                    className="w-full lg:w-1/2"
                    initial={{ x: -100 }}
                    animate={{ x: 0 }}
                    transition={{ duration: 0.5 }}
                >
                    <form
                        className="space-y-6"
                        onSubmit={handleSubmit(onSubmit)}
                    >
                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                {t("name")}
                            </label>
                            <input
                                type="text"
                                className={`mt-1 block w-full px-4 py-2 border ${
                                    errors.name
                                        ? "border-red-500"
                                        : "border-gray-300"
                                } rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500`}
                                placeholder={t("name_placeholder")}
                                {...register("name")}
                            />
                            {errors.name && (
                                <p className="mt-1 text-red-500 text-sm">
                                    {t(errors.name.message)}
                                </p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                {t("email")}
                            </label>
                            <input
                                type="email"
                                className={`mt-1 block w-full px-4 py-2 border ${
                                    errors.email
                                        ? "border-red-500"
                                        : "border-gray-300"
                                } rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500`}
                                placeholder={t("email_placeholder")}
                                {...register("email")}
                            />
                            {errors.email && (
                                <p className="mt-1 text-red-500 text-sm">
                                    {t(errors.email.message)}
                                </p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                {t("phone")}
                            </label>
                            <input
                                type="tel"
                                className={`mt-1 block w-full px-4 py-2 border ${
                                    errors.phone
                                        ? "border-red-500"
                                        : "border-gray-300"
                                } rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500`}
                                placeholder={t("phone_placeholder")}
                                {...register("phone")}
                            />
                            {errors.phone && (
                                <p className="mt-1 text-red-500 text-sm">
                                    {t(errors.phone.message)}
                                </p>
                            )}
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-gray-700">
                                {t("message")}
                            </label>
                            <textarea
                                className={`mt-1 block w-full px-4 py-2 border ${
                                    errors.message
                                        ? "border-red-500"
                                        : "border-gray-300"
                                } rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500`}
                                placeholder={t("message_placeholder")}
                                {...register("message")}
                            ></textarea>
                            {errors.message && (
                                <p className="mt-1 text-red-500 text-sm">
                                    {t(errors.message.message)}
                                </p>
                            )}
                        </div>

                        <div>
                            <button
                                type="submit"
                                className="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                {t("send_message")}
                            </button>
                        </div>
                    </form>
                </motion.div>

                <motion.div
                    className="w-full lg:w-1/2"
                    initial={{ x: 100 }}
                    animate={{ x: 0 }}
                    transition={{ duration: 0.5 }}
                >
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7447.727851395522!2d105.747262!3d21.03813!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455e940879933%3A0xcf10b34e9f1a03df!2zVHLGsOG7nW5nIENhzyDEkeG6s25nIEZQVCBQb2x5dGVjaG5pYw!5e0!3m2!1sen!2sus!4v1722291695252!5m2!1sen!2sus"
                        width="100%"
                        height="450"
                        style={{ border: 0 }}
                        allowFullScreen=""
                        loading="lazy"
                    ></iframe>
                </motion.div>
            </div>
        </motion.div>
    );
};

export default ContactPage;
