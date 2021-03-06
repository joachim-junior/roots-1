</div>
<script>
	CKEDITOR.replace('editor1');
</script>
<script type="module" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule="" src="https://unpkg.com/ionicons@5.0.0/dist/ionicons/ionicons.js"></script>
<script>
	$(document).ready(function() {

		var chatInterval;
		var indexChatInterval;

		$('.delete-not').on('click', function() {
			var notId = $(this).data('id');
			$.ajax({
				url: '<?= base_url() ?>messages/delete_notification/' + notId,
				type: 'post',
				success: function(data) {
					window.location.reload(true);
				},
				error: function() {
					console.log('error');
				}
			});
		});

		$('.show-chat-box').on('click', function() {
			$('#page-content').show();
			var id = $(this).data('id');
			var name = $(this).data('name');
			$('.chat-box-title').html('Chatting with ' + name);
			chatInterval = setInterval(function() {
				$.ajax({
					url: '<?= base_url() ?>/messages/get_messages/' + id,
					type: 'post',
					success: function(response) {
						$('#msg-content').html(response);
						console.log('success');
					},
					error: function() {
						console.log('error');
					}
				});
			}, 2000);
		});

		$('.close-Btn').on('click', function() {
			clearInterval(chatInterval);
		});

		$('#chat-sub').on('click', function() {
			var recieverId = $('.show-chat-box').data('id');
			var avatar = $('#reciever-avatar').val();
			var message = $('#chat_area').val();
			if (message != '') {
				$.ajax({
					url: '<?= base_url(); ?>messages/send_message',
					method: 'POST',
					data: {
						'message': message,
						'recieverId': recieverId
					},
					beforeSend: function() {
						$('#chat-submit').attr('disabled', 'disabled');
					},
					error: function() {
						console.log(recieverId + message)
					},
					success: function(data) {
						console.log('sucess');
						$('#chat-subm').attr('disabled', false);
						var output = '<div class="media media-chat media-chat-reverse">';
						output += '<div class="media-body">';
						output += '<p>' + message + '</p>';
						output += '<p class="meta">' + new Date($.now()) + '</p>';
						output += '</div>';
						output += '</div>';
						$('#msg-content').append(output);
						$('#chat_area').val('');
					}
				});
			} else {
				alert('Chat is empty, Please Type Something in Chat box.');
			}
		});
	});

	function loading() {
		var output = '<div align="center"><br /><br /><br />';
		output += '<img src="<?= base_url(); ?>assets/images/loading.gif" /> Please wait...</div>';
		return output;
	}

	$('#page-content').hide();
	$('.user-trigger').on('click', function() {

		$('.post-creator .meta-data button').attr('disabled', true);

		var id = $(this).data('id');
		console.log(id);
		var avatar = $(this).data('avatar');
		var name = $(this).data('name');
		var data = '<input type="hidden" id="reciever-id" value="' + id + '">';
		var image = '<input type="hidden" id="reciever-avatar" value="' + avatar + '">';
		$('.card-header .chat-box-title').html(name);
		$('#chat-id').html(data);
		$('#chat-avatar').html(image);
		$('#page-content').show();

		$.ajax({
			url: '<?= base_url() ?>users/ajax_fetch_user/' + id,
			type: 'post',
			success: function(response) {
				$('.chat-data-items').append(response);
				checkMultipleAvatar('.chat-data-items .d-flex');
				$('.closeBtn').click(function() {
					$('.post-creator .meta-data button').attr('disabled', false);
				});
			},
			error: function() {
				console.log('error');
			}
		});

		var tid = $('#chat-card').data('id');
		indexChatInterval = setInterval(function() {
			$.ajax({
				url: '<?= base_url() ?>/messages/get_messages/' + id,
				type: 'post',
				success: function(response) {
					$('#chat-content-' + tid).html(response);
					checkMultipleAvatar('.media-chat');
					console.log('success');
				},
				error: function() {
					console.log('error');
				}
			});
		}, 2000);
	});

	$('.closeBtn').on('click', function() {
		clearInterval(indexChatInterval);
	});

	function getLikes() {
		$('.check').each(function() {
			var id = $(this).find($('.index-comment')).data('id');
			$.ajax({
				url: '<?= base_url() ?>posts/get_likes/' + id,
				type: 'post',
				success: function(response) {
					$('#upvotes-' + id).text(response);
					console.log(response)
				},
				error: function() {
					console.log('error');
				}
			});
		});
	}
	getLikes();

	function getDisikes() {
		$('.check').each(function() {
			var id = $(this).find($('.index-comment')).data('id');
			$.ajax({
				url: '<?= base_url() ?>posts/get_dislikes/' + id,
				type: 'post',
				success: function(response) {
					$('#downvotes-' + id).text(response);
				},
				error: function() {
					console.log('error');
				}
			});
		});
	}
	getDisikes();

	function getCommentCount() {
		$('.check').each(function() {
			var id = $(this).find($('.index-comment')).data('id');
			$.ajax({
				url: '<?= base_url() ?>comments/get_comments_count/' + id,
				type: 'post',
				success: function(response) {
					$('.index-comment').each(function() {
						$(this).find($('#count-' + id)).html(response);
					});
				},
				error: function() {
					console.log('error');
				}
			});
		});
	}
	getCommentCount();

	// Show ellipsis on comment-info hover
	function showCommentOptions() {
		$('.comment-info').each(function() {
			let icon = $(this).find("#ion-icon");
			icon.hide();

			let editBtn = $(this).find(".editBtn");
			let commentBody = $(this).find(".commentBody");
			let editComment = $(this).find(".editComment");
			let cancelBtn = $(this).find(".cancelBtn");
			let commentInfo = $(this).find(".comment-info");

			let replyBtn = $(this).find(".replyBtn");
			let replyComment = $(this).find(".replyComment");
			let replyInfo = $(this).find(".replyinfo");

			editComment.hide();
			replyComment.hide();
			cancelBtn.hide();

			$(this).hover(function() {
				var iteration = $(this).data('iteration') || 1

				switch (iteration) {
					case 1:
						icon.show();
						$(editBtn).click(function() {
							commentBody.hide();
							replyComment.hide();
							editComment.show();
							editComment.addClass('added-margin');
						});

						$(cancelBtn).click(function() {
							editComment.removeClass('added-margin');
							editComment.hide();
							replyComment.removeClass('added-margin');
							replyComment.hide();
							commentBody.show();
						});

						$(replyBtn).click(function() {
							editComment.hide();
							replyComment.show();
							replyComment.addClass('added-margin');
						});
						break;

					case 2:
						icon.hide();
						break;
				}

				iteration++;

				if (iteration > 2) iteration = 1
				$(this).data('iteration', iteration)
			});

			$('.replies').each(function() {
				let replyText = $(this).find('.replyText');
				let commentReplies = $(this).find('.comment-replies');
				$(commentReplies).hide()

				$(replyText).click(function() {
					var iteration = $(this).data('iteration') || 1

					switch (iteration) {
						case 1:
							$(commentReplies).show()
							break;

						case 2:
							$(commentReplies).hide()
							break;
					}

					iteration++;

					if (iteration > 2) iteration = 1
					$(this).data('iteration', iteration)
				});


				$(replyInfo).each(function() {
					let editReply = $(this).find(".editReply");
					let replyReply = $(this).find(".replyReply");
					let editInfoBtn = $(this).find(".editinfobtn");
					let replyInfoBtn = $(this).find(".replyinfobtn");
					let replyBody = $(this).find(".replyBody");
					let cancelReplyBtn = $(this).find(".cancelReplyBtn");

					editReply.hide();
					replyReply.hide();
					cancelReplyBtn.hide();

					$(editInfoBtn).click(function() {
						replyBody.hide();
						replyReply.hide();
						editReply.show()
						editReply.addClass('added-margin');
					});

					$(replyInfoBtn).click(function() {
						editReply.hide()
						replyBody.show();
						replyReply.show();
						replyReply.addClass('added-margin');
					});

					$(cancelReplyBtn).click(function() {
						editReply.removeClass('added-margin');
						editReply.hide();
						replyReply.removeClass('added-margin');
						replyReply.hide();
						ReplyBody.show();
					});
				});
			});
		});
	}

	showCommentOptions();

	$('.index-comment').on('click', function() {
		var id = $(this).data('id');
		$.ajax({
			url: '<?= base_url() ?>comments/get_comments',
			type: 'post',
			data: {
				id: id
			},
			beforeSend: function() {
				$('#comments-' + id).html(loading());
			},
			success: function(response) {
				$('#comments-' + id).html(response);
				checkMultipleAvatar('.comment-info');

			},
			error: function() {
				console.log('error');
			}
		});
	});

	// Disable button and enable when user input is not null
	function enableDisableBtn(Btn, inputBox) {
		$(Btn).attr('disabled', true);

		$(inputBox).keyup(function() {
			if ($(inputBox).val()) {
				$(Btn).attr('disabled', false);
			} else {
				$(Btn).attr('disabled', true);
			}
		});
	}

	enableDisableBtn("#input-form-btn", "#input-form");
	enableDisableBtn("#search-bar-btn", "#postSearch");
	enableDisableBtn(".index-comment-postbtn", ".index-comment-body");


	// Hide comment and show when comment-heading is clicked
	$("#comment-div").hide();

	$('.comment-heading').click(function() {

		var iteration = $(this).data('iteration') || 1

		switch (iteration) {
			case 1:
				$("#comment-div").show()
				break;

			case 2:
				$("#comment-div").hide()
				break;
		}

		iteration++;

		if (iteration > 2) iteration = 1
		$(this).data('iteration', iteration)
	});

	// Hide comment and show when comment div is clicked on index page
	function showComments() {
		$('.post-content').each(function() {
			let index_comment_details = $(this).find(".index-comment-details");
			index_comment_details.hide();

			let index_comment = $(this).find(".index-comment");

			index_comment.click(function() {

				var iteration = $(this).data('iteration') || 1

				switch (iteration) {
					case 1:
						index_comment_details.show();
						index_comment_details.scrollTop($(index_comment_details).height());
						break;

					case 2:
						index_comment_details.hide();
						break;
				}

				iteration++;

				if (iteration > 2) iteration = 1
				$(this).data('iteration', iteration)
			});
		});
	}

	showComments();

	// Show chats on click
	function showChats() {
		let nchats = 'Chats';
		$('.chats-title h6 strong').text(nchats);
		$('.chats-title p').hide();
		$('.chat-data').hide();

		$('.chats-title').click(function() {
			var iteration = $(this).data('iteration') || 1

			switch (iteration) {
				case 1:
					$('.chats-title h6 strong').text('Chats');
					$('.chats-title p').show();
					$('.chat-data').show();
					$('.chat-data').css('{padding: 0}');
					$('.chats-title').css('{margin-bottom: 0.5rem}');
					break;

				case 2:
					$('.chats-title h6 strong').text(nchats);
					$('.chats-title p').hide();
					$('.chat-data').hide();
					break;
			}

			iteration++;

			if (iteration > 2) iteration = 1
			$(this).data('iteration', iteration)
		});
	}

	showChats();

	// Show chats on click
	function showChatBox() {
		$('.chat-data-items').click(function() {
			$('#page-content').show();
		});
	}

	showChatBox();

	// Show specific chat on click
	function showSpecificChat() {
		// Close button to close chat session
		$('.closeBtn').click(function() {
			$('#page-content').hide();
		});
	}

	showSpecificChat();

	// Handle Posts without Image
	function post() {
		$('.post').each(function() {
			let postImage = $(this).find('.post-thumbnail');
			let imageDiv = $(this).find('.col-md-5');
			let postContent = $(this).find('.post-content');

			if (postImage.attr('src') == '<?php echo site_url(); ?>assets/images/posts/') {

				imageDiv.remove();
				postContent.removeClass("col-md-7").addClass("col-md-12");
				$(".loader").show();

				setTimeout(function() {
					$(".loader").hide();
					$('.newsfeed').removeClass('d-none').css('{display: flex !important}');
				}, 1000);

			}
		});

		$('.post-search').each(function() {
			let postImage = $(this).find('.search-post-img');

			if (postImage.attr('src') == '<?php echo site_url(); ?>assets/images/posts/') {
				postImage.remove();

				$(".loader").show();

				setTimeout(function() {
					$(".loader").hide();
					$('.post-search-result').removeClass('d-none').css('{display: flex !important}');
				}, 1000);
			}
		});
	}

	function view() {
		$('.carousel-item').each(function() {
			if ($(this).find('img').attr('src') == '<?php echo site_url(); ?>assets/images/posts/') {
				$(this).remove();
			}
		});
	}

	post();
	view();

	// Show view more button on post image hover
	$('.post').each(function() {
		let postImage = $(this).find('.post-thumbnail');
		let title = $(this).find('.title');
		let img2 = $(this).find('.img2');
		let img3 = $(this).find('.img3');
		let img4 = $(this).find('.img4');
		let imagePost = $(this).find('.imagePost');

		$(this).find($('.title').hide());
		$(this).find($('.post-page-img')).hover(function() {

			var iteration = $(this).data('iteration') || 1

			switch (iteration) {
				case 1:
					// Handle view more button on post image in newsfeed
					if (img2.attr('src') == '<?php echo site_url(); ?>assets/images/posts/' && img3.attr('src') == '<?php echo site_url(); ?>assets/images/posts/' && img4.attr('src') == '<?php echo site_url(); ?>assets/images/posts/') {
						$(imagePost).addClass('no-after');
						$(title).css('{display: none !important}');
					} else $(this).find($('.title')).show();
					break;

				case 2:
					$(this).find($('.title')).hide();
					break;
			}

			iteration++;

			if (iteration > 2) iteration = 1
			$(this).data('iteration', iteration)
		});

	});

	// Handles likes
	$('.like').on('click', function() {
		var postId = $(this).data('pid');
		var userId = $(this).data('id');
		$(this).attr('disabled', true);
		$.ajax({
			url: '<?= base_url() ?>posts/likes',
			type: 'post',
			data: {
				postId: postId,
				userId: userId
			},
			success: function(response) {
				$('#upvotes-' + postId).text(response);
			},
			error: function() {
				console.log('error');
			}
		});
	});

	$('.dislike').on('click', function() {
		var postId = $(this).data('pid');
		var userId = $(this).data('id');
		$(this).attr('disabled', true);
		$.ajax({
			url: '<?= base_url() ?>posts/dislikes',
			type: 'post',
			data: {
				postId: postId,
				userId: userId
			},
			success: function(response) {
				$('#downvotes-' + postId).text(response);
				console.log(likes);
			},
			error: function() {
				console.log('error');
			}
		});
	});

	$('.pin-post').on('click', function() {
		var postId = $(this).data('pid');
		var postTitle = $(this).data('title');
		var postSlug = $(this).data('slug');
		$.ajax({
			url: '<?= base_url() ?>posts/get_pin_post/' + postId,
			type: 'post',
			data: {
				postTitle: postTitle,
				postSlug: postSlug
			},
			dataType: 'json',
			success: function(response) {
				$output = '<div class="post-info">';
				$output += '<a href="' + response.slug + '">';
				$output += '<h6 class="post-title">' + response.title + '</h6>';
				$output += '</a></div>';
				$output += '<div class="pin-meta meta-data d-flex justify-content-between">'
				// $output += ' <button class="mr-1 unpin-post" data-id="' + id + '">unpin</button>';
				// $output += '<p class="ml-auto">' + time_ago(new Date($.now())) + '</p>';
				$output += '</div><hr class="separator">';
				// location.reload();
				$('#pin_post').prepend($output);
				$('#pin-' + postId).attr('disabled', true);
				console.log(response);
			},
			error: function() {
				console.log('error');
			}
		});
	});

	$('.unpin-post').on('click', function() {
		var id = $(this).data('id');
		$.ajax({
			url: '<?= base_url() ?>posts/delete_pin_post/' + id,
			type: 'post',
			success: function(data) {
				window.location.reload(true);
			}
		});
	});

	// Check Avatars
	function checkAvatar($parentDiv) {
		var avatar_image = $($parentDiv).find('.avatar-image');
		var attrib = avatar_image.attr('src');
		if (attrib == '') {
			avatar_image.attr('src', '<?= base_url() ?>assets/images/avatar/noimage.jpg');
		}
	}

	function checkMultipleAvatar($parentDiv) {

		$($parentDiv).each(function() {
			var avatar_image = $(this).find('.avatar-image');
			var attrib = avatar_image.attr('src');
			if (attrib == '') {
				avatar_image.attr('src', '<?= base_url() ?>assets/images/avatar/noimage.jpg');
			}
		});

	}

	var id = $('.index-comment').data('id');

	checkAvatar('.view-content .meta-data');
	checkMultipleAvatar('.profile');
	checkMultipleAvatar('.meta-data');
	checkMultipleAvatar('.nearby-meta-data');
	checkMultipleAvatar('.comment-info');
	checkMultipleAvatar('.search-info');
	checkMultipleAvatar('.replyinfo');
	checkMultipleAvatar('.chat-data-items .d-flex');
	checkAvatar('.post-div1');
	checkAvatar('.publisher');


	$image_crop = $('#image_demo').croppie({
		enableExif: true,
		viewport: {
			width: 250,
			height: 250,
			type: 'square'
		},
		boundary: {
			width: 300,
			height: 300
		}
	});

	myUrl = 'users/upload/';
	$('#insert_image').on('change', function() {
		var reader = new FileReader();
		reader.onload = function(event) {
			$image_crop.croppie('bind', {
				url: event.target.result
			}).then(function() {
				console.log('jQuery bind complete');
			});
		}
		reader.readAsDataURL(this.files[0]);
		$('#insertimageModal').modal('show');
	});

	$('.crop_image').on('click', function(event) {
		$image_crop.croppie('result', {
			type: 'canvas',
			size: 'viewport'
		}).then(function(response) {
			$.ajax({
				url: '<?= base_url() ?>users/upload',
				type: 'POST',
				data: {
					"image": response
				},

				error: function() {
					alert('Error Uploading Image. This may be due to a fault in your internet connection. Please try again later. Thanks');
				},

				success: function(data) {
					$('#insertimageModal').modal('hide');
					$('#insert_image').empty();
					window.location.reload(true);
				}
			});
		});
	});

	$('#input-form').on('click', function() {
		$('#search-submit').attr('disabled', false);
	});

	$('#chat-submit').on('click', function() {
		var id = $('#chat-card').data('id');
		var recieverId = $('#reciever-id').val();
		var avatar = $('#reciever-avatar').val();
		var message = $('#chat_msg_area').val();
		console.log(recieverId);
		if (message != '') {
			$.ajax({
				url: '<?= base_url(); ?>messages/send_message',
				method: 'POST',
				data: {
					'message': message,
					'recieverId': recieverId
				},
				beforeSend: function() {
					$('#chat-submit').attr('disabled', 'disabled');
				},
				error: function() {
					console.log(recieverId + message)
				},
				success: function(data) {
					console.log('sucess');
					$('#chat-submit').attr('disabled', false);
					var output = '<div class="media media-chat media-chat-reverse">';
					output += '<div class="media-body">';
					output += '<p>' + message + '</p>';
					output += '<p class="meta">' + new Date($.now()) + '</p>';
					output += '</div>';
					output += '</div>';
					$('#chat-content-' + id).append(output);
					$('#chat_msg_area').val('');
				}
			});
		} else {
			alert('Chat is empty, Please Type Something in Chat box.');
		}
	});
</script>
</body>

</html>