<!DOCTYPE html>
<html class=''>

<head>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

	<link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,300' rel='stylesheet'
		type='text/css'>

	<!-- <script src="https://use.typekit.net/hoy3lrg.js"></script> -->
	<script>try { Typekit.load({ async: true }); } catch (e) { }</script>
	<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css'>
	<link rel='stylesheet prefetch'
		href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.2/css/font-awesome.min.css'>
	<link rel="stylesheet" href="{{ asset('css/chat.css') }}">
</head>

<body>
	<div id="frame">
		<div id="sidepanel">
			<div id="profile">
				<div class="wrap">
					<img id="profile-img" src="http://emilcarlsson.se/assets/mikeross.png" class="online" alt="" />
					<p>Mike Ross</p>
					<!-- <i class="fa fa-chevron-down expand-button" aria-hidden="true"></i> -->
					<div id="status-options">
						<ul>
							<li id="status-online" data-status="chat" class="active"><span class="status-circle"></span>
								<p>Online</p>
							</li>
							<li id="status-away" data-status="away"><span class="status-circle"></span>
								<p>Away</p>
							</li>
							<li id="status-busy" data-status="dnd"><span class="status-circle"></span>
								<p>Busy</p>
							</li>
							<li id="status-offline" data-status="xa"><span class="status-circle"></span>
								<p>Offline</p>
							</li>
						</ul>
					</div>
					<!-- <div id="expanded">
                    </div> -->
				</div>
			</div>
			<div id="search">
				<label for=""><i class="fa fa-search" aria-hidden="true"></i></label>
				<input type="text" placeholder="Search contacts..." />
			</div>
			<div id="contacts">
				<ul>
					
				</ul>
			</div>
			<div id="bottom-bar">
				<button id="addcontact"><i class="fa fa-user-plus fa-fw" aria-hidden="true"></i> <span>Add
						contact</span></button>
				<button id="settings"><i class="fa fa-cog fa-fw" aria-hidden="true"></i> <span>Settings</span></button>
			</div>
		</div>
		<div class="content d-none">
			<div class="contact-profile">
				<img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" />
				<p>Harvey Specter</p>

			</div>
			<div class="messages">
				<ul>
				</ul>
			</div>
			<div class="message-input">
				<span class="typing"></span>
				<div class="wrap">
					<form id="chat-form">
						<input type="text" id="chat-input" placeholder="Write your message..." />
						<i class="fa fa-paperclip attachment" aria-hidden="true"></i>
						<button class="submit"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script src="js/strophejs/dist/strophe.js"></script>
	<script src="js/strophejs/dist/modules/iso8601_support.js"></script>
	<script src="js/strophejs/dist/modules/strophe.RSM.js"></script>
	<script src="js/strophejs/dist/modules/strophe.archive.js"></script>
	<script src="js/strophejs/dist/modules/strophe.receipts.js"></script>
    <script>
        const JID = "{{ session('jid') }}";
        const PRESENCE = "{{ session('presence') }}";
    </script>
    <script src="js/xampp.js"></script>
    <script>
		XamppChat.connect();
    </script>

	<script>
		function listChatters(chatters){
			chatters.forEach(chatter => {
				$("#contacts ul").append(`<li class="contact" data-id="`+chatter.jid+`" data-name="`+chatter.name+`">
						<div class="wrap">
							<span class="contact-status"></span>
							<img src="http://emilcarlsson.se/assets/louislitt.png" alt="" />
							<div class="meta">
								<p class="name">`+chatter.name+`</p>
								<p class="preview">You just got LITT up, Mike.</p>
							</div>
						</div>
					</li>`
				);
			});
			return true;
		}
		
		function updatePresence(id, presence){
			// console.log(id);
			// console.log(presence);
			id = id.split("/")[0];	
			var li = $("#contacts ul").find('[data-id="'+id+'"]').find('.contact-status');
			if(li){
				$(li).attr('class', 'contact-status');
				$(li).addClass(presence);
			}
			return true;
		}

		function newMessage(message) {
			$('<li class="replies"><img src="http://emilcarlsson.se/assets/harveyspecter.png" alt="" /><p>' + message.body + '</p></li>').appendTo($('.messages ul'));
			$(".messages").animate({ scrollTop: $(document).height() }, "fast");
		}

		function typingStatus(response){
			if(response.status == 1){
				$('.typing').html(response.from+' is typing....');
			} else {
				$('.typing').html('');
			}
		}

		$("#contacts ul").on('click', 'li', function(){
			$('.contact-profile p').html($(this).data('name'));
			$('.contact-profile img').attr('src', $(this).find('img').attr('src'));

			$("#chat-form input:text").attr('data-to', $(this).data('id'));
			$(".content").removeClass('d-none');
		});

		$(".messages").animate({ scrollTop: $(document).height() }, "fast");

		$("#profile-img").click(function () {
			$("#status-options").toggleClass("active");
		});

		$(".expand-button").click(function () {
			$("#profile").toggleClass("expanded");
			$("#contacts").toggleClass("expanded");
		});

		$("#status-options ul li").click(function () {
			$("#profile-img").removeClass();
			$("#status-online").removeClass("active");
			$("#status-away").removeClass("active");
			$("#status-busy").removeClass("active");
			$("#status-offline").removeClass("active");
			$(this).addClass("active");

			if ($("#status-online").hasClass("active")) {
				$("#profile-img").addClass("online");
			} else if ($("#status-away").hasClass("active")) {
				$("#profile-img").addClass("away");
			} else if ($("#status-busy").hasClass("active")) {
				$("#profile-img").addClass("busy");
			} else if ($("#status-offline").hasClass("active")) {
				$("#profile-img").addClass("offline");
			} else {
				$("#profile-img").removeClass();
			};
			XamppChat.setStatus($(this).data('status'));
			$("#status-options").removeClass("active");
		});

		$('#chat-form').on('submit', function (e) {
			e.preventDefault();
			var _input = $(this).find("input[type='text']");
			var message = $(_input).val();
			var to = $(_input).data('to');

			if ($.trim(message) == '') {
				return false;
			}
			XamppChat.sendMessage(to, message);
			$('<li class="sent"><img src="http://emilcarlsson.se/assets/mikeross.png" alt="" /><p>' + message + '</p></li>').appendTo($('.messages ul'));
			$('.message-input input').val(null);
			$('.contact.active .preview').html('<span>You: </span>' + message);
			$(".messages").animate({ scrollTop: $(document).height() }, "fast");
		});

		$("#chat-input").on('keyup', function(){
			if($(this).val() != ""){
				XamppChat.typing($(this).data('to'), 1);
			} else {
				XamppChat.typing($(this).data('to'), 0);
			}
			return;
		});
	</script>
</body>

</html>