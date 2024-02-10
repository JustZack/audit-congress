export default class NumberUtil {
    static pluralizeMapping = ["th","st","nd","rd"];
    
    static pluralizeNumber(number) {
        var relevent = number%100, tens = relevent%10;
        var suffix = "", isTeens = relevent >= 4 && relevent <= 20;

        if (tens == 0 || isTeens) suffix = NumberUtil.pluralizeMapping[0];
        else                      suffix = NumberUtil.pluralizeMapping[tens];

        suffix = number + suffix;
        return suffix;
    }
}