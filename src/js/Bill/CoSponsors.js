import CoSponsor from "./CoSponsor.js";

export default class CoSponsors extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
      jsx: null
    };
  }
  
  componentDidMount = () => {
    var jsx = []
    var people = this.props.cosponsors;
    if (people !== undefined) {
      for (var i = 0;i < people.length;i++) {
        jsx.push(<CoSponsor person={people[i]} key={people[i].bioguideId} setView={this.props.setView}/>)
      }
      this.setState({
        jsx: jsx
      });
    }
  };
  
  render() {
    return (
    <div className="">
        <h3>CoSponsored by:</h3>
          {this.state.jsx}
    </div>
    );
  }
}