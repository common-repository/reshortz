(function() {

    'use strict';

    /**
     * Liked videos functionality
     */
    var Reshortz_Liked = {

        /**
         * Bootstrap helper
         */
        init: function() {
            var self = this;
            self.storage = window.localStorage;
            self.refreshStorage();
        },

        /**
         * Bootstrap storage or initialize it
         */
        refreshStorage: function() {
            var self = this;
            var tempLikedList = self.storage.getItem('reshortz_liked_list');

            if (tempLikedList === null || tempLikedList === undefined) {
                self.likedList = [];
                self.storage.setItem('reshortz_liked_list', JSON.stringify(self.likedList));
            } else {
                self.likedList = tempLikedList;
            }

            return self.likedList;
        },

        /**
         * Add video ID to liked list
         * @param item
         */
        like: function(item) {
            var self = this;
            var tempList = self.getLikedList();
            tempList.push(item);
            self.storage.setItem('reshortz_liked_list', JSON.stringify(tempList));

            self.refreshStorage();
        },

        /**
         * Remove video ID from liked list
         *
         * @param item
         */
        dislike: function(item) {
            var self = this;
            var tempList = self.getLikedList();

            var index = tempList.indexOf(item);
            if (index > -1) {
                tempList.splice(index, 1);
            }
            self.storage.setItem('reshortz_liked_list', JSON.stringify(tempList));
        },

        /**
         * Get Liked list
         * @returns {[]|string|*}
         */
        getLikedList: function() {
            var self = this;
            try {
                return JSON.parse(self.likedList);
            } catch (e) {
                return [];
            }
        }
    };

    /**
     * Initialize DOM Ready Helpers
     */
    jQuery(document).ready(function () {
       Reshortz_Liked.init();
    });

    jQuery(document).ready(function() {
        var $items = jQuery('[data-reshortz-collection]');

        $items.each(function() {
            var id = jQuery(this).attr('id');
            var collection_id = jQuery(this).attr('data-reshortz-collection');

            initApp(id, collection_id);
        })
    })

    function initApp(id, collection_id) {
        new Vue({
            el: '#' + id,
            data: {

                /**
                 * Passed settings
                 */

                /**
                 * Array of items to be played
                 */
                items: reshortz['items_' + collection_id],

                /**
                 * Used for initial display, as items may change when going trough tags, category etc.
                 * Items are used for aside playlist.
                 */
                initial_items: reshortz['items_' + collection_id],

                ui_settings: Object.assign({
                    show_likes: 'yes',
                    show_views: 'no',
                    allow_likes: 'yes',
                }, reshortz['ui_settings_' + collection_id]),

                l10n: reshortz.l10n,

                failedFullScreen: false,

                event_listeners: {},


                /**
                 * Pagination stuff
                 */
                showing_all: true,


                /**
                 * Actual Vue data
                 */
                current: null,


                settings: {},

                /**
                 * Video state stuff
                 */
                isFullScreen: false,
                isLoaded: false,
                isLoading: false,
                isPlaying: false,
                isPaused: false,
                muted: false,
                changingVideo: false,

                loaded_duration: null,


                /**
                 * Progress bar
                 */
                $pb: null,
                progress_width: 0,

                /**
                 * Video DOM Reference
                 */
                $video: null,

                /**
                 * Timing stuff
                 */
                currentTime: null,


                /**
                 * Current item additional things
                 */
                contentExpanded: false,


                /**
                 * ViewList stuff
                 */
                viewListDisplayed: false,

                /**
                 * Misc UI handlers
                 */
                swiping: false,

                /**
                 * Tag & categorie meta
                 */
                viewing_tag: null,
                viewing_cat: null,

                /**
                 * Advertising stuff
                 */

                ad_running: false,
                show_ad_skip: false,
                skipping_ad: false,
                watched_ads: [], // ads are only displayed once.
            },

            computed: {
                ad_settings() {
                    return this.current.ad_settings;
                },

                isCardsDisplay() {
                    return [ 'cards', 'cards2', 'cards3' ].indexOf(this.ui_settings.display_type) !== -1;
                },

                duration() {
                    // If we were able to load duration from HTML5 video - use this duration.
                    if(this.loaded_duration) {
                        return this.loaded_duration;
                    }

                    // @todo: Fix this stuff.
                    if(this.ad_running) {
                        return this.ad_settings.ad_duration;
                    } else {
                        if(this.current) {
                            return this.current.duration;
                        }
                    }

                    return  0;
                },

                isSlider() {
                  return this.ui_settings.behavior === 'carousel';
                },

                isGrid() {
                    return this.ui_settings.behavior === 'grid';
                },


                isMobile() {
                    return jQuery(window).width() < 768;
                },

                cardsHeight() {
                    return this.isMobile ? this.ui_settings.cards_height_sm : this.ui_settings.cards_height_lg;
                },

                cardsWidth() {
                   return this.isMobile ? this.ui_settings.cards_width_sm : this.ui_settings.cards_width_lg;
                },

                canPlayAd() {
                    return this.ad_settings && // If ad settings are present
                        this.ad_settings.ad_file !== undefined && // Check if file was added
                        this.ad_settings.ad_file.url !== undefined && // Check if file_url is provided.
                        this.ad_settings.ad_type === 'preroll' &&  // If ad type is preroll
                        this.watched_ads.indexOf(this.ad_settings.ad_file.url) === -1
                },

                remainingCount() {
                    var remaining = parseInt(this.initial_items.length) - parseInt(this.ui_settings.posts_to_display);
                    return remaining >= 0 ? remaining : 0;
                },

                remainingNextItem() {
                  var nextIndex = this.ui_settings.posts_to_display;
                  return this.initial_items[nextIndex] ? this.initial_items[nextIndex] : false;
                },
            },

            mounted() {

                const self = this;


                /**
                 * Add document key listeners.
                 */
                document.onkeydown = function(e) {

                    if( self.ad_running) {
                        return;
                    }

                    if(e.keyCode === 37) {
                        self.loadPrevVideo();
                    }
                    if(e.keyCode === 39) {
                        self.loadNextVideo();
                    }
                    if(e.keyCode === 32) {
                        if(self.isPaused) {
                            self.playVideo();
                        } else {
                            self.pauseVideo();
                        }
                    }
                };
            },

            methods: {

                makeKey(key)
                {
                    return key + '_' + id
                },

                /**
                 * Load an item FROM initial list to be viewed.
                 *
                 * @param item
                 */
                viewItem(item) {
                    var self = this;
                    this.current = item;

                    this.$nextTick(() => {

                        try {
                            if (this.$refs.currentElem.requestFullscreen) {
                                this.$refs.currentElem.requestFullscreen().then(() => {

                                }).catch(e => {
                                    self.couldNotEnterFullScreen();
                                });
                            } else if (this.$refs.currentElem.webkitRequestFullscreen) { /* Safari */
                                this.$refs.currentElem.webkitRequestFullscreen();
                            } else if (elem.msRequestFullscreen) { /* IE11 */
                                this.$refs.currentElem.msRequestFullscreen();
                            }
                        } catch (e) {
                            self.couldNotEnterFullScreen();
                        }


                        // Check if video is liked
                        const likedList = Reshortz_Liked.getLikedList();
                        if(likedList !== undefined && likedList !== null) {
                            if(likedList.indexOf(this.current.id) !== -1) {
                                this.current.liked = true;
                            }
                        }


                        this.isFullScreen = true;

                        this.startVideo();

                        this.$refs.currentElem.addEventListener('fullscreenchange', () => {
                            if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement)
                            {
                                // Run code on exit
                                this.onFullScreenExit();
                            }
                        })
                    });
                },

                couldNotEnterFullScreen() {
                    this.failedFullScreen = true;
                },


                /**
                 * Attach events for current video.
                 * Clean up attached events when needed.
                 */
                attachEvents()
                {
                    const self = this;

                    /**
                     * Handed canplay
                     */
                    if(this.event_listeners.canplay !== undefined) {
                        this.$video.removeEventListener('canplay', this.event_listeners.canplay);
                    }

                    this.event_listeners.canplay = this.$video.addEventListener('canplay', () => {
                        self.isLoaded = true;
                        self.isLoading = false;
                    });

                    /**
                     * Handle error stuff
                     */
                    if(this.event_listeners.error !== undefined) {
                        this.$video.removeEventListener('error', this.event_listeners.error);
                    }

                    this.event_listeners.error = this.$video.addEventListener('error', () => {
                        self.isLoaded = false;
                        self.isLoading = true;
                    });

                    if(this.event_listeners.waiting !== undefined) {
                        this.$video.removeEventListener('waiting', this.event_listeners.waiting);
                    }

                    this.event_listeners.waiting = this.$video.addEventListener('waiting', () => {
                        self.isLoaded = false;
                        self.isLoading = true;
                    });

                    if(this.event_listeners.loadstart !== undefined) {
                        this.$video.removeEventListener('loadstart', this.event_listeners.loadstart);
                    }

                    this.event_listeners.loadstart = this.$video.addEventListener('loadstart', () => {
                        self.isLoaded = false;
                        self.isLoading = true;
                    });

                    if(this.event_listeners.stalled !== undefined) {
                        this.$video.removeEventListener('stalled', this.event_listeners.stalled);
                    }

                    this.event_listeners.stalled = this.$video.addEventListener('stalled', () => {
                        self.isLoaded = false;
                        self.isLoading = true;
                    });


                    /**
                     * Handle play
                     */
                    if(this.event_listeners.play !== undefined) {
                        this.$video.removeEventListener('play', this.event_listeners.play);
                    }

                    this.event_listeners.play = this.$video.addEventListener('play', () => {
                        self.isPaused = false;
                    });

                    /**
                     * Handle pause
                     */
                    if(this.event_listeners.pause !== undefined) {
                        this.$video.removeEventListener('pause', this.event_listeners.pause);
                    }

                    this.event_listeners.pause = this.$video.addEventListener('pause', () => {
                        self.isPlaying = false;
                        self.isPaused = true;
                        jQuery(self.$refs.currentItemControls).css({
                            opacity: 1
                        });
                    });

                    /**
                     * Handle ended
                     */
                    if(this.event_listeners.ended !== undefined) {
                        this.$video.removeEventListener('ended', this.event_listeners.ended);
                    }

                    this.event_listeners.ended= this.$video.addEventListener('ended', () => {

                        /**
                         * Handle case when preroll is ended
                         */
                        if(this.ad_running && this.ad_settings.ad_type === 'preroll') {
                            this.current.video = this.current.original_video;
                            self.$video.load();
                            this.ad_running = false;
                            this.ad_played = true;
                            this.$forceUpdate();
                            setTimeout(function() {
                                self.startVideo();
                            });
                        } else {
                            if(!this.skipping_ad) {
                                this.loadNextVideo();
                            }
                        }
                    });

                    /**
                     * Handle playing event
                     */
                    if(this.event_listeners.playing !== undefined) {
                        this.$video.removeEventListener('playing', this.event_listeners.playing);
                    }

                    this.event_listeners.playing= this.$video.addEventListener('playing', () => {
                        self.isPlaying = true;
                        self.isPaused = false;

                        /**
                         * Set a timeout that will hide the controls
                         */
                        setTimeout(function() {
                            jQuery(self.$refs.currentItemControls).css({
                                opacity: 0
                            });
                        }, 1000);
                    });

                    /**
                     * Handle time update
                     */
                    if(this.event_listeners.timeupdate !== undefined) {
                        this.$video.removeEventListener('timeupdate', this.event_listeners.timeupdate);
                    }

                    this.event_listeners.timeupdate= this.$video.addEventListener('timeupdate', () => {
                        if(this.$video !== null) {
                            this.currentTime = this.$video.currentTime;
                            this.progress_width = ((this.currentTime * 1000) / this.duration) * 100;
                        }
                    });

                    /**
                     * Handle loaded meta data
                     */
                    if(this.event_listeners.loadedmetadata !== undefined) {
                        this.$video.removeEventListener('loadedmetadata', this.event_listeners.loadedmetadata);
                    }

                    this.event_listeners.loadedmetadata = this.$video.addEventListener('loadedmetadata', () => {
                        if(this.$video.duration) {
                            this.loaded_duration = parseFloat(this.$video.duration * 1000);
                        }
                    })


                    /**
                     * Handle content section touch events.
                     */
                    this.handlePostContentTouchMoves();

                },


                /**
                 * Load specific video
                 *
                 * @param item
                 */
                loadItem(item)
                {
                    const self = this;

                    jQuery(this.$refs.currentElem).addClass('loading-another');

                    this.endVideo();
                    this.current = item;
                    this.startVideo();

                    // Collaterals

                    setTimeout(function() {
                        jQuery(self.$refs.currentElem).removeClass('loading-another');
                    }, 500);
                },

                /**
                 * Load previous video
                 */
                loadPrevVideo() {

                    if(this.ad_running) {
                        return;
                    }

                    // If viewList displayed - close it
                    if(this.viewListDisplayed) {
                        this.viewListDisplayed = false;
                        if(this.isPaused) {
                            this.playVideo();
                        }

                        return;
                    }

                    const currentItemIndex = this.items.indexOf(this.current);
                    const prevItemIndex    = currentItemIndex - 1;

                    if(!!this.items[prevItemIndex]) {
                        this.loadItem(this.items[prevItemIndex]);
                    } else {
                        // video not set
                    }
                },

                /**
                 * Load next video
                 */
                loadNextVideo() {

                    if(this.ad_running) {
                        return;
                    }

                    const currentItemIndex = this.items.indexOf(this.current);
                    const nextItemIndex    = currentItemIndex + 1;

                    if(!!this.items[nextItemIndex]) {
                          this.loadItem(this.items[nextItemIndex]);
                    } else {
                          // video not set
                    }
                },


                /**
                 * Entry point for preroll
                 */
                startPreroll() {

                },

                /**
                 * Entry point where we load metadata of the video.
                 */
                startVideo() {
                    const self = this;

                    // Clean up loading state
                    this.isLoaded = false;
                    this.isLoading = true;

                    /**
                     * Load advert if should
                     */
                    this.checkAndLoadAd();

                    this.$pb = this.$refs.progressBar;
                    this.$video = this.$refs.videoElement;
                    this.$video.load();
                    // this.duration = this.$video.duration;
                    this.attachEvents();
                    this.playVideo();
                    this.updateViewsCount();

                    if(!this.ad_running) {
                        this.skipping_ad = false;
                    }
                },

                /**
                 * Check if current item has a preroll ad and load it if needed.
                 */
                checkAndLoadAd()
                {
                    const self = this;

                    // Will replace video attribute src before playing
                    if(this.canPlayAd) {

                        this.current.original_video = this.current.video;
                        this.current.video = this.ad_settings.ad_file.url;
                        this.ad_running = true;

                        this.watched_ads.push(this.ad_settings.ad_file.url);

                        let skippable = true;
                        if(this.ad_settings.ad_skippable) {
                            if(this.ad_settings.ad_skippable === 'no') {
                                skippable = false;
                            }
                        }

                        // check for skip button
                        if(skippable) {
                            let timeout = 3000;
                            if(this.ad_settings.ad_skip_timeout) {
                                timeout = parseFloat(this.ad_settings.ad_skip_timeout);
                            }
                            setTimeout(function() {
                                self.show_ad_skip = true;
                            }, timeout);
                        }
                    }
                },

                /**
                 * Skip ad
                 */
                skipAd()
                {
                    // flag telling that we're seeking video while skipping the ad
                    this.skipping_ad = true;
                    // just seek to the end of the ad and it will automatically end and load next video without much trouble.
                    this.$video.currentTime = Math.ceil(parseFloat(this.duration) / 1000) - 0.01;// sub 0.5s
                },

                /**
                 * Exit button has some context so it may act differently.
                 */
                exitButton() {

                    if(this.viewListDisplayed) {
                        this.viewListDisplayed = false;
                        this.playVideo();
                    } else {

                        if(this.failedFullScreen) {
                            this.onFullScreenExit();
                        }

                        if (document.fullscreenElement) {
                            document.exitFullscreen()
                                .then(() => {})
                                .catch((err) => console.error(err))
                        } else {
                            document.documentElement.requestFullscreen();
                        }
                    }
                },


                /**
                 * Video Controls
                 */
                toggleMute() {
                  this.muted = !this.muted;
                  this.$video.muted = this.muted;
                },

                /**
                 * Handle full screen exit event
                 * @todo: Check and compare with other methods regarding end video.
                 *
                 */
                onFullScreenExit()
                {
                    this.failedFullScreen = false;
                    this.ad_running = false;
                    this.isFullScreen = false;
                    this.current = null;
                    this.endVideo();
                },

                /**
                 * End video
                 */
                endVideo() {
                    this.isPlaying = false;
                    this.pauseVideo();
                    this.currentTime = 0;
                    this.progress_width = 0;
                    // this.$video = null;
                },

                /**
                 * Play video
                 */
                playVideo() {
                    var self = this;

                    if(!this.isPlaying) {
                        const playPromise = this.$video.play();
                        if (playPromise !== undefined) {
                            playPromise.then(_ => {
                                self.isLoading = false;
                            })
                            .catch(error => {
                            });
                        }
                    }
                },

                /**
                 * Pause video
                 */
                pauseVideo() {
                    if(this.isPlaying) {
                        this.$video.pause();
                    }
                },

                /**
                 * Toggle like
                 *
                 * @todo: Integrate API, make likes optional
                 */
                toggleLike() {
                  if(!this.current.liked) {
                      this.likeVideo();
                  }  else {
                      this.dislikeVideo();
                  }
                },

                /**
                 * @todo: API Request to like the video
                 */
                likeVideo() {

                    const self = this;
                    jQuery(document).trigger('RESHORT_POST_LIKED');

                    jQuery.post(reshortz.ajax_url, {
                        action: 'reshortz_like_video',
                        post_id: self.current.id
                    }, function(data) {
                        if(!self.current.liked) {
                            self.current.likes++;
                            self.current.liked = true;
                            // push to storage list
                            Reshortz_Liked.like(self.current.id);
                        }
                    });
                },

                /**
                 * @todo: API Request to unlike the video
                 */
                dislikeVideo() {
                    const self = this;
                    this.current.liked = false;
                    jQuery(document).trigger('RESHORT_POST_UNLIKED');

                    jQuery.post(reshortz.ajax_url, {
                        action: 'reshortz_unlike_video',
                        post_id: self.current.id
                    }, function(data) {
                        if(!self.current.liked) {
                            if(self.current.likes - 1 >= 0) {
                                self.current.likes--;
                            }
                            // push to storage list
                            Reshortz_Liked.dislike(self.current.id);
                        }
                    });
                },

                /**
                 * Update views count
                 */
                updateViewsCount() {
                    const self = this;

                    // Do not count ads views?
                    if(this.ad_running) {
                        return;
                    }

                    jQuery(document).trigger('RESHORT_POST_VIEWED');
                    jQuery.post(reshortz.ajax_url, {
                        action: 'reshortz_update_views',
                        post_id: self.current.id
                    }, function(data) {
                    });
                },


                /**
                 * Post content swipe up/down - show/hide
                 */
                handlePostContentTouchMoves() {
                    const self = this;

                    if(this.ad_running || !this.isMobile) {
                        return;
                    }

                    this.$refs.contentSection.addEventListener('touchstart', handlePostContentTouchStart, false);
                    this.$refs.contentSection.addEventListener('touchmove', handlePostContentTouchEnd, false);

                    var xDown = null;
                    var yDown = null;

                    function getTouches(evt) {
                        return evt.touches ||             // browser API
                            evt.originalEvent.touches; // jQuery
                    }

                    function handlePostContentTouchStart(evt) {
                        const firstTouch = getTouches(evt)[0];
                        xDown = firstTouch.clientX;
                        yDown = firstTouch.clientY;
                    };

                    function handlePostContentTouchEnd(evt) {
                        if ( ! xDown || ! yDown ) {
                            return;
                        }

                        var xUp = evt.touches[0].clientX;
                        var yUp = evt.touches[0].clientY;

                        var xDiff = xDown - xUp;
                        var yDiff = yDown - yUp;

                        if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {/*most significant*/
                            if ( xDiff > 0 ) {
                                /* right swipe */
                            } else {
                                /* left swipe */
                            }
                        } else {
                            if ( yDiff > 0 ) {
                                /* down swipe */
                                self.expandContent();
                            } else {
                                /* up swipe */
                                self.collapseContent();

                            }
                        }
                        /* reset values */
                        xDown = null;
                        yDown = null;
                    }
                },

                /**
                 * Expand content
                 */
                expandContent() {
                    if(this.ad_running) {
                        return;
                    }

                    this.contentExpanded = true;
                    jQuery('.current-reshort__aside .content').slideDown(200);
                },

                /**
                 * Collapse content
                 */
                collapseContent() {
                    this.contentExpanded = false;
                    jQuery('.current-reshort__aside .content').slideUp(200);
                },

                /**
                 * Playlist controls
                 */
                toggleViewList() {
                    this.pauseVideo();
                    this.viewListDisplayed = !this.viewListDisplayed;
                },

                /**
                 * Play video from a list
                 * @param item
                 */
                playFromViewList(item) {
                    this.viewItem(item);
                    this.viewListDisplayed = false;
                },


                /**
                 * Load videos from additional sources like tags & categories.
                 *
                 *
                 *
                 */

                loadItemsFromTag(tag) {
                    const self = this;
                    self.viewing_cat = null;

                    jQuery.post(reshortz.ajax_url, {
                        action: 'reshortz_load_by_tags',
                        tag_id: tag.id,
                        tag_slug: tag.slug
                    }, function(data) {
                        var res = JSON.parse(data);

                        if(res.posts !== undefined && res.posts !== null) {
                            self.items = res.posts;
                            self.viewing_tag = tag;
                        }
                    });

                },


                loadItemsFromCat(cat) {
                    const self = this;
                    self.viewing_tag = null;

                    jQuery.post(reshortz.ajax_url, {
                        action: 'reshortz_load_by_cat',
                        cat_id: cat.id,
                        cat_slug: cat.slug
                    }, function(data) {
                        var res = JSON.parse(data);

                        if(res.posts !== undefined && res.posts !== null) {
                            self.items = res.posts;
                            self.viewing_cat = cat;
                        }
                    });

                },

                /**
                 * Slider
                 */
                slideRight() {
                    var self = this;
                    var leftPos = jQuery(self.$refs.cardsList).scrollLeft();
                    var width = jQuery('.reshort-item--card').first().outerWidth();
                    jQuery(self.$refs.cardsList).animate({scrollLeft: leftPos + width}, 600);
                },

                slideLeft() {
                    var self = this;
                    var leftPos = jQuery(self.$refs.cardsList).scrollLeft();
                    var width = jQuery('.reshort-item--card').first().outerWidth();
                    jQuery(self.$refs.cardsList).animate({scrollLeft: leftPos - width}, 600);
                }
            },
        });
    }

})();
