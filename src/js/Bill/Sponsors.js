import Sponsor from "./Sponsor.js";

export default class Sponsors extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
      jsx: null
    };
  }
  
  componentDidMount = () => {
    var jsx = []
    var people = this.props.sponsors;
    if (people !== undefined && people !== null) {
      for (var i = 0;i < people.length;i++) {
        jsx.push(<Sponsor person={people[i]} key={people[i].bioguideId} setView={this.props.setView}/>)
      }
      this.setState({
        jsx: jsx
      });
    }
  };
  
  render() {
    return (
    <div className="">
        <h3>Sponsored by:</h3>
          {this.state.jsx}
    </div>
    );
  }
}