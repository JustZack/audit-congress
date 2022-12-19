//import React, { PureComponent } from "react";
import BillInfo from "./BillInfo.js";
import Header from "./Header.js";
import RecentBills from "./RecentBills.js";
export default class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      view: "bill-listing",
      options: null
    };

    this.setView = this.setView.bind(this);
  }

  setView(viewType, viewOptions) {
    this.setState({
      view: viewType,
      options: viewOptions,
    });
  }

  render() {
    var jsx = [<Header key="header">Audit Congress</Header>];
    var options = this.state.options;
    switch (this.state.view) {
      case "bill-listing": jsx.push(<RecentBills key="recentBills" setView={this.setView}/>);
        break;
      case "bill-info": jsx.push(<BillInfo key="billInfo" setView={this.setView} congress={options.congress} type={options.type} number={options.number}/>);
        break;
    }

    return (
      <div>
        {jsx}
      </div>
    );
  }
}