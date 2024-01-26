import APICallingComponent from "../Api/APICallingComponent.js";
import DateUtil from "../Util/DateUtil.js";
import Chamber from "../Bill/Chamber.js"
import Congress from "../Bill/Congress.js";
import CoSponsors from "../Bill/CoSponsors.js";
import Sponsors from "../Bill/Sponsors.js";
import PolicyArea from "../Bill/PolicyArea.js";
import Actions from "../Bill/Actions.js";
import Committees from "../Bill/Committees.js";
import Titles from "../Bill/Titles.js";
import TextVersions from "../Bill/TextVersions.js";
import UrlUtil from "../Util/UrlUtil.js";

export default class Bill extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
      jsx: (<h1>Loading Data...</h1>),
      bill: null
    };

    this.handleBillData = this.handleBillData.bind(this);
    this.handleBackClick = this.handleBackClick.bind(this);
    this.componentDidMount = this.componentDidMount.bind(this);
    this.getJSX = this.getJSX.bind(this);
  }
  
  static getPath(congress, type, number) {
    return `bill/${congress}/${type}/${number}`;
  }

  componentDidMount = () => {
    var bill = this.props.bill;
    bill.type = `${bill.type}`.toLowerCase();

    var congress = bill.congress;
    var type = bill.type;
    var number = bill.number;
    var path = Bill.getPath(congress, type, number);
    UrlUtil.setWindowUrl(`Bill ${type} ${number}`, path);

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
        <h1>{bill.title}</h1>
        <h1>{bill.updated}</h1>

        <div>
          <PolicyArea policyArea={bill.policyArea}/>
          <Congress congress={bill.congress}/>
          <Chamber chamber={bill.originChamber}/>

          <TextVersions textVersions={bill.textVersions}/>
          
          <Sponsors sponsors={bill.sponsors} setView={this.props.setView}/>
          <CoSponsors cosponsors={bill.cosponsors} setView={this.props.setView}/>

          <Actions actions={bill.actions}/>
          <Committees committees={bill.committees}/>
          <Titles titles={bill.titles}/>
        </div>
      </div>
    )
  }
  
  handleBackClick = () => {
    this.props.setView("bill-listing");
  }
  
  render() {
    console.log(this.state.bill);
    return (
      <div className="detailed-view">
        <button onClick={this.handleBackClick}>Back To Listing</button>   

        {this.state.jsx}      
      </div>
    );
  }
}