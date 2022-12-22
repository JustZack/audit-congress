import BillStatus from "./BillStatus.js";

export default class BillListItem extends React.Component {
  constructor(props) {
    super(props);
    var bill = this.props.bill;
    this.state = {
      id: this.properBillID(bill.type,bill.number),
      congress: this.properCongressName(bill.congress),
      updated: new Date(bill.updateDateIncludingText).toLocaleString('en-US')
    };

    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);
    this.properBillID = this.properBillID.bind(this);
    this.properCongressName = this.properCongressName.bind(this);
    this.pluralizeNumber = this.pluralizeNumber.bind(this);
    this.getYearsByCongress = this.getYearsByCongress.bind(this);
  }
  //Create the normal looking bill id like H.R.5634
  properBillID = (type, number) => {
    var billID = "";
    for(var c in type) billID += type[c]+".";
    billID+=number;
    return billID;
  }
  //Decide the suffix for the given number
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
  getYearsByCongress = (number) => {
    var n2 = number*2;
    //congress# * 2 + 1787 = first year of this congress session
    var years = `(${n2+1787} - `;
    //Then compute the second year
    if (n2 > 72) years += `${n2+1788})`;
    else years += `${n2+1789})`;

    return years;
  }
  properCongressName = (number) => {
    return `${this.pluralizeNumber(number)} congress ${this.getYearsByCongress(number)}`;
  }

  handleBillInfoClick = () => {
    this.props.setView("bill-detail", this.props.bill);
  }

  render() {
    var bill = this.props.bill;
    return (
      <li>
        <strong onClick={this.handleBillInfoClick} className={'li-bill-title link'}>
          {this.state.id} — {this.state.congress}
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