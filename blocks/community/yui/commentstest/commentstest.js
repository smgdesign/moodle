YUI.add('moodle-block_community-commentstest', function(Y) {

    var COMMENTSTESTNAME = 'blocks_community_commentstest';

    var COMMENTSTEST = function() {
        COMMENTSTEST.superclass.constructor.apply(this);
    };

    Y.extend(COMMENTSTEST, Y.Base, {

        initializer : function(params) {

            M.block_community.init_commentstest = new Y.Test.TestCase({

                // Init & destroy
                name: "block_community comments module",
                setUp : function () {
                    this.commentids_example = new Array("12", "13");
                    this.empty_config = {commentids : []};

                    // Creating test comment data
                    for (var i = 0; i < this.commentids_example.length; i++) {
                        Y.one(document.body)
                            .append(Y.Node.create('<div id="commentoverlay-' + this.commentids_example[i] + '">Content</div>')
                                .append(Y.Node.create('<div class="commenttitle">Title</div>')))
                            .append(Y.Node.create('<div id="comments-' + this.commentids_example[i] + '">'));
                    }
                },
                tearDown : function () {
                    delete this.commentids_example;
                    delete this.empty_config;

                    // Removing created DOM elements
                    for (var i = 0; i < this.commentids_example.length; i++) {
                        Y.one('#commentoverlay-' + this.commentids_example[i]).remove();
                        Y.one('#comments-' + this.commentids_example[i]).remove();
                    }
                },

                // Tests

                /**
                 * Create a comments YUI module instance
                 */
                test_comments_creation : function () {

                    var comments = new M.block_community.init_comments(this.empty_config);
                    Y.Test.Assert.isObject(comments, 'Error in comments creation');
                },

                /**
                 * Comments YUI module without comment ids
                 */
                test_without_comments_ids: function () {

                    var comments = new M.block_community.init_comments(this.empty_config);
                    Y.Test.Assert.areEqual(0, comments.overlays.length, 'There should not be any overlay');
                },


                /**
                 * Comments YUI module with ids
                 */
                test_overlays_creation: function () {

                    config = {commentids : this.commentids_example};
                    var comments = new M.block_community.init_comments(config);
                    Y.Test.Assert.areNotEqual(0, comments.overlays.length, 'There should be one overlay per comment id');
                }

            });
        }

    }, {
        NAME : COMMENTSTESTNAME,
        ATTRS : { }
    });

    M.block_community = M.blocks_community || {};
    M.block_community.init_commentstest = function(params) {
        return new COMMENTSTEST(params);
    };

}, '@VERSION@', {requires:['test', 'moodle-block_community-comments']});
