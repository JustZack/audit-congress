export default class BillInfo extends React.Component {
  constructor(props) {
    super(props);
    this.state = { };
  }

  

  render() {
    return (
      <div>
        <p>I'm a bill!</p>
        {this.props.children}
      </div>
    );
  }
}