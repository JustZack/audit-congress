import ListUtil from "./ListUtil";

export default class PartyUtil extends ListUtil
 {
    static partyDict = [{"name": "Democrat", "abbreviation": "D", "altName": "Democratic"},
                      {"name": "Republican", "abbreviation": "R", "altName": "Republican"}]
              
    static getPartyNameFromAbbr(abbr) {
        return PartyUtil.getPropertyFromTerm(this.partyDict, abbr, "abbreviation", "name");
    }

    static getPartyNameFromAltName(altName) {
        return PartyUtil.getPropertyFromTerm(this.partyDict, altName, "altName", "name");
    }

    static getPartyAbbrFromName(name) {
        return PartyUtil.getPropertyFromTerm(this.partyDict, name, "altName", "abbreviation");
    }
}
