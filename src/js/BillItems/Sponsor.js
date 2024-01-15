import DateUtil from "../DateUtil";

export default class Sponsor extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };
  }
  
  componentDidMount = () => {

  };
  
  render() {
    var p = this.props.person;
    return (
    <div className="">
        [{DateUtil.buildSimpleDateString(p.sponsorshipDate)}] {p.firstName} {p.lastName} ({p.party}-{p.state})
    </div>
    );
  }
}