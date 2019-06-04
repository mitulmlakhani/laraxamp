var XamppChat = {
    connection : new Strophe.Connection("ws://192.168.0.32:7070/ws/"),
    connect : function(){
        try{
            this.connection.connect(JID, PRESENCE, this.callbacks.onConnect.bind(this), {protocol: "ws", sync: true});
            this.connection.rawInput = this.rawInput;
            this.connection.rawOutput = this.rawOutput;
            return true;
        } catch(e){
            console.log(e);
        }
    },
    rawInput : function(data) {
        console.log('RECV: ' + data);
    },
    rawOutput : function(data) {
        console.log('SENT: ' + data);
    },
    setStatus : function(status){
        var status = $pres().c('show').t(status);
        this.connection.send(status);
        return true;
    },
    getRosters : function(){
        var iq = $iq({ type: 'get' }).c('query', {
            xmlns: 'jabber:iq:roster'
        });
        this.connection.sendIQ(iq, this.callbacks.getRosterCallback);
        return true;
    },
    sendMessage:function(to, msg, type) {
        var message = $msg({
          to: to,
          from: JID,
          id: this.connection.getUniqueId('list'),
          type: type
        }).c("body").t(msg);

        try{
            console.log(this.connection.receipts.sendMessage(message));
        } catch(e){
            console.log(e);
        }
        return true;
    },
    typing:function(to, status){
        var message = $msg({
            to: to,
            from: JID,
            type: 'chat',
          }).c('typing').t(status);
          try{
              this.connection.send(message);
          } catch(e){
              console.log(e);
          }
          return true;
    },
    history: function(jid, page){
        var rsm = new Strophe.RSM({max: page});
        this.connection.archive.listCollections(jid, rsm, function(collections, firstRsm) {
            collections.forEach(function(collection) {
                collection.retrieveMessages(firstRsm, function(messages) {
                    messages.forEach((message) => {                          
                        console.log({
                            'from' : message.from,
                            'to' : message.to,
                            'body' : message.body,
                            'timestamp' : message.timestamp.toString()
                        });
                    });
                });
            });
        });
    },
    callbacks:{
        onConnect:function(status){
            if (status === Strophe.Status.CONNECTED) {
                this.getRosters();
                this.connection.send($pres());
                this.connection.addHandler(this.callbacks.onPresence, null, "presence");
                this.connection.addHandler(this.callbacks.onMessage, null, 'message', null, null, null);
                this.connection.archive.init(this.connection);
                this.history('1@192.168.0.32', 1);
        
                console.log("CONNECTED ");
            } else if (status === Strophe.Status.DISCONNECTED) {
                console.log("DISCONNECTED ");
            } else if (status === Strophe.Status.CONNFAIL) {
                console.log("CONNFAIL ");
            } else if (status === Strophe.Status.AUTHENTICATING) {
                console.log("AUTHENTICATING ");
            } else if (status === Strophe.Status.AUTHFAIL) {
                console.log("AUTHFAIL ");
            } else if (status === Strophe.Status.ERROR) {
                console.log("ERROR ");
            } else if (status === Strophe.Status.ATTACHED){
                console.log("ATTACHED ");
            } else if (status === Strophe.Status.CONNFAIL){
                console.log("CONNFAIL ");
            }
            return true;
        },
        onPresence: function(presence){
            var from = $(presence).attr('from');
            var presence_type = $(presence).attr('type');
            var status = undefined;
            if (presence_type && presence_type != 'error') {
                if (presence_type === 'unavailable') {
                    status = 'offline';
                }
            } else {
                var show = $(presence).find("show").text();
                switch (show) {
                    case 'chat':
                    status = 'online';
                    break;
                    case '':
                    status = 'online';
                    break;
                    case 'dnd':
                    status = 'busy';
                    break;
                    case 'xa':
                    status = 'offline';
                    break;
                    case 'away':
                    status = 'away';
                    break;
                    default:
                    break;
                }
            }
            updatePresence(from, status);
            return true;
        },
        getRosterCallback:function(rosters){
            let rosters_Arr = [];
            $(rosters).find('item').each(function(key, roster) {
                rosters_Arr[key] = {
                    'jid' : $(this).attr('jid'), 'name' : $(this).attr('name')
                };
            });
            listChatters(rosters_Arr);
            return true;
        },
        onMessage: function(msg){
            var type = msg.getAttribute('type');
            var to = msg.getAttribute('to');
            var elems = msg.getElementsByTagName('body');
            var typing = msg.getElementsByTagName('typing');
            
            if (elems.length > 0) {
                var message = {
                    'to' : to,
                    'from' : msg.getAttribute('from'),
                    'type' : type,
                    'body' : Strophe.getText(elems[0])
                };
                newMessage(message);
            }
            
            if (typing.length > 0) {
                var status = {
                    'to' : to,
                    'from' : msg.getAttribute('from'),
                    'status' : Strophe.getText(typing[0])
                };
                typingStatus(status);
            }
            return true;
        },
    }
};