//import React, { PureComponent } from "react";
import BillInfo from "./Views/Bill.js";
import MemberInfo from "./Views/Member.js";
import Header from "./Header.js";
import BillListing from "./Views/BillListing.js";
import "../css/App.scss"

export default class App extends React.Component {
  constructor(props) {
    super(props);

    this.setView = this.setView.bind(this);
    this.determineViewJSX = this.determineViewJSX.bind(this);
    
    this.state = {
      options: null,
      jsx: this.determineViewJSX("bill-listing"),
      lastBillOpts: null,
      lastMemberOpts: null
    };

  }

  setView(viewType, viewOptions) {
    this.setState({
      view: viewType,
      options: viewOptions,
      jsx: this.determineViewJSX(viewType, viewOptions)
    });
  }

  determineViewJSX(view, options) {
    var jsx = []; var options = options;
    switch (view) {
      case "bill-listing": 
        jsx.push(<BillListing key="billListing" setView={this.setView} options={options}/>);
        break;
      case "bill-detail": 
        jsx.push(<BillInfo key="billInfo" setView={this.setView} bill={options}/>);
        break;
      case "member-detail": 
        jsx.push(<MemberInfo key="memberInfo" setView={this.setView} member={options}/>);
        break;
      //Use this as home for now
      default: 
        jsx.push(<RecentBills key="recentBills" setView={this.setView}/>);
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
