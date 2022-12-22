export default class Header extends React.Component {
    constructor(props) {
      super(props);
      this.state = { };
    }
  
    render() {
      return (
        <div className="site-header">{this.props.children}</div>
      );
    }
  }