@extends('layouts.master')
@section('title', 'FAQ')
@section('ogtype', 'article')
@section('ogurl', route('faq'))
@section('ogimage',config('content.cloudfront').'/assets/'.config('content.assets_ver').'/video-small-1.png')
@section('ogvideo','')
@section('embed_player','')
@section('content')
    <style type="text/css">
        .answer .icon img{
            width: 16px;
            height: 16px;;
        }

    </style>
    <main>
    <div class="gradient"> <div class="background"></div> </div>
    <div class="faq">
        <div class="title float-center"> BOOM FAQ</div>
        <div class="update float-center"> Updated July 13, 2017
        </div>

        <h2 class="question">Top Questions</h2>

        <div class="question">What broadcasting softwares and platforms do you support?
        </div>

        <div class="answer">
            We currently support:<br/>
		- Twitch, Mixer<br/>
		- Software: OBS Studio, OBS Studio with FTL, unofficially XSplit (thanks to this <a href="https://frogdude.tv/boom-tv-on-xsplit/">workaround</a> by <a href="https://twitter.com/Sllayt3r">@Sllayt3r</a>) 
        </div>

        <div class="question">I’m on the latest OBS and Boom, but I am not seeing Boom Replay as a source in OBS! How do I fix this?
        </div>

        <div class="answer">
            1) Quit OBS<br/>

            2) Copy file: C:\Program Files (x86)\Boom.tv\BoomReplay\thirdparty\obs\x64\boom-capture.dll to C:\Program Files (x86)\obs-studio\obs-plugins\64bit <br/>

            3) Copy folder: C:\Program Files (x86)\Boom.tv\BoomReplay\thirdparty\obs\boom-capture to C:\Program Files (x86)\obs-studio\data\obs-plugins <br/>

        </div>
	<div class="question">Does Boom support OBS Studio FTL?
	</div>
	<div class="answer">
		Yes. Please manually install Boom Replay plugin by:<br/>
	1) Quit OBS ftl<br/>
	2) Copy file: C:\Program Files (x86)\Boom.tv\BoomReplay\thirdparty\obs\x64\boom-capture.dll to C:\Program Files (x86)\obs-studio-ftl\obs-plugins\64bit<br/>
	3) Copy folder: C:\Program Files (x86)\Boom.tv\BoomReplay\thirdparty\obs\boom-capture to C:\Program Files (x86)\obs-studio-ftl\data\obs-plugins
	</div>

        <div class="question">I am getting a blank black space in my replay window! What’s the issue?</div>

        <div class="answer">This is a codec issue, please install the the K-Lite Codec pack here: <a href="http://files2.codecguide.com/K-Lite_Codec_Pack_1324_Basic.exe" target="_blank">http://files2.codecguide.com/K-Lite_Codec_Pack_1324_Basic.exe</a>
        </div>

        <div class="question">Can I change or remove the border of the Boom Replay window?</div>

        <div class="answer">Yes. Please right-click your Boom Replay source in OBS > Properties. You can change the border color and thickness there. If you do not want a border, change thickness to 0. 
        <a class="icon" target="_blank" href="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/32umBK3.png'}}"> <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/32umBK3.png'}}"> </a>
        </div>

        <div class="question">Do I have to be a Twitch partner to use Boom?</div>

        <div class="answer">No, all Twitch streamers can use Boom. If you are a partner or affiliate, you can add a trigger emote. If you are not, you can set the text in your settings or tell users to type !boom or boom
        </div>

        <div class="question">How do I change Boom Meter skin?</div>

        <div class="answer">Go to Settings -> Boom Meter --> Revisit Setup --> Select the look for your Boom Meter?<br/>
            Note: After selecting a skin, please make sure to refresh your browser cache in OBS for the Boom Meter source.
        </div>

        <div class="question">The Boom Meter seems to automatically change its size. I can only see a small part of it! What is the issue?</div>

        <div class="answer">
            In OBS, refresh the cache of the current page! Right-click the source of the Boom Meter > Properties, scroll to the bottom of the page and click on "Refresh cache of current page"
            <a class="icon" target="_blank" href="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/faq-boom-meter.png'}}">
                <img src="{{config('content.cloudfront').'/assets/'.config('content.assets_ver').'/faq-boom-meter.png'}}" />
            </a>
        </div>

        <div class="question">I am confused about setting up BoomReplay in OBS, can you help?</div>

        <div class="answer">Sure, here’s a video to start with <a href="https://www.youtube.com/watch?v=3VKIrHaajNg" target="_blank">http://boom.tv/obshelp.</a>
        </div>

        <div class="question">I am confused about setting up Boom Meter, can you help?</div>

        <div class="answer">Sure, here’s a video to start with <a href="https://www.youtube.com/watch?v=I42SPPN1bhY" target="_blank">http://boom.tv/meterhelp.</a></div>

        <div class="question">I am confused about how to create Montage, can you help?</div>

        <div class="answer">Sure, here’s a video to start with <a href="https://www.youtube.com/watch?v=RwDPiCgp_bw" target="_blank">http://boom.tv/montagehelp.</a></div>

        <h2 class="question">Specific OBS Setup Issues</h2>

        <div class="question">Why don’t I see “Boom Replay” on the list of sources to add?</div>

        <div class="answer">Have you recently updated OBS? If you updated OBS after installing BoomReplay, it will not register properly.<br/>

                In order for it to work, you may have to go to the Streaming tab > “Revisit OBS setup” > scroll to the bottom to Re-install. If that they does not work, please uninstall and reinstall BoomReplay.
        </div>
        <div class="question">Why is my game crashing?</div>

        <div class="answer">Boom does not interfere in ANY way with the game. It only creates replays off your live stream.
        </div>
        <div class="question">Voice command is not working, why is that?</div>

        <div class="answer">If you insert or removes headphone after you start BoomReplay, game sound and voice command will not work (For example: Although you turn on "Record with game sound", generated video still has no sound).<br/>
            It requires you to plug-in a headphone and microphone before you open BoomReplay and the game in order to record game sound and use voice command.
        </div>
        <div class="question">Why do I need the BoomTVmod?</div>

        <div class="answer">This added feature allows you to have a bot do the work for you. It posts the link to the replay after it finishes loading onto the <a href="https://boom.tv/videos">Boom.tv</a> website.<br/>

            <u>Note</u>: Make sure BoomTVmod has mod in your channel.
        </div>
        <div class="question">Is there a limit to how many replay recordings I can make?</div>

        <div class="answer">As of right now, there is no limit. Since the files go to your computer's folder, it goes as much as the amount of free space you have left on your hard drive.
        </div>
        <div class="question">Where are my replay videos located?
        </div>
        <div class="answer">Your replay videos default directory is located  C:\ProgramData\Boom\recordings or whatever you set the file location during the installation process in step 4. It can also be found in the Streaming tab in Settings.
        </div>
        <div class="question">Where is the default configuration file located?
        </div>
        <div class="answer">It is located in C:\ProgramData\Boom\~$config.
        </div>
        <div class="question">List of 3rd party software dependencies
        </div>
        <div class="answer">The BoomReplay setup.exe will ask you to download the following softwares if you do not have them pre-installed. It is necessary in order for BoomReplay to work properly.<br/>
            <ul style="list-style-type:circle;margin-left: 50px">
                <li>K-Lite Codec</li>
                <li>Virtual Audio Capture Grabber</li>
                <li>Microsoft Visual C++ 2010</li>
            </ul>
        </div>
        <div class="question">What games do you support?
        </div>
        <div class="answer">BoomReplay supports ALL games. The app does not interfere in ANY way with the game. It only creates replays off your live stream.
        </div>

        <div class="question">If Boom is crashing, please send us your logs by doing the following:</div>

        <div class="answer" style="margin-left: 50px">
            1.Go to your system tray and find the BoomReplay icon.<br/>
            2.Right-click and select “Send Log”<br/>
            3.You will receive a notification that your logs have been sent<br/>
        </div>
        <div class="question">How do I delete my replay recordings off of Boom.tv?</div>

        <div class="answer">If you go to <a href="https://boom.tv/myprofile">https://boom.tv/myprofile</a> , you can click ellipses (. . .) at the bottom right of each replay to delete it from Boom.tv.

        </div>

        <div class="note">
            <div>Don’t see any questions you have on here?</div>
            <div>Join our <a href="https://discordapp.com/invite/yyZ7Z8G">discord</a> and post your questions there. We
                will get back to you asap!
            </div>
        </div>
    </div>
    </main>
@endsection
