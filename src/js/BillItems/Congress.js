export default class Congress extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
    };
  }
  
  componentDidMount = () => {

  };
  
  render() {
    return (
    <div className="">
        From the {this.props.congress} congress
    </div>
    );
  }
}