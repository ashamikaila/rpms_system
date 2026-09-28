<div class="student-dashboard" id="studentDashboard">
    <div class="student-summary-grid" aria-label="Student summary">
        <button type="button" data-student-filter="all"><span>My Documents</span><i class="fa-regular fa-folder-open" aria-hidden="true"></i><strong data-summary-count>0</strong></button>
        <button type="button" data-student-filter="review"><span>For Review</span><i class="fa-regular fa-clock" aria-hidden="true"></i><strong data-summary-count>0</strong></button>
        <button type="button" data-student-filter="revision"><span>For Revision</span><i class="fa-regular fa-pen-to-square" aria-hidden="true"></i><strong data-summary-count>0</strong></button>
        <button type="button" data-go="calendar"><span>Deadlines</span><i class="fa-regular fa-calendar" aria-hidden="true"></i><strong data-summary-count>0</strong></button>
    </div>
    <article class="panel student-current"><h2>Current Submission</h2><div id="currentSubmissionContent"></div></article>
    <div class="student-dashboard-columns">
        <article class="panel"><h2>Adviser Review</h2><div id="adviserReviewContent"></div></article>
        <article class="panel"><div class="panel-head"><h2>IERB Progress</h2><button type="button" data-go="progress">View Progress</button></div><div id="studentIerbContent"></div></article>
    </div>
    <div class="student-dashboard-columns">
        <article class="panel"><div class="panel-head"><h2>Upcoming Deadlines</h2><button type="button" data-go="calendar">View All</button></div><div id="studentDeadlines"></div></article>
        <article class="panel"><div class="panel-head"><h2>Recent Notifications</h2><button type="button" data-go="notifications">View All</button></div><div id="studentNotifications"></div></article>
    </div>
</div>
<dialog id="studentFeedbackDialog" class="student-feedback-dialog" aria-labelledby="studentFeedbackTitle"><h2 id="studentFeedbackTitle">Adviser Feedback</h2><p id="studentFeedbackText"></p><form method="dialog"><button class="primary-btn">Close</button></form></dialog>
