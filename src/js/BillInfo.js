import APICallingComponent from "./APICallingComponent.js";
import DateUtil from "./DateUtil.js";
import Chamber from "./BillItems/Chamber.js"
import Congress from "./BillItems/Congress.js";
import CoSponsors from "./BillItems/CoSponsors.js";
import PolicyArea from "./BillItems/PolicyArea.js";
import Actions from "./BillItems/Actions.js";
import Committees from "./BillItems/Committees.js";
import Titles from "./BillItems/Titles.js";
import TextVersions from "./BillItems/TextVersions.js";

export default class BillInfo extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };

    this.handleBillData = this.handleBillData.bind(this);
    this.handleBackClick = this.handleBackClick.bind(this);
    this.componentDidMount = this.componentDidMount.bind(this);
    this.getJSX = this.getJSX.bind(this);
  }
  
  componentDidMount = () => {
    var bill = this.props.bill;
    bill.type = `${bill.type}`.toLowerCase();
    this.APIFetch("fullBill", {congress: bill.congress, type: bill.type, number: bill.number}, this.handleBillData);
  };

  handleBillData = (json) => {
    var billObj = json.bill;
    this.setState({
      bill: billObj,
      updated: DateUtil.buildLocaleDateTimeString(billObj.updateDateIncludingText),
      jsx: this.getJSX(billObj),
      isset: true
    });
  }

  getJSX = (bill) => {
    return (
      <div>
        <PolicyArea policyArea={bill.policyArea.name}/>
        <Congress congress={bill.congress}/>
        <Chamber chamber={bill.originChamber}/>

        <TextVersions textVersions={bill.textVersions}/>

        <CoSponsors cosponsors={bill.cosponsors}/>
        <Actions actions={bill.actions}/>
        <Committees committees={bill.committees}/>
        <Titles titles={bill.titles}/>
      </div>
    )
  }
  
  handleBackClick = () => {
    this.props.setView("bill-listing");
  }
  
  render() {
    console.log(this.state.bill);
    if (this.state.isset) {
      return (
        <div className="detailed-view">
          <button onClick={this.handleBackClick}>Back To Listing</button>   
          
          <h1>{this.state.bill.title}</h1>
          <h1>{this.state.updated}</h1>

          {this.state.jsx}      
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