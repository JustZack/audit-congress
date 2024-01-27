import BillStatus from "./BillStatus.js";
import DateUtil from "../Util/DateUtil.js";
import BillType from "./BillType.js";

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
    this.props.setView("bill", this.props.bill);
  }

  render() {
    var bill = this.props.bill;
    return (
      <li>
        <BillType type={bill.type}/>
        <div onClick={this.handleBillInfoClick} className={'li-bill-title link'}>
          {bill.id} — {bill.congressTitle}
        </div>
        <div className={'li-bill-details'}>
          <div className={'bill-details-title fullwidth'}>{bill.title}</div>
          <div className={'bill-details-update fullwidth'}>Last Update: {this.state.updated}</div>
          <BillStatus action={bill.latestAction}/>
        </div>
      </li>
    );
  }
}