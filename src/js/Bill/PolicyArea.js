export default class PolicyArea extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };
  }
  
  componentDidMount = () => {

  };
  
  render() {
    if (this.props.policyArea !== null) {
      return (
        <div className="">
            Filed under: {this.props.policyArea.name}
        </div>
      );
    }
  }
}