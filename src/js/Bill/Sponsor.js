import DateUtil from "../Util/DateUtil.js";
import StateUtil from "../Util/StateUtil.js";
import PartyUtil from "../Util/PartyUtil.js";

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
    this.props.setView("member", {id: p.bioguideId});
  }

  render() {
    var p = this.props.person;
    var stateAbbr = StateUtil.getStateAbbrFromName(p.state);
    var partAbbr = PartyUtil.getPartyAbbrFromName(p.partyHistory[0].partyName);
    return (
    <div className="">
        {p.firstName} {p.lastName} ({partAbbr}-{stateAbbr}) <button onClick={this.handleMemberInfo}>More...</button>
    </div>
    );
  }
}