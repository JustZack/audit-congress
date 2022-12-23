import BillStatus from "./BillStatus.js";

export default class BillListItem extends React.Component {
  static weekdays = ["Sunday", "Monday", "Tuesday", "Wedsday", "Thursday", "Friday", "Saturday"];
  static months = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];
  
  constructor(props) {
    super(props);
    var bill = this.props.bill;
    
    this.state = {
      updated: this.buildUpdatedTimeString(bill.updateDateIncludingText)
    };

    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);
    this.buildUpdatedTimeString = this.buildUpdatedTimeString.bind(this);
    this.pluralizeNumber = this.pluralizeNumber.bind(this);
    
  }

  pluralizeNumber = (number) => {
    var relevent = number%100;
    var tens = relevent%10;
    var suffix = "";

    if (tens == 0 || (relevent >= 4 && relevent <= 20)) 
                        suffix = "th";
    else if (tens == 1) suffix = "st";
    else if (tens == 2) suffix = "nd";
    else if (tens == 3) suffix = "rd";
    suffix = number+suffix;
    return suffix;
}

  buildUpdatedTimeString = (datetimestring) => {
    var dt = new Date(datetimestring);
    //EX:            Friday Dec.
    var localTime = `${BillListItem.weekdays[dt.getDay()]} ${BillListItem.months[dt.getMonth()]}`;
    //EX:          4th
    localTime += ` ${this.pluralizeNumber(dt.getDay())} `;
    //EX:         11:30 PM
    localTime += dt.toLocaleTimeString('en-us', { hour: 'numeric', minute: 'numeric', hour12: true });
    //EX:         2022
    localTime += `  ${dt.getFullYear()}`;
    return localTime;
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