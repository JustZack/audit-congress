export default class BriefBillListItem extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
        title: this.props.title,
        congress: this.props.congress,
        type: this.props.type,
        number: this.props.number
    };
    this.handleBillInfoClick = this.handleBillInfoClick.bind(this);
  }

  handleBillInfoClick = () => {
    this.props.setView("bill-info", this.props);
  }

  render() {
    return (
      <li onClick={this.handleBillInfoClick}>
        {this.props.title}
      </li>
    );
  }
}