//import React, { PureComponent } from "react";
import BillInfo from "./BillInfo.js";
import MouseTracker from "./mouse.js";
export default class App extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      
    };
  }

  handleSearchChange = event => {
    this.setState({
      
    });
  };

  render() {
    return (
      <div>
        <BillInfo>
          <MouseTracker/>
        </BillInfo>
      </div>
    );
  }
}