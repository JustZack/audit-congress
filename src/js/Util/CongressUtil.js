export default class CongressUtil {
    static getYearsByCongress(congress) {
        var n2 = congress*2;
        //congress# * 2 + 1787 = first year of this congress session
        var years = "("+(n2+1787)+" - ";
        //Then compute the second year
        //This differs because the 72nd congress sessions were 3 years, now they are 2
        if (n2 > 72) years += (n2+1788)+")";
        else years += (n2+1789)+")";
    
        return years;
    }
}