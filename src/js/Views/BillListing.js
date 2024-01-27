import APICallingComponent from "../Api/APICallingComponent.js";
import BillListItem from "../BillListing/BillListItem.js";
import UrlUtil from "../Util/UrlUtil.js";

export default class BillListing extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = {
      isset: false,
      jsx: (<h1>Loading Data...</h1>)
    };

    this.handleRecentBills = this.handleRecentBills.bind(this);
    this.componentDidMount = this.componentDidMount.bind(this);
  }
  
  componentDidMount = () => {
    UrlUtil.setWindowUrl(`Bill Listing`, "bill-listing");
    this.APIFetch("recentBills", {page: 1}, this.handleRecentBills)
  }

  handleRecentBills = (json) => {
    this.setState({
      jsx: this.generateBillListing(json.bills),
      isset: true
    });
  }

  generateBillListing(bills) {
    var jsx = [];
    for (var billIndex in bills) {
      var billObj = bills[billIndex]; 
      var billKey = billObj.number+billObj.type;
      jsx.push(<BillListItem key={billKey} setView={this.props.setView} bill={billObj}/>);
    }
    return jsx;
  }

  render() {
    return (
      <ul id="bill-listing" className="list-view">
        {this.state.jsx}
      </ul>
    );
  }
}