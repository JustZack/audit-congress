import APICallingComponent from "./APICallingComponent.js";

export default class BillInfo extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { isset: false };

    this.APIFetch("bill", {congress: props.congress, type: props.type, number: props.number}, this.handleBillData);

    this.handleBillData = this.handleBillData.bind(this);
    this.handleBackClick = this.handleBackClick.bind(this);
  }

  handleBillData = (json) => {
    var bill = json.bill;
    this.setState({
      congress: bill.congress,
      type: bill.type,
      number: bill.number,
      originChamber: null??bill.originChamber,
      policyArea: bill.policyArea == undefined ? null : bill.policyArea.name,
      title: bill.title,
      updated: null??bill.updateDate,
      lastAction: null??bill.latestAction,
      isset: true
    });
  }

  handleBackClick = () => {
    this.props.setView("bill-listing");
  }

  render() {
    console.log(this.state);
    if (this.state.isset) {
      return (
        <div>
          <h1>{this.state.title}</h1>
          <button onClick={this.handleBackClick}>Back To Listing</button>
        </div>
      );
    } else {
      return (
        <div>
          <h1>Loading Data...</h1>
          <button onClick={this.handleBackClick}>Back To Listing</button>
        </div>
      );
    }
  }
}