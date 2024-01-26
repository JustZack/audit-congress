export default class Chamber extends React.Component {
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
        Originated in the {this.props.chamber}
    </div>
    );
  }
}