export default class DateUtil {
    static weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    static months = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];

    static pluralizeNumber(number) {
        var relevent = number%100;
        var tens = relevent%10;
        var suffix = "";
    
        if (tens == 0 || (relevent >= 4 && relevent <= 20)) 
                            suffix = "th";
        else if (tens == 1) suffix = "st";
        else if (tens == 2) suffix = "nd";
        else if (tens == 3) suffix = "rd";
        number += suffix;
        return number;
    }
    
    static buildLocaleDateTimeString(datetimestring) {
        var dt = new Date(datetimestring);
        //EX:            Friday Dec.
        var localTime = `${DateUtil.weekdays[dt.getDay()]} ${DateUtil.months[dt.getMonth()]}`;
        //EX:          4th
        localTime += ` ${DateUtil.pluralizeNumber(dt.getDate())} `;
        //EX:         11:30 PM
        localTime += dt.toLocaleTimeString('en-us', { hour: 'numeric', minute: 'numeric', hour12: true });
        //EX:         2022
        localTime += `  ${dt.getFullYear()}`;
        return localTime;
    }

    static buildSimpleDateString(dateString) {
        var dt = new Date(dateString);
        //EX:            Friday Dec.
        var localDate = `${DateUtil.months[dt.getMonth()]} `;
        //EX:          4th
        localDate += `${DateUtil.pluralizeNumber(dt.getDate())} `;
        //EX:         2022
        localDate += `${dt.getFullYear()}`;
        return localDate;
    }
}