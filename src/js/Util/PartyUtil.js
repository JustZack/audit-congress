export default class PartyUtil
 {
    static parties = [{name: "Democrat", abbreviation: "D", altName: "Democratic"},
                      {name: "Republican", abbreviation: "R", altName: "Republican"}]
    static getPartyNameFromAbbr(abbr) {
        abbr = abbr.toLowerCase();
        for (var i = 0;i < this.parties.length;i++) {
            var party = this.parties[i];
            if (abbr == party.abbr.toLowerCase()) return party.name;
        }
    }

    static getPartyAbbrFromName(name) {
        name = name.toLowerCase();
        for (var i = 0;i < this.parties.length;i++) {
            var party = this.parties[i];
            if (name == party.name.toLowerCase() || name == party.altName.toLowerCase()) return party.abbreviation;
        }
    }
}
