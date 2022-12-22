import APICallingComponent from "./APICallingComponent.js";

export default class BillInfo extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { isset: false };

    this.handleBillData = this.handleBillData.bind(this);
    this.handleBackClick = this.handleBackClick.bind(this);
    this.componentDidMount = this.componentDidMount.bind(this);
  }
  
  componentDidMount = () => {
    var bill = this.props.bill;
    this.APIFetch("bill", {congress: bill.congress, type: bill.type, number: bill.number}, this.handleBillData);
  };

  handleBillData = (json) => {
    var billObj = json.bill;
    this.setState({
      bill: billObj,
      isset: true
    });
  }
  
  handleBackClick = () => {
    this.props.setView("bill-listing");
  }
  
  render() {
    console.log(this.state.bill);
    if (this.state.isset) {
      return (
        <div className="detailed-view">
          <h1>{this.state.bill.title}</h1>
          <h1>{this.state.bill.updateDate}</h1>
          <button onClick={this.handleBackClick}>Back To Listing</button>
        </div>
      );
    } else {
      return (
        <div className="detailed-view">
          <h1>Loading Data...</h1>
          <button onClick={this.handleBackClick}>Back To Listing</button>
        </div>
      );
    }
  }
}