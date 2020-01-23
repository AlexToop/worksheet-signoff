<div class="modal" id="submitModel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sign-off edit confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmationText">
                <p>Placeholder</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitRequestToDatabase">Save changes</button>
                <button type="button" class="btn btn-secondary" id="cancelSubmission" data-dismiss="modal">Cancel
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="declineModel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dismiss student</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please confirm you've finished changes to this students marks.</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="dismissStudentButton" class="btn btn-primary"
                        onclick="removeRequest('<?php echo (isset($request)) ? $request : "NA" ?>')">Remove from queue
                </button>
                <button type="button" id="dismissStudentButton" class="btn btn-primary"
                        onclick="unassignRequest('<?php echo (isset($request)) ? $request : "NA" ?>')">Keep in queue
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="uploadModel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Grade Center Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Mark sections in the grade center CSV must follow the following examples.
                    </br><b>Example 1:</b> Fifth worksheet, marked in its entirety. Column names include W5.
                    </br><b>Example 2:</b> Eleventh worksheet, two questions. Column names include W11Q1 and W11Q2.
                    </br><b>Example 3:</b> Third worksheet, two questions, the first with two sub-parts and the other
                    with no sub-parts. Column names include W3Q1P1, W3Q1P2 and W3P2.</p>
                <input type="file" id="gradeCenterFileUpload"/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="editUsersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleEditModal">Admins</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="viewUsersModalBody">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="removeUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleRemovalModal">Please confirm removal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="removeUserModalBody">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="removeStudentButton" class="btn btn-danger">Remove</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="deleteModule" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleRemoveModal">Delete class?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="removeModuleModalBody">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="removeModuleButton" class="btn btn-danger">Remove</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<div class="modal" id="generalInfoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generalInfoModal">Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="generalInfoModal">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Okay</button>
            </div>
        </div>
    </div>
</div>