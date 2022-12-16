// The <Mouse> component encapsulates the behavior we need...
class Mouse extends React.Component {
  constructor(props) {
    super(props);
    this.handleMouseMove = this.handleMouseMove.bind(this);
    this.state = { x: 0, y: 0 };
  }

  handleMouseMove(event) {
    this.setState({
      x: event.clientX,
      y: event.clientY
    });
  }

  render() {
    return (
      <div onMouseMove={this.handleMouseMove}>
        <p>
          The mouse is at {[this.state.x, this.state.y].join(",")}.
        </p>
      </div>
    );
  }
}
  
export default class MouseTracker extends React.Component {
  render() {
    return (
      <div>
        <h1>Move the mouse around!</h1>
        <Mouse/>
      </div>
    );
  }
}