import { useState, useEffect } from "react";

const buyerNames = [
    "Nguyễn Thanh Thu",
    "Hoàng Long Trần",
    "Lê Vạn Tuế",
    "Trần Thái Công",
    "Phạm Kim Ngân",
    "Lê Văn Dũng",
    "Nguyễn Hữu Tài",
    "Trần Ngọc Diệp",
    "Đặng Quang Huy",
    "Phạm Hồng Anh",
    "Nguyễn Văn Minh",
    "Vũ Thị Hà",
    "Lê Trọng Phú",
    "Nguyễn Thị Mai",
    "Trần Hoàng Nam",
    "Bùi Khánh Linh",
    "Đỗ Tiến Đạt",
    "Phạm Văn Kiên",
    "Võ Quỳnh Như",
    "Trịnh Quốc Toản",
];

const products = [
    {
        name: "Giày ADIDAS COURT CLASSIC",
        code: "JH5061",
        imageUrl:
            "https://media-cdn-v2.laodong.vn/Storage/NewsPortal/2022/3/6/1020690/Kim-Seon-Ho-4.jpg",
    },
    {
        name: "Giày Nike Air Max 270",
        code: "AH8050-005",
        imageUrl:
            "https://www.elle.vn/wp-content/uploads/2021/10/19/453810/dien-vien-kim-yoo-jung.jpg",
    },
    {
        name: "Giày Puma RS-X",
        code: "FY0378",
        imageUrl:
            "https://giadinh.mediacdn.vn/2018/3/2/photo-0-15199918686881712124565.jpg",
    },
    {
        name: "Giày Puma RS-X3",
        code: "374665-01",
        imageUrl:
            "https://i-giaitri.vnecdn.net/2020/05/17/IMG-5119-JPG-9737-1589707304.jpg",
    },
    {
        name: "Giày Adidog Classic",
        code: "M7650C",
        imageUrl:
            "https://nld.mediacdn.vn/291774122806476800/2022/10/22/img-7815-16664220616861104673336-16664225662431312567739.jpg",
    },
];

interface Notification {
    id: number;
    buyerName: string;
    productName: string;
    productCode: string;
    imageUrl: string;
    timeAgo: string;
}

const generateRandomNotifications = (): Notification[] => {
    const notifications: Notification[] = [];

    for (let i = 0; i < 20; i++) {
        const buyer = buyerNames[Math.floor(Math.random() * buyerNames.length)];
        const product = products[Math.floor(Math.random() * products.length)];
        const randomMinutes = Math.floor(Math.random() * 60) + 1;

        notifications.push({
            id: i + 1,
            buyerName: buyer,
            productName: product.name,
            productCode: product.code,
            imageUrl: product.imageUrl,
            timeAgo: `${randomMinutes} phút trước`,
        });
    }

    return notifications;
};

const PurchaseNotification = () => {
    const notifications = generateRandomNotifications();
    const [currentIndex, setCurrentIndex] = useState(0);
    const [isVisible, setIsVisible] = useState(true);

    useEffect(() => {
        const interval = setInterval(() => {
            setIsVisible(false);
            setTimeout(() => {
                setCurrentIndex(
                    (prevIndex) => (prevIndex + 1) % notifications.length
                );
                setIsVisible(true);
            }, 500);
        }, 3000);

        return () => clearInterval(interval);
    }, [notifications.length]);

    const handleClose = () => {
        setIsVisible(false);
        setTimeout(() => {
            setCurrentIndex(
                (prevIndex) => (prevIndex + 1) % notifications.length
            );
            setIsVisible(true);
        }, 500);
    };

    const currentNotification = notifications[currentIndex];

    return (
        <div className="fixed bottom-4 left-4 z-50">
            {isVisible && (
                <div className="flex items-center bg-white shadow-lg rounded-lg p-3 max-w-sm animate-slide-up">
                    <img
                        src={currentNotification.imageUrl}
                        alt={currentNotification.productName}
                        className="w-16 h-16 object-cover rounded-md mr-3"
                    />
                    <div className="flex-1">
                        <p className="text-sm font-semibold text-gray-800">
                            {currentNotification.buyerName}
                        </p>
                        <p className="text-xs text-gray-500">Đã mua</p>
                        <p className="text-sm font-semibold text-gray-800">
                            {currentNotification.productName}{" "}
                            {currentNotification.productCode}
                        </p>
                        <p className="text-xs text-gray-500">
                            {currentNotification.timeAgo}
                        </p>
                    </div>

                    <button
                        onClick={handleClose}
                        className="ml-3 text-gray-500 hover:text-gray-700"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            )}
        </div>
    );
};

export default PurchaseNotification;
