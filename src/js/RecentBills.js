import APICallingComponent from "./APICallingComponent.js";
import BriefBillListItem from "./BreifBillListItem.js";

export default class RecentBills extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = {
      isset: false
    };

    this.handleRecentBills = this.handleRecentBills.bind(this);
    this.APIFetch("recentBills", {}, this.handleRecentBills)
  }

  handleRecentBills = (json) => {
    this.setState({
      bills: json.bills,
      isset: true
    });
  }
  
  generateBillListing() {
    var bills = this.state.bills; 
    var jsx = [];
    for (var billIndex in bills) {
      var bill = bills[billIndex]; 
      jsx.push(<BriefBillListItem key={bill.number+bill.type} setView={this.props.setView} title={bill.title} congress={bill.congress} type={bill.type} number={bill.number}>
                {bill.title}
              </BriefBillListItem>);
    }
    return jsx;
  }

  render() {
    if (this.state.isset) {
      return (
        <ul>
          {this.generateBillListing()}
        </ul>
      );
    } else {
      return (
        <div>Loading Data...</div>
      );
    }
  }
}