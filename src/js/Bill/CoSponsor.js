import DateUtil from "../Util/DateUtil";

export default class Sponsor extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };

    this.handleMemberInfo = this.handleMemberInfo.bind(this);
  }
  
  componentDidMount = () => {

  };
  
  handleMemberInfo() {
    var p = this.props.person;
    this.props.setView("member-detail", {bioguideId: p.bioguideId});
  }

  render() {
    var p = this.props.person;
    return (
    <div className="">
        {p.firstName} {p.lastName} ({p.party}-{p.state}) since {DateUtil.buildSimpleDateString(p.sponsorshipDate)} <button onClick={this.handleMemberInfo}>More...</button>
    </div>
    );
  }
}