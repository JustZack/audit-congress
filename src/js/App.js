//import React, { PureComponent } from "react";
import BillInfo from "./BillInfo.js";
import Header from "./Header.js";
import RecentBills from "./RecentBills.js";
import "../css/App.scss"

export default class App extends React.Component {
  constructor(props) {
    super(props);

    this.setView = this.setView.bind(this);
    this.determineViewJSX = this.determineViewJSX.bind(this);

    this.state = {
      view: "bill-listing",
      options: null,
      jsx: this.determineViewJSX("bill-listing")
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
      case "bill-listing": jsx.push(<RecentBills key="recentBills" setView={this.setView}/>);
        break;
      case "bill-detail": jsx.push(<BillInfo key="billInfo" setView={this.setView} bill={options}/>);
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