<?php
/**
 * @var integer $collection_id
 * @var integer[] $items
 */
?>

<div id="reshortz-collection-<?php echo esc_attr($collection_id)?>"
     data-reshortz-collection="<?php echo esc_attr($collection_id)?>"
     :class="{
    'reshortz-base': true,
    'reshortz-collection': true,
    'is-fullscreen' : this.isFullScreen,
    'is-playing': this.isPlaying,
    'is-paused': this.isPaused,
    'ad-running': this.ad_running,
    'show-views': this.ui_settings.show_views,
    'is-mobile': this.isMobile,
    'failed-fullscreen': this.failedFullScreen
}" v-cloak>

    <div class="current-reshort-wrapper" v-if="current">
        <div class="current-reshort"  ref="currentElem">

            <div class="current-reshort__ad-running" v-if="ad_running">{{l10n.ad_running}}</div>
            <div class="current-reshort__views-count" v-if="!ad_running && ui_settings.show_views === 'yes'">
                <span class="current-reshort__views-count__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M279.6 160.4C282.4 160.1 285.2 160 288 160C341 160 384 202.1 384 256C384 309 341 352 288 352C234.1 352 192 309 192 256C192 253.2 192.1 250.4 192.4 247.6C201.7 252.1 212.5 256 224 256C259.3 256 288 227.3 288 192C288 180.5 284.1 169.7 279.6 160.4zM480.6 112.6C527.4 156 558.7 207.1 573.5 243.7C576.8 251.6 576.8 260.4 573.5 268.3C558.7 304 527.4 355.1 480.6 399.4C433.5 443.2 368.8 480 288 480C207.2 480 142.5 443.2 95.42 399.4C48.62 355.1 17.34 304 2.461 268.3C-.8205 260.4-.8205 251.6 2.461 243.7C17.34 207.1 48.62 156 95.42 112.6C142.5 68.84 207.2 32 288 32C368.8 32 433.5 68.84 480.6 112.6V112.6zM288 112C208.5 112 144 176.5 144 256C144 335.5 208.5 400 288 400C367.5 400 432 335.5 432 256C432 176.5 367.5 112 288 112z"/></svg>
                </span>
                <span class="current-reshort__views-count__text">{{current.views}}</span>
            </div>


            <div class="current-reshort__close" @click="exitButton()">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="121.31px" height="122.876px" viewBox="0 0 121.31 122.876" enable-background="new 0 0 121.31 122.876" xml:space="preserve"><g><path fill-rule="evenodd" clip-rule="evenodd" d="M90.914,5.296c6.927-7.034,18.188-7.065,25.154-0.068 c6.961,6.995,6.991,18.369,0.068,25.397L85.743,61.452l30.425,30.855c6.866,6.978,6.773,18.28-0.208,25.247 c-6.983,6.964-18.21,6.946-25.074-0.031L60.669,86.881L30.395,117.58c-6.927,7.034-18.188,7.065-25.154,0.068 c-6.961-6.995-6.992-18.369-0.068-25.397l30.393-30.827L5.142,30.568c-6.867-6.978-6.773-18.28,0.208-25.247 c6.983-6.963,18.21-6.946,25.074,0.031l30.217,30.643L90.914,5.296L90.914,5.296z"/></g></svg>
            </div>


            <div class="current-reshort__progress">
                <div class="current-reshort__progressbar" ref="progressBar" :style="{
                    width: progress_width + '%'
                }"></div>
            </div>

            <div class="current-reshort__term" v-if="viewing_tag || viewing_cat">
                <div class="current-reshort__tag" v-if="viewing_tag">
                    <span class="current-reshort__term-label">{{ l10n.viewing_tag}}</span>
                    <span class="current-reshort-term-tag-val">{{viewing_tag.name}}</span>
                </div>
                <div class="current-reshort__cat" v-if="viewing_cat">
                    <span class="current-reshort__term-label">{{ l10n.viewing_category}}</span>
                    <span class="current-reshort-term-tag-val">{{viewing_cat.name}}</span>
                </div>
            </div>

            <div class="current-reshort__main" ref="videoWrapper">
                <div class="current-reshort__video" v-if="current.video">
                    <video ref="videoElement">
                         <source :src="current.video">
                    </video>
                </div>
            </div>

            <div class="current-reshort__prev" @click="loadPrevVideo()"></div>
            <div class="current-reshort__next" @click="loadNextVideo()"></div>

            <div class="current-reshort__controls" ref="currentItemControls">
                <span class="rs-control-icon rs-buffering-icon" v-show="isLoading && !isPlaying && !isPaused">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M304 48C304 74.51 282.5 96 256 96C229.5 96 208 74.51 208 48C208 21.49 229.5 0 256 0C282.5 0 304 21.49 304 48zM304 464C304 490.5 282.5 512 256 512C229.5 512 208 490.5 208 464C208 437.5 229.5 416 256 416C282.5 416 304 437.5 304 464zM0 256C0 229.5 21.49 208 48 208C74.51 208 96 229.5 96 256C96 282.5 74.51 304 48 304C21.49 304 0 282.5 0 256zM512 256C512 282.5 490.5 304 464 304C437.5 304 416 282.5 416 256C416 229.5 437.5 208 464 208C490.5 208 512 229.5 512 256zM74.98 437C56.23 418.3 56.23 387.9 74.98 369.1C93.73 350.4 124.1 350.4 142.9 369.1C161.6 387.9 161.6 418.3 142.9 437C124.1 455.8 93.73 455.8 74.98 437V437zM142.9 142.9C124.1 161.6 93.73 161.6 74.98 142.9C56.24 124.1 56.24 93.73 74.98 74.98C93.73 56.23 124.1 56.23 142.9 74.98C161.6 93.73 161.6 124.1 142.9 142.9zM369.1 369.1C387.9 350.4 418.3 350.4 437 369.1C455.8 387.9 455.8 418.3 437 437C418.3 455.8 387.9 455.8 369.1 437C350.4 418.3 350.4 387.9 369.1 369.1V369.1z"/></svg>
                </span>
                <span class="rs-control-icon" @click="playVideo()" v-show="isLoaded && !this.isPlaying">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M361 215C375.3 223.8 384 239.3 384 256C384 272.7 375.3 288.2 361 296.1L73.03 472.1C58.21 482 39.66 482.4 24.52 473.9C9.377 465.4 0 449.4 0 432V80C0 62.64 9.377 46.63 24.52 38.13C39.66 29.64 58.21 29.99 73.03 39.04L361 215z"/></svg>
                </span>
                <span class="rs-control-icon" @click="pauseVideo()" v-show="isLoaded && !this.isPaused">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M272 63.1l-32 0c-26.51 0-48 21.49-48 47.1v288c0 26.51 21.49 48 48 48L272 448c26.51 0 48-21.49 48-48v-288C320 85.49 298.5 63.1 272 63.1zM80 63.1l-32 0c-26.51 0-48 21.49-48 48v288C0 426.5 21.49 448 48 448l32 0c26.51 0 48-21.49 48-48v-288C128 85.49 106.5 63.1 80 63.1z"/></svg>
                </span>
            </div>

            <div class="current-reshort__actions">
                <div class="current-reshort__action" @click="toggleMute()" v-show="!viewListDisplayed">
                    <span class="rs-action--icon rs-icon--mute" v-if="!this.muted">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M412.6 182c-10.28-8.334-25.41-6.867-33.75 3.402c-8.406 10.24-6.906 25.35 3.375 33.74C393.5 228.4 400 241.8 400 255.1c0 14.17-6.5 27.59-17.81 36.83c-10.28 8.396-11.78 23.5-3.375 33.74c4.719 5.806 11.62 8.802 18.56 8.802c5.344 0 10.75-1.779 15.19-5.399C435.1 311.5 448 284.6 448 255.1S435.1 200.4 412.6 182zM473.1 108.2c-10.22-8.334-25.34-6.898-33.78 3.34c-8.406 10.24-6.906 25.35 3.344 33.74C476.6 172.1 496 213.3 496 255.1s-19.44 82.1-53.31 110.7c-10.25 8.396-11.75 23.5-3.344 33.74c4.75 5.775 11.62 8.771 18.56 8.771c5.375 0 10.75-1.779 15.22-5.431C518.2 366.9 544 313 544 255.1S518.2 145 473.1 108.2zM534.4 33.4c-10.22-8.334-25.34-6.867-33.78 3.34c-8.406 10.24-6.906 25.35 3.344 33.74C559.9 116.3 592 183.9 592 255.1s-32.09 139.7-88.06 185.5c-10.25 8.396-11.75 23.5-3.344 33.74C505.3 481 512.2 484 519.2 484c5.375 0 10.75-1.779 15.22-5.431C601.5 423.6 640 342.5 640 255.1S601.5 88.34 534.4 33.4zM301.2 34.98c-11.5-5.181-25.01-3.076-34.43 5.29L131.8 160.1H48c-26.51 0-48 21.48-48 47.96v95.92c0 26.48 21.49 47.96 48 47.96h83.84l134.9 119.8C272.7 477 280.3 479.8 288 479.8c4.438 0 8.959-.9314 13.16-2.835C312.7 471.8 320 460.4 320 447.9V64.12C320 51.55 312.7 40.13 301.2 34.98z"/></svg>
                    </span>

                    <span class="rs-action--icon rs-icon--mute" v-if="this.muted">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M301.2 34.85c-11.5-5.188-25.02-3.122-34.44 5.253L131.8 160H48c-26.51 0-48 21.49-48 47.1v95.1c0 26.51 21.49 47.1 48 47.1h83.84l134.9 119.9c5.984 5.312 13.58 8.094 21.26 8.094c4.438 0 8.972-.9375 13.17-2.844c11.5-5.156 18.82-16.56 18.82-29.16V64C319.1 51.41 312.7 40 301.2 34.85zM513.9 255.1l47.03-47.03c9.375-9.375 9.375-24.56 0-33.94s-24.56-9.375-33.94 0L480 222.1L432.1 175c-9.375-9.375-24.56-9.375-33.94 0s-9.375 24.56 0 33.94l47.03 47.03l-47.03 47.03c-9.375 9.375-9.375 24.56 0 33.94c9.373 9.373 24.56 9.381 33.94 0L480 289.9l47.03 47.03c9.373 9.373 24.56 9.381 33.94 0c9.375-9.375 9.375-24.56 0-33.94L513.9 255.1z"/></svg>
                    </span>
                </div>

                <div class="current-reshort__action" @click="toggleLike()"
                     v-show="(ui_settings.allow_likes  === 'yes' || ui_settings.show_likes === 'yes') && !ad_running && !viewListDisplayed ">

                    <span class="rs-action--icon rs-icon--unliked" v-if="!current.liked">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M244 84L255.1 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 0 232.4 0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84C243.1 84 244 84.01 244 84L244 84zM255.1 163.9L210.1 117.1C188.4 96.28 157.6 86.4 127.3 91.44C81.55 99.07 48 138.7 48 185.1V190.9C48 219.1 59.71 246.1 80.34 265.3L256 429.3L431.7 265.3C452.3 246.1 464 219.1 464 190.9V185.1C464 138.7 430.4 99.07 384.7 91.44C354.4 86.4 323.6 96.28 301.9 117.1L255.1 163.9z"/></svg>
                    </span>

                    <span class="rs-action--icon rs-icon--liked" v-if="current.liked">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M0 190.9V185.1C0 115.2 50.52 55.58 119.4 44.1C164.1 36.51 211.4 51.37 244 84.02L256 96L267.1 84.02C300.6 51.37 347 36.51 392.6 44.1C461.5 55.58 512 115.2 512 185.1V190.9C512 232.4 494.8 272.1 464.4 300.4L283.7 469.1C276.2 476.1 266.3 480 256 480C245.7 480 235.8 476.1 228.3 469.1L47.59 300.4C17.23 272.1 .0003 232.4 .0003 190.9L0 190.9z"/></svg>
                    </span>

                    <span class="rs-action--value" v-show="ui_settings.show_likes === 'yes'">
                        {{ current.likes }}
                    </span>

                </div>


                <div class="current-reshort__action" @click="toggleViewList" v-show="!ad_running">
                    <span class="rs-action--icon rs-icon--viewlist">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M88 48C101.3 48 112 58.75 112 72V120C112 133.3 101.3 144 88 144H40C26.75 144 16 133.3 16 120V72C16 58.75 26.75 48 40 48H88zM480 64C497.7 64 512 78.33 512 96C512 113.7 497.7 128 480 128H192C174.3 128 160 113.7 160 96C160 78.33 174.3 64 192 64H480zM480 224C497.7 224 512 238.3 512 256C512 273.7 497.7 288 480 288H192C174.3 288 160 273.7 160 256C160 238.3 174.3 224 192 224H480zM480 384C497.7 384 512 398.3 512 416C512 433.7 497.7 448 480 448H192C174.3 448 160 433.7 160 416C160 398.3 174.3 384 192 384H480zM16 232C16 218.7 26.75 208 40 208H88C101.3 208 112 218.7 112 232V280C112 293.3 101.3 304 88 304H40C26.75 304 16 293.3 16 280V232zM88 368C101.3 368 112 378.7 112 392V440C112 453.3 101.3 464 88 464H40C26.75 464 16 453.3 16 440V392C16 378.7 26.75 368 40 368H88z"/></svg>
                    </span>
                </div>

                <div :class="{
                    'current-reshort__action': true,
                    'current-reshort__skipad': true,
                    'current-reshort__skipad--show': show_ad_skip
                }" v-show="ad_running" @click="skipAd">
                    <span class="rs-action--icon rs-icon--viewlist">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M52.51 440.6l171.5-142.9V214.3L52.51 71.41C31.88 54.28 0 68.66 0 96.03v319.9C0 443.3 31.88 457.7 52.51 440.6zM308.5 440.6l192-159.1c15.25-12.87 15.25-36.37 0-49.24l-192-159.1c-20.63-17.12-52.51-2.749-52.51 24.62v319.9C256 443.3 287.9 457.7 308.5 440.6z"/></svg>
                    </span>
                </div>

            </div>

            <div :class="{
                'current-reshort__aside': true,
                'current-reshort__aside--expanded': contentExpanded || !this.isMobile,
            }" ref="contentSection">

                <div class="current-reshort__adcontent" v-if="ad_running">
                    <div class="current-reshort__adcontenthtml" v-if="ad_settings.ad_text">{{ad_settings.ad_text}}</div>
                    <div class="current-reshort__ad_btn_content" v-if="ad_settings.ad_btn_url && ad_settings.ad_btn_text">
                        <a :href="ad_settings.ad_url" class="current-reshort__ad-btn">{{ ad_settings.ad_btn_text }}</a>
                    </div>
                </div>

                <ul class="reshort-item__categories" v-if="ui_settings.show_cat === 'yes' && current.categories.length">
                    <li v-for="cat in current.categories" :key="cat.id" class="reshort-item__category">
                        <a :href="cat.url" @click.prevent="loadItemsFromCat(cat)" class="reshort-item__category-url">{{ cat.name }}</a>
                    </li>
                </ul>

                <div class="current-reshort__url" v-if="current.visit_url">
                    <a :href="current.visit_url">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M172.5 131.1C228.1 75.51 320.5 75.51 376.1 131.1C426.1 181.1 433.5 260.8 392.4 318.3L391.3 319.9C381 334.2 361 337.6 346.7 327.3C332.3 317 328.9 297 339.2 282.7L340.3 281.1C363.2 249 359.6 205.1 331.7 177.2C300.3 145.8 249.2 145.8 217.7 177.2L105.5 289.5C73.99 320.1 73.99 372 105.5 403.5C133.3 431.4 177.3 435 209.3 412.1L210.9 410.1C225.3 400.7 245.3 404 255.5 418.4C265.8 432.8 262.5 452.8 248.1 463.1L246.5 464.2C188.1 505.3 110.2 498.7 60.21 448.8C3.741 392.3 3.741 300.7 60.21 244.3L172.5 131.1zM467.5 380C411 436.5 319.5 436.5 263 380C213 330 206.5 251.2 247.6 193.7L248.7 192.1C258.1 177.8 278.1 174.4 293.3 184.7C307.7 194.1 311.1 214.1 300.8 229.3L299.7 230.9C276.8 262.1 280.4 306.9 308.3 334.8C339.7 366.2 390.8 366.2 422.3 334.8L534.5 222.5C566 191 566 139.1 534.5 108.5C506.7 80.63 462.7 76.99 430.7 99.9L429.1 101C414.7 111.3 394.7 107.1 384.5 93.58C374.2 79.2 377.5 59.21 391.9 48.94L393.5 47.82C451 6.731 529.8 13.25 579.8 63.24C636.3 119.7 636.3 211.3 579.8 267.7L467.5 380z"/></svg>
                    </a>
                </div>
                <h3 class="title" v-html="current.title"></h3>
                <div class="content" ref="contentSectionContent" v-html="current.excerpt"></div>

                <ul class="reshort-item__tags" v-if="ui_settings.show_tags === 'yes' && current.tags.length">
                    <li v-for="tag in current.tags" :key="tag.id" :class="{
                        'reshort-item__tag': true,
                        'reshort-item__tag--selected': viewing_tag && viewing_tag.id === tag.id
                    }">
                        <a :href="tag.url" @click.prevent="loadItemsFromTag(tag)" class="reshort-item__tag-url">{{ tag.name }}</a>
                    </li>
                </ul>
            </div>

            <div :class="{
                'current-reshort__viewlist': true,
                'current-reshort__viewlist--show': this.viewListDisplayed
            }" >
                <ul class="viewlist-aside">
                    <li v-for="i in items" :key="i.title">
                        <div :class="{
                            'viewlist-aside__item': true,
                            'viewlist-aside__item--current': current && current.id === i.id
                        }" @click.prevent="playFromViewList(i)">
                            <div class="viewlist-item__thumb">
                                <img :src="i.thumbnail" :alt="i.title" v-if="i.thumbnail">
                            </div>
                            <h4 class="viewlist-item__title" v-html="i.title"></h4>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

<!--  Story-like circles  -->
    <ul class="reshortz-collection-list reshortz-collection-circles" v-show="!this.current" v-if="ui_settings.display_type === 'circles'">
        <li v-for="(i, index) in initial_items" :key="i.title" v-show="showing_all || index < ui_settings.posts_to_display">
            <div :class="{
                'reshort-item-preview': true,
                'reshort-item--circle': true,
                'no-image': !i.thumbnail
            }" @click.prevent="viewItem(i)">
                <img :src="i.thumbnail" :alt="i.title" class="reshort-item__thumb" v-if="i.thumbnail">
            </div>
        </li>
        <li v-if="remainingCount && !showing_all">
            <div :class="{
                'reshort-item-preview': true,
                'reshort-item--circle': true,
                'reshort-item--showall': true
            }" @click.prevent="showAll">
                <img :src="remainingNextItem.thumbnail" :alt="remainingNextItem.title" class="reshort-item__thumb" v-if="remainingNextItem && remainingNextItem.thumbnail">
                <div class="reshort-item__card-body" >
                    <div class="reshort-showall__count">
                        <span class="reshort-showall__count-counter">+ {{ remainingCount }}</span>
                    </div>
                </div>
            </div>
        </li>
    </ul>

<!--  Cards like on YT  -->
    <div class="reshortz-collection-cards-wrapper" :class="{
        'reshortz-collection-cards-slider': isSlider,
        'reshortz-collection-cards-grid': isGrid
    }">
        <span @click.prevent="slideLeft" class="reshortz-slider-nav reshortz-slider-nav--left" v-if="isSlider">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M192 448c-8.188 0-16.38-3.125-22.62-9.375l-160-160c-12.5-12.5-12.5-32.75 0-45.25l160-160c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25L77.25 256l137.4 137.4c12.5 12.5 12.5 32.75 0 45.25C208.4 444.9 200.2 448 192 448z"/></svg>
        </span>
        <span @click.prevent="slideRight" class="reshortz-slider-nav reshortz-slider-nav--right" v-if="isSlider">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M64 448c-8.188 0-16.38-3.125-22.62-9.375c-12.5-12.5-12.5-32.75 0-45.25L178.8 256L41.38 118.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0l160 160c12.5 12.5 12.5 32.75 0 45.25l-160 160C80.38 444.9 72.19 448 64 448z"/></svg>
        </span>
        <ul class="reshortz-collection-list reshortz-collection-cards"
            :class="{
            'reshortz-collection-cards2': ui_settings.display_type === 'cards2',
            'reshortz-collection-cards3': ui_settings.display_type === 'cards3',
        }"
            v-show="!this.current && isCardsDisplay"
            ref="cardsList"
        >
            <li v-for="(i, index) in initial_items" :key="i.title" v-show="showing_all || index < ui_settings.posts_to_display"
                :style="{
                'width': ui_settings.display_type !== 'cards3' ? cardsWidth: false,
                'min-width': ui_settings.display_type !== 'cards3' ? cardsWidth: false
            }"
            >
                <div :class="{
                'reshort-item-preview': true,
                'reshort-item--card': true,
                'no-image': !i.thumbnail,
                'reshort-item--card2': ui_settings.display_type === 'cards2',
                'reshort-item--card3': ui_settings.display_type === 'cards3'

            }"
                     :style="{
                'height': ui_settings.display_type !== 'cards3' ? cardsHeight : false
            }"
                     @click.prevent="viewItem(i)">
                    <img :src="i.thumbnail" :alt="i.title" class="reshort-item__thumb" v-if="i.thumbnail"
                         :style="{
                        'height': ui_settings.display_type === 'cards3' ? cardsHeight : false
                    }"
                    >
                    <div  class="reshort-item__placeholder" v-if="!i.thumbnail && ui_settings.display_type === 'cards3'"
                          :style="{
                        'height': cardsHeight
                    }"
                    ></div>


                    <ul class="reshort-item__categories" v-if="ui_settings.show_cat === 'yes' && i.categories.length && ui_settings.display_type === 'cards2'">
                        <li v-for="cat in i.categories" :key="cat.id" class="reshort-item__category">
                            <a :href="cat.url" class="reshort-item__category-url">{{ cat.name }}</a>
                        </li>
                    </ul>

                    <div class="reshort-item__card-body" v-show="isCardsDisplay">
                        <ul class="reshort-item__categories" v-if="ui_settings.show_cat === 'yes' && i.categories.length && (ui_settings.display_type === 'cards' || ui_settings.display_type === 'cards3' ) ">
                            <li v-for="cat in i.categories" :key="cat.id" class="reshort-item__category">
                                <a :href="cat.url" class="reshort-item__category-url">{{ cat.name }}</a>
                            </li>
                        </ul>
                        <h4 class="reshort-item__title" v-html="i.title"></h4>
                    </div>
                </div>
            </li>
            <li v-if="remainingCount && !showing_all"
                :style="{
                'width': cardsWidth,
                'min-width': cardsWidth
            }"
            >
                <div :class="{
                'reshort-item-preview': true,
                'reshort-item--card': true,
                'reshort-item--showall': true
            }"
                     :style="{
                'height': cardsHeight
            }"
                     @click.prevent="showAll">
                    <img :src="remainingNextItem.thumbnail" :alt="remainingNextItem.title" class="reshort-item__thumb" v-if="remainingNextItem && remainingNextItem.thumbnail">
                    <div class="reshort-item__card-body" >
                        <div class="reshort-showall__count">
                            <span class="reshort-showall__count-counter">{{ remainingCount }}</span>
                            <span class="reshort-showall__count-text">{{ l10n.remaining }}</span>
                        </div>
                        <h4 class="reshort-item__title">{{l10n.show_all}}</h4>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
