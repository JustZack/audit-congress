import BillStatus from "./BillStatus.js";
import DateUtil from "../Util/DateUtil.js";
import BillType from "./BillType.js";
import CongressUtil from "../Util/CongressUtil.js";
import NumberUtil from "../Util/NumberUtil.js";

export default class BillListItem extends React.Component {
  constructor(props) {
    super(props);
    var bill = this.props.bill;
    this.state = {
      updated: DateUtil.buildLocaleDateTimeString(bill.updated),
      bill: bill
    };

    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);    
    this.getCongressTitle = this.getCongressTitle.bind(this);    
  }
  
  handleBillInfoClick = () => {
    this.props.setView("bill", this.state.bill);
  }

  getCongressTitle() {
    var bill = this.state.bill;
    return `${NumberUtil.pluralizeNumber(bill.congress)} Congress ${CongressUtil.getYearsByCongress(bill.congress)}`;
  }
  
  render() {
    var bill = this.state.bill;
    var typeNumber = `${bill.type.toUpperCase()} ${bill.number}`
    return (
      <li>
        <BillType type={bill.type}/>
        <div onClick={this.handleBillInfoClick} className={'li-bill-title link'}>
          {typeNumber} — {this.getCongressTitle(bill)}
        </div>
        <div className={'li-bill-details'}>
          <div className={'bill-details-title fullwidth'}>{bill.title}</div>
          <div className={'bill-details-update fullwidth'}>Last Update: {this.state.updated}</div>
          {
            //<BillStatus action={bill.latestAction}/>
          }
        </div>
      </li>
    );
  }
}