<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Direct Upload Example</title>
        <style type="text/css">

			/* general styles */
			body {
			    font-family: sans-serif;
			}

			.container {
			    width: 600px;
			    margin: 50px auto;
			}

			form {
			    margin-bottom: 30px;
			}
			/* textarea to show files as json */
			textarea#uploaded {
			    width: 100%;
			    min-height: 150px;
			    font-size: 10px;
			}


			/* show upload progress with bars */
			.progress-bar-area {
			    margin-top: 20px;
			}
			.progress {
			    display: none;
			    position: relative;
			    width: 100%; height: 15px;
			    background: #C7DA9F;
			    border-radius: 15px;
			    overflow: hidden;
			    margin-top: 10px;
			}
			.bar {
			    position: absolute;
			    top: 0; left: 0;
			    width: 0; height: 15px;
			    background: #85C220;
			    text-align: center;
			    color: white;
			    font-weight: bold;
			    font-size: .7em;
			}
			.bar.red { background: tomato; }
        </style>
    </head>
    <body>

        <div class="container">
            <h1>Direct Upload</h1>

            <!-- Direct Upload to S3 Form -->
            <form action="<?php echo $s3FormDetails['url']; ?>"
                  method="POST"
                  enctype="multipart/form-data"
                  class="direct-upload">

                <?php foreach ($s3FormDetails['inputs'] as $name => $value) { ?>
                    <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
                <?php } ?>

                <!-- Key is the file's name on S3 and will be filled in with JS -->
                <input type="hidden" name="key" value="">
                <input type="file" name="file" multiple>

                <!-- Progress Bars to show upload completion percentage -->
                <div class="progress-bar-area"></div>

            </form>

            <!-- This area will be filled with our results (mainly for debugging) -->
            <div>
                <h3>Files</h3>
                <textarea id="uploaded"></textarea>
            </div>
            <form action="{{route('postUploadClient')}}" method="POST">
			    <input type="hidden" name="upload_original_name" id="upload_original_name"/>
			    <label for="version">Version:</label><br />
			    <input type="text" name="version" id="version" /><br />

			    <input type="submit" value="Save" />
			</form>

        </div>


        <!-- Start of the JavaScript -->
        <!-- Load jQuery & jQuery UI (Needed for the FileUpload Plugin) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

        <!-- Load the FileUpload Plugin (more info @ https://github.com/blueimp/jQuery-File-Upload) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>

        <script>
            $(document).ready(function () {

                // Assigned to variable for later use.
                var form = $('.direct-upload');
                var filesUploaded = [];

                // Place any uploads within the descending folders
                // so ['test1', 'test2'] would become /test1/test2/filename
                var folders = [];

                form.fileupload({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    datatype: 'xml',
                    add: function (event, data) {

                        // Show warning message if your leaving the page during an upload.
                        window.onbeforeunload = function () {
                            return 'You have unsaved changes.';
                        };

                        // Give the file which is being uploaded it's current content-type (It doesn't retain it otherwise)
                        // and give it a unique name (so it won't overwrite anything already on s3).
                        var file = data.files[0];
                        var filename = file.name;
                        form.find('input[name="Content-Type"]').val(file.type);
                        form.find('input[name="key"]').val((folders.length ? folders.join('/') + '/' : '') + filename);

                        // Actually submit to form to S3.
                        data.submit();

                        // Show the progress bar
                        // Uses the file size as a unique identifier
                        var bar = $('<div class="progress" data-mod="'+file.size+'"><div class="bar"></div></div>');
                        $('.progress-bar-area').append(bar);
                        bar.slideDown('fast');
                    },
                    progress: function (e, data) {
                        // This is what makes everything really cool, thanks to that callback
                        // you can now update the progress bar based on the upload progress.
                        var percent = Math.round((data.loaded / data.total) * 100);
                        $('.progress[data-mod="'+data.files[0].size+'"] .bar').css('width', percent + '%').html(percent+'%');
                    },
                    fail: function (e, data) {
                        // Remove the 'unsaved changes' message.
                        window.onbeforeunload = null;
                        $('.progress[data-mod="'+data.files[0].size+'"] .bar').css('width', '100%').addClass('red').html('');
                    },
                    done: function (event, data) {
                        window.onbeforeunload = null;

                        // Upload Complete, show information about the upload in a textarea
                        // from here you can do what you want as the file is on S3
                        // e.g. save reference to your server using another ajax call or log it, etc.
                        var original = data.files[0];
                        var s3Result = data.result.documentElement.children;
                        filesUploaded.push({
                            "original_name": original.name,
                            "s3_name": s3Result[2].innerHTML,
                            "size": original.size,
                            "url": s3Result[0].innerHTML
                        });
                        $('#uploaded').html(JSON.stringify(filesUploaded, null, 2));
                        $('#upload_original_name').val(s3Result[0].innerHTML);
                    }
                });
            });
        </script>
    </body>
</html>