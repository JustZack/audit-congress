export default class Title extends React.Component {
  constructor(props) {
    super(props);
    this.state = { 
      isset: false,
      jsx: null
    };
  }
  
  componentDidMount = () => {
      var t = this.props.title;
      var jsx = (
          <div>
              {t.titleType}: <i>{t.title}</i>
          </div>
      )
              
      this.setState({
        jsx: jsx
      });
  };
  
  render() {
    return (
    <div className="">
        {this.state.jsx}
    </div>
    );
  }
}