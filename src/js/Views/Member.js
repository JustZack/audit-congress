import APICallingComponent from "../Api/APICallingComponent.js";
import DateUtil from "../Util/DateUtil.js";
import UrlUtil from "../Util/UrlUtil.js";

export default class MemberInfo extends APICallingComponent {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };

    this.handleBackClick = this.handleBackClick.bind(this);
    this.handleMemberData = this.handleMemberData.bind(this);
  }
  
  static getPath(id) {
    return `member/${id}`;
  }

  componentDidMount = () => {
    var member = this.props.member;
    UrlUtil.setWindowUrl(`Member`, MemberInfo.getPath(member.id));
    this.APIFetch("member", {id: member.id}, this.handleMemberData);
  };

  handleMemberData = (json) => {
    var memberObj = json.member;
    this.setState({
      member: memberObj,
      jsx: this.getJSX(memberObj),
      isset: true
    });
  }

  getJSX = (member) => {
    var state = member.state;
    var party = member.partyHistory[0].partyName;
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
    if (this.state.isset) {
      return (
        <div className="detailed-view">
          <button onClick={this.handleBackClick}>Back To Listing</button>   
            {this.state.jsx}      
        </div>
      );
    } else {
      return (
        <div className="detailed-view">
          <h1>Loading Data...</h1>
          <button onClick={this.handleBackClick}>Back To Listing</button>
        </div>
      );
    }
  }
}