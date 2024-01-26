//import React, { PureComponent } from "react";
import UrlUtil from "./Util/UrlUtil.js";
import Bill from "./Views/Bill.js";
import Member from "./Views/Member.js";
import BillListing from "./Views/BillListing.js";
import Header from "./Header.js";
import "../css/App.scss"

export default class App extends React.Component {
  constructor(props) {
    super(props);

    this.setView = this.setView.bind(this);
    this.determineViewJSX = this.determineViewJSX.bind(this);
  
    this.state = {
      options: null,
      jsx: this.determineViewJSX(UrlUtil.getViewOptions()),
      lastBillOpts: null,
      lastMemberOpts: null
    };

  }

  setView(viewType, viewOptions) {
    var viewOptions = {view: viewType, options: viewOptions};
    this.setState({
      view: viewType,
      options: viewOptions,
      jsx: this.determineViewJSX(viewOptions)
    });
  }

  determineViewJSX(viewOptions) {
    var jsx = []; 
    var view = viewOptions.view;
    var options = viewOptions.options;
    switch (view) {
      case "bill-listing": 
        jsx.push(<BillListing key="billListing" setView={this.setView} options={options}/>);
        break;
      case "bill": 
        jsx.push(<Bill key="billInfo" setView={this.setView} bill={options}/>);
        break;
      case "member": 
        jsx.push(<Member key="memberInfo" setView={this.setView} member={options}/>);
        break;
      //Use this as home for now
      default: 
        jsx.push(<BillListing key="billListing" setView={this.setView}/>);
        break;
    }
    return jsx;
  }

  determineViewJSXOld(view, options) {
    var jsx = []; var options = options;
    switch (view) {
      case "bill-listing": 
        jsx.push(<BillListing key="billListing" setView={this.setView} options={options}/>);
        break;
      case "bill": 
        jsx.push(<Bill key="billInfo" setView={this.setView} bill={options}/>);
        break;
      case "member": 
        jsx.push(<Member key="memberInfo" setView={this.setView} member={options}/>);
        break;
      //Use this as home for now
      default: 
        jsx.push(<BillListing key="billListing" setView={this.setView}/>);
        break;
    }
    return jsx;
  }

  render() {
    return (
      <div>
        <Header key="header">Audit Congress</Header>
        {this.state.jsx}
      </div>
    );
  }
}
