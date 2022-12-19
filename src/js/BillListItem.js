import BillStatus from "./BillStatus.js";

export default class BillListItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = {

    };

    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);
  }

  handleBillInfoClick = () => {
    this.props.setView("bill-detail", this.props.bill);
  }

  render() {
    var bill = this.props.bill;
    return (
      <li onClick={this.handleBillInfoClick}>
        <h1 className={'fullwidth li-bill-title'}>{bill.title}</h1>
        <div className={'fullwidth li-bill-details'}>
          <p>{`${bill.type+bill.number}`}</p>
          <p>{bill.updateDate}</p>
          <BillStatus latestAction={bill.latestAction}/>
        </div>
      </li>
    );
  }
}