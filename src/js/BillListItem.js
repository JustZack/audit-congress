import BillStatus from "./BillStatus.js";
import DateUtil from "./DateUtil.js";

export default class BillListItem extends React.Component {
  constructor(props) {
    super(props);
    var bill = this.props.bill;
    this.state = {
      updated: DateUtil.buildLocaleDateTimeString(bill.updateDateIncludingText)
    };

    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);    
  }
  
  handleBillInfoClick = () => {
    this.props.setView("bill-detail", this.props.bill);
  }

  render() {
    
    var bill = this.props.bill;
    return (
      <li>
        <strong onClick={this.handleBillInfoClick} className={'li-bill-title link'}>
          {bill.id} — {bill.congressTitle}
        </strong>
        <div className={'li-bill-details'}>
          <strong className={'fullwidth'}>{bill.title}</strong>
          <span className={'fullwidth'}>Last Updated: {this.state.updated}</span>
          <BillStatus action={bill.latestAction}/>
        </div>
      </li>
    );
  }
}