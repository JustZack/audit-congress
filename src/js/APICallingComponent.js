export default class APICallingComponent extends React.Component {
    constructor(props) {
      super(props);
      this.state = { };
    }

    //Fetch from the backend API
    //Use $route as the top level EX: 'bill'
    //Use options for ordered route options EX: {type: 'hr', number: '8080'}
    async APIFetch(route, options, handleAIPData) {
      var url = `${window.location.href}src/api/api.php?route=${route}`;
      var optionKeys = Object.keys(options);
      //Convert from options object to query string
      for (var op in optionKeys) url += `&${optionKeys[op]}=${options[optionKeys[op]]}`;
      console.log(url);
      const response = await fetch(url).then((res) => { return res.json() }).then((json) => { handleAIPData(json) });
    }
  }