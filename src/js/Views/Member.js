import APICallingComponent from "../Api/APICallingComponent.js";
import DateUtil from "../Util/DateUtil.js";
import UrlUtil from "../Util/UrlUtil.js";
import PartyUtil from "../Util/PartyUtil.js";

export default class Member extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
      jsx: (<h1>Loading Data...</h1>),
      member: null
    };

    this.handleBackClick = this.handleBackClick.bind(this);
    this.handleMemberData = this.handleMemberData.bind(this);
  }
  
  static getPath(id) {
    return `member/${id}`;
  }

  componentDidMount = () => {
    var member = this.props.member;
    var path = Member.getPath(member.id);
    UrlUtil.setWindowUrl(`Member`, path);
    this.APIFetch("member", {id: member.id}, this.handleMemberData);
  };

  handleMemberData = (json) => {
    this.setState({
      member: json,
      jsx: this.getJSX(json),
      isset: true
    });
  }

  getJSX = (member) => {
    var state = member.state;
    var party = PartyUtil.getPartyNameFromAltName(member.partyHistory[0].partyName);
    return (
      <div>
        {member.firstName} {member.lastName} - {state} {party}
      </div>
    )
  }
  
  handleBackClick = () => {
    this.props.setView("bill-listing");
  }
  
  render() {
    console.log(this.state.member);
    return (
      <div className="detailed-view">
        <button onClick={this.handleBackClick}>Back To Listing</button>   
          {this.state.jsx}      
      </div>
    );
  }
}