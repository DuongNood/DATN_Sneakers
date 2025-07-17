import { t } from "i18next";
import { AuroraText } from "./magicui/aurora-text";

const TitleWithEffect = () => {
    return (
        <div className="container mx-auto px-4 sm:px-8 md:px-16">
            <div className=" flex items-center my-12">
                <p className="text-3xl sm:text-2xl md:text-3xl lg:text-4xl xl:text-2xl font-bold text-gray-900 max-w-full uppercase">
                    <AuroraText>{t("products_hot")}</AuroraText>
                </p>
      
                <h2 className="text-2xl ml-2 sm:text-2xl md:text-3xl lg:text-4xl xl:text-2xl font-bold text-gray-900 bg-clip-text relative z-20 max-w-full uppercase ">
                    {t("in_week")}
                </h2>
            </div>
        </div>
    );
};

export default TitleWithEffect;
