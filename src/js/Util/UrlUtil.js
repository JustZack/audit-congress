import Env from "../Env";

export default class UrlUtil {
    //Get part of the URL after the domain
    static getUrlPath() {
        var domain = Env.getDomain();
        var url = window.location.href;
        var path = url.substring(domain.length)
        return path;
    }
    //Get url parts split by back slashes
    static getUrlPathItems() {
        var path = UrlUtil.getUrlPath();
        var items = path.split("/");
        return items;
    }

    static getBillListViewOptions(pathItems) {
        if (pathItems.length >= 1) {
            var page = 1;
            if (pathItems.length > 1) page = parseInt(pathItems[1]);
            return {
                view: "bill-listing", 
                options: {page: page}
            };
        } else return this.getDefaultOptions();
    }

    static getBillViewOptions(pathItems) {
        if (pathItems.length >= 4)
            return {
                view: "bill", 
                options: {congress: pathItems[1], type: pathItems[2], number: pathItems[3]}
            };
        else return this.getDefaultOptions();
    }
    static getMemberViewOptions(pathItems) {
        if (pathItems.length >= 2)
            return {
                view: "member", 
                options: {id: pathItems[1]}
            };
        else return this.getDefaultOptions();
    }
    static getDefaultOptions() {
        return {
            view: "default", 
            options: {}
        };
    }

    static getViewOptionsObject(view, pathItems) {
        switch (view) {
            case "bill-listing": return UrlUtil.getBillListViewOptions(pathItems);
            case "bill": return UrlUtil.getBillViewOptions(pathItems);
            case "member": return UrlUtil.getMemberViewOptions(pathItems);
            default: return UrlUtil.getDefaultOptions()
        }
    }

    static views = ["bill", "member", "bill-listing"]
    //Determine the current view based on path
    static getViewOptions() {
        var pathItems = UrlUtil.getUrlPathItems();
        var viewOptions = {view: "default", options: {}}
        if (pathItems.length > 0) {
            var view = pathItems[0];
            viewOptions = UrlUtil.getViewOptionsObject(view, pathItems);
        }
        return viewOptions;
    }

    static setWindowUrl(windowTitle, path) {
        window.history.pushState("", windowTitle, path);
    }
}
