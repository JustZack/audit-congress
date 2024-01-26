export default class ListUtil {
    static getPropertyFromTerm(dictionary, searchTerm, searchKey, returnKey) {
        searchTerm = searchTerm.toLowerCase();
        for (var i = 0;i < dictionary.length;i++) {
            var item = dictionary[i];
            if (searchTerm == item[searchKey].toLowerCase()) return item[returnKey];
        }
    }
}