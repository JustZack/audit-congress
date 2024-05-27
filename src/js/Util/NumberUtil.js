export default class NumberUtil {
    static pluralizeMapping = ["th","st","nd","rd"];
    
    static pluralizeNumber(number) {
        var relevent = number%100, tens = relevent%10;
        var suffix = "";
        var isTeens = relevent >= 4 && relevent <= 20;
        var isTh = tens >= 4 || tens == 0;

        if (isTh || isTeens)    suffix = NumberUtil.pluralizeMapping[0];
        else                    suffix = NumberUtil.pluralizeMapping[tens];

        return number + suffix;
    }
}