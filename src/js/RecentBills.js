import APICallingComponent from "./APICallingComponent.js";
import BillListItem from "./BillListItem.js";
import "../css/App.scss"
import "../css/RecentBills.scss"

export default class RecentBills extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = {
      isset: false
    };

    this.handleRecentBills = this.handleRecentBills.bind(this);
    this.componentDidMount = this.componentDidMount.bind(this);
  }
  
  componentDidMount = () => {
    this.APIFetch("recentBills", {}, this.handleRecentBills)
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
    if (this.state.isset) {
      return (
        <ul className="list-view">
          {this.state.jsx}
        </ul>
      );
    } else {
      return (
        <div>Loading Data...</div>
      );
    }
  }
}