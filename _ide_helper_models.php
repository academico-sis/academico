<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Attendance
 *
 * @mixin IdeHelperAttendance
 * @property int $id
 * @property int $student_id
 * @property int $event_id
 * @property int $attendance_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\AttendanceType $attendance_type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \App\Models\Event $event
 * @property-read string $student_name
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereAttendanceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attendance whereUpdatedAt($value)
 */
	class IdeHelperAttendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendanceType
 *
 * @mixin IdeHelperAttendanceType
 * @property int $id
 * @property array $name
 * @property string|null $class
 * @property string|null $icon
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AttendanceType whereName($value)
 */
	class IdeHelperAttendanceType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Book
 *
 * @mixin IdeHelperBook
 * @property int $id
 * @property string $name
 * @property string|null $price
 * @property string|null $product_code
 * @property-read mixed $price_with_currency
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereProductCode($value)
 */
	class IdeHelperBook extends \Eloquent {}
}

namespace App\Models{
/**
 * NOTE: In the current configuration, the campus with the ID of 1 represent the school itself
 * the campus model with the ID of 2 represents all external courses
 *
 * @mixin IdeHelperCampus
 * @property int $id
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newQuery()
 * @method static \Illuminate\Database\Query\Builder|Campus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Campus withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Campus withoutTrashed()
 */
	class IdeHelperCampus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comment
 *
 * @mixin IdeHelperComment
 * @property int $id
 * @property int $commentable_id
 * @property string $commentable_type
 * @property string $body
 * @property int|null $action
 * @property int|null $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $author
 * @property-read Model|\Eloquent $commentable
 * @property-read mixed $date
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCommentableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 */
	class IdeHelperComment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Config
 *
 * @mixin IdeHelperConfig
 * @property int $id
 * @property string $name
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Config newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Config query()
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Config whereValue($value)
 */
	class IdeHelperConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contact
 *
 * @mixin IdeHelperContact
 * @property int $id
 * @property int $student_id
 * @property string $lastname
 * @property string $firstname
 * @property string|null $idnumber
 * @property string|null $address
 * @property string|null $email
 * @property int|null $relationship_id
 * @property int|null $profession_id
 * @property int|null $invoiceable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $locale
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PhoneNumber[] $phone
 * @property-read int|null $phone_count
 * @property-read \App\Models\Profession|null $profession
 * @property-read \App\Models\ContactRelationship|null $relationship
 * @property-read \App\Models\Student $student
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereInvoiceable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereRelationshipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 */
	class IdeHelperContact extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ContactRelationship
 *
 * @mixin IdeHelperContactRelationship
 * @property int $id
 * @property array $name
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactRelationship whereName($value)
 */
	class IdeHelperContactRelationship extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Coupon
 *
 * @mixin IdeHelperCoupon
 * @property int $id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property mixed $price
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereValue($value)
 */
	class IdeHelperCoupon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Course
 *
 * @mixin IdeHelperCourse
 * @property int $id
 * @property int $campus_id
 * @property int|null $rhythm_id
 * @property int|null $level_id
 * @property int $volume
 * @property string $name
 * @property string $price
 * @property string $price_b
 * @property string $price_c
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $room_id
 * @property int|null $teacher_id
 * @property int|null $parent_course_id
 * @property int|null $exempt_attendance
 * @property int $period_id
 * @property int|null $opened
 * @property int|null $spots
 * @property int|null $head_count
 * @property int|null $new_students
 * @property string|null $color
 * @property int|null $evaluation_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $marked
 * @property int|null $partner_id
 * @property string|null $hourly_price
 * @property int|null $sync_to_lms
 * @property int|null $lms_id
 * @property string|null $remote_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \App\Models\EvaluationType|null $evaluationType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read bool $accepts_new_students
 * @property-read mixed $course_enrollments_count
 * @property-read string $course_level_name
 * @property-read mixed $course_period_name
 * @property-read mixed $course_rhythm_name
 * @property-read mixed $course_room_name
 * @property-read mixed $course_teacher_name
 * @property-read mixed $course_times
 * @property-read mixed $description
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @property-read Course|null $parent
 * @property-read mixed $pending_attendance
 * @property-read mixed $price_with_currency
 * @property-read mixed $shortname
 * @property-read mixed $sortable_id
 * @property-read bool $takes_attendance
 * @property-read mixed $total_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Grade[] $grades
 * @property-read int|null $grades_count
 * @property-read \App\Models\Level|null $level
 * @property-read Partner|null $partner
 * @property-read \App\Models\Period $period
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RemoteEvent[] $remoteEvents
 * @property-read int|null $remote_events_count
 * @property-read \App\Models\Rhythm|null $rhythm
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CourseTime[] $times
 * @property-read int|null $times_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course children()
 * @method static \Illuminate\Database\Eloquent\Builder|Course external()
 * @method static \Illuminate\Database\Eloquent\Builder|Course internal()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course parent()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course realcourses()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCampusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereEvaluationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereExemptAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereHeadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereHourlyPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereMarked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereNewStudents($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereOpened($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereParentCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePartnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePeriodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePriceB($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course wherePriceC($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRemoteVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRhythmId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSpots($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereSyncToLms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereVolume($value)
 */
	class IdeHelperCourse extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CourseTime
 *
 * @mixin IdeHelperCourseTime
 * @property int $id
 * @property int $course_id
 * @property int $day
 * @property string $start
 * @property string $end
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTime whereStart($value)
 */
	class IdeHelperCourseTime extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Discount
 *
 * @mixin IdeHelperDiscount
 * @property int $id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount query()
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discount whereValue($value)
 */
	class IdeHelperDiscount extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Enrollment
 *
 * @mixin IdeHelperEnrollment
 * @property int $id
 * @property int $student_id
 * @property int|null $responsible_id
 * @property int $course_id
 * @property int $status_id
 * @property string|null $total_price
 * @property int|null $parent_id
 * @property int|null $invoice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Enrollment[] $childrenEnrollments
 * @property-read int|null $children_enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\EnrollmentStatusType $enrollmentStatus
 * @property-read mixed $absence_count
 * @property-read mixed $attendance_ratio
 * @property-read mixed $balance
 * @property-read mixed $children
 * @property-read mixed $children_count
 * @property-read mixed $date
 * @property-read mixed $has_book_for_course
 * @property-read mixed $name
 * @property-read mixed $price
 * @property-read mixed $price_with_currency
 * @property-read mixed $product_code
 * @property-read mixed $result_name
 * @property-read mixed $status
 * @property-read mixed $student_age
 * @property-read mixed $student_birthdate
 * @property-read mixed $student_email
 * @property-read mixed $student_name
 * @property-read mixed $total_paid_price
 * @property-read mixed $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Grade[] $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\Result|null $result
 * @property-read \Illuminate\Database\Eloquent\Collection|ScheduledPayment[] $scheduledPayments
 * @property-read int|null $scheduled_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Scholarship[] $scholarships
 * @property-read int|null $scholarships_count
 * @property-read \Illuminate\Database\Eloquent\Collection|SkillEvaluation[] $skill_evaluations
 * @property-read int|null $skill_evaluations_count
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\User $user
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment noresult()
 * @method static Builder|Enrollment parent()
 * @method static Builder|Enrollment pending()
 * @method static Builder|Enrollment period($period)
 * @method static Builder|Enrollment query()
 * @method static Builder|Enrollment real()
 * @method static Builder|Enrollment whereCourseId($value)
 * @method static Builder|Enrollment whereCreatedAt($value)
 * @method static Builder|Enrollment whereDeletedAt($value)
 * @method static Builder|Enrollment whereId($value)
 * @method static Builder|Enrollment whereInvoiceId($value)
 * @method static Builder|Enrollment whereParentId($value)
 * @method static Builder|Enrollment whereResponsibleId($value)
 * @method static Builder|Enrollment whereStatusId($value)
 * @method static Builder|Enrollment whereStudentId($value)
 * @method static Builder|Enrollment whereTotalPrice($value)
 * @method static Builder|Enrollment whereUpdatedAt($value)
 * @method static Builder|Enrollment withoutChildren()
 */
	class IdeHelperEnrollment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EnrollmentStatusType
 *
 * @mixin IdeHelperEnrollmentStatusType
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereName($value)
 */
	class IdeHelperEnrollmentStatusType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EvaluationType
 *
 * @mixin IdeHelperEvaluationType
 * @property int $id
 * @property array $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GradeType[] $gradeTypes
 * @property-read int|null $grade_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Skill[] $skills
 * @property-read int|null $skills_count
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereName($value)
 */
	class IdeHelperEvaluationType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
 * @mixin IdeHelperEvent
 * @property int $id
 * @property int|null $course_id
 * @property int|null $teacher_id
 * @property int|null $room_id
 * @property string $start
 * @property string $end
 * @property string $name
 * @property int|null $course_time_id
 * @property int|null $exempt_attendance
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\CourseTime $coursetime
 * @property-read mixed $color
 * @property-read mixed $end_time
 * @property-read mixed $event_length
 * @property-read mixed $formatted_date
 * @property-read mixed $length
 * @property-read mixed $period
 * @property-read mixed $short_date
 * @property-read mixed $start_time
 * @property-read mixed $volume
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event unassigned()
 * @method static Builder|Event whereCourseId($value)
 * @method static Builder|Event whereCourseTimeId($value)
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereExemptAttendance($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereName($value)
 * @method static Builder|Event whereRoomId($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereTeacherId($value)
 * @method static Builder|Event whereUpdatedAt($value)
 */
	class IdeHelperEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ExternalCourse
 *
 * @mixin IdeHelperExternalCourse
 * @property int $id
 * @property int $campus_id
 * @property int|null $rhythm_id
 * @property int|null $level_id
 * @property int $volume
 * @property string $name
 * @property string $price
 * @property string $price_b
 * @property string $price_c
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $room_id
 * @property int|null $teacher_id
 * @property int|null $parent_course_id
 * @property int|null $exempt_attendance
 * @property int $period_id
 * @property int|null $opened
 * @property int|null $spots
 * @property int|null $head_count
 * @property int|null $new_students
 * @property string|null $color
 * @property int|null $evaluation_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $marked
 * @property int|null $partner_id
 * @property string|null $hourly_price
 * @property int|null $sync_to_lms
 * @property int|null $lms_id
 * @property string|null $remote_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \App\Models\EvaluationType|null $evaluationType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property-read bool $accepts_new_students
 * @property-read mixed $course_enrollments_count
 * @property-read string $course_level_name
 * @property-read mixed $course_period_name
 * @property-read mixed $course_rhythm_name
 * @property-read mixed $course_room_name
 * @property-read mixed $course_teacher_name
 * @property-read mixed $course_times
 * @property-read mixed $description
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @property-read \App\Models\Course|null $parent
 * @property-read mixed $pending_attendance
 * @property-read mixed $price_with_currency
 * @property-read mixed $shortname
 * @property-read mixed $sortable_id
 * @property-read bool $takes_attendance
 * @property-read mixed $total_volume
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Grade[] $grades
 * @property-read int|null $grades_count
 * @property-read \App\Models\Level|null $level
 * @property-read \App\Models\Partner|null $partner
 * @property-read \App\Models\Period $period
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RemoteEvent[] $remoteEvents
 * @property-read int|null $remote_events_count
 * @property-read \App\Models\Rhythm|null $rhythm
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CourseTime[] $times
 * @property-read int|null $times_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course children()
 * @method static \Illuminate\Database\Eloquent\Builder|Course external()
 * @method static \Illuminate\Database\Eloquent\Builder|Course internal()
 * @method static Builder|ExternalCourse newModelQuery()
 * @method static Builder|ExternalCourse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course parent()
 * @method static Builder|ExternalCourse query()
 * @method static \Illuminate\Database\Eloquent\Builder|Course realcourses()
 * @method static Builder|ExternalCourse whereCampusId($value)
 * @method static Builder|ExternalCourse whereColor($value)
 * @method static Builder|ExternalCourse whereCreatedAt($value)
 * @method static Builder|ExternalCourse whereEndDate($value)
 * @method static Builder|ExternalCourse whereEvaluationTypeId($value)
 * @method static Builder|ExternalCourse whereExemptAttendance($value)
 * @method static Builder|ExternalCourse whereHeadCount($value)
 * @method static Builder|ExternalCourse whereHourlyPrice($value)
 * @method static Builder|ExternalCourse whereId($value)
 * @method static Builder|ExternalCourse whereLevelId($value)
 * @method static Builder|ExternalCourse whereLmsId($value)
 * @method static Builder|ExternalCourse whereMarked($value)
 * @method static Builder|ExternalCourse whereName($value)
 * @method static Builder|ExternalCourse whereNewStudents($value)
 * @method static Builder|ExternalCourse whereOpened($value)
 * @method static Builder|ExternalCourse whereParentCourseId($value)
 * @method static Builder|ExternalCourse wherePartnerId($value)
 * @method static Builder|ExternalCourse wherePeriodId($value)
 * @method static Builder|ExternalCourse wherePrice($value)
 * @method static Builder|ExternalCourse wherePriceB($value)
 * @method static Builder|ExternalCourse wherePriceC($value)
 * @method static Builder|ExternalCourse whereRemoteVolume($value)
 * @method static Builder|ExternalCourse whereRhythmId($value)
 * @method static Builder|ExternalCourse whereRoomId($value)
 * @method static Builder|ExternalCourse whereSpots($value)
 * @method static Builder|ExternalCourse whereStartDate($value)
 * @method static Builder|ExternalCourse whereSyncToLms($value)
 * @method static Builder|ExternalCourse whereTeacherId($value)
 * @method static Builder|ExternalCourse whereUpdatedAt($value)
 * @method static Builder|ExternalCourse whereVolume($value)
 */
	class IdeHelperExternalCourse extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fee
 *
 * @mixin IdeHelperFee
 * @property int $id
 * @property string $name
 * @property string $price
 * @property string|null $product_code
 * @property int $default
 * @property-read mixed $price_with_currency
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereProductCode($value)
 */
	class IdeHelperFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Grade
 *
 * @mixin IdeHelperGrade
 * @property int $id
 * @property int $grade_type_id
 * @property int|null $enrollment_id
 * @property int|null $student_id
 * @property int|null $course_id
 * @property string $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Enrollment|null $enrollment
 * @property-read mixed $grade_type_category
 * @property-read \App\Models\GradeType $grade_type
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGradeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 */
	class IdeHelperGrade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradeType
 *
 * @mixin IdeHelperGradeType
 * @property int $id
 * @property int $grade_type_category_id
 * @property string $name
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\GradeTypeCategory $category
 * @property-read mixed $complete_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EvaluationType[] $presets
 * @property-read int|null $presets_count
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereGradeTypeCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeType whereUpdatedAt($value)
 */
	class IdeHelperGradeType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradeTypeCategory
 *
 * @mixin IdeHelperGradeTypeCategory
 * @property int $id
 * @property array $name
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GradeTypeCategory whereName($value)
 */
	class IdeHelperGradeTypeCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Institution
 *
 * @mixin IdeHelperInstitution
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereUpdatedAt($value)
 */
	class IdeHelperInstitution extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invoice
 *
 * @mixin IdeHelperInvoice
 * @property int $id
 * @property int|null $invoice_number
 * @property int|null $invoice_type_id
 * @property int|null $user_id
 * @property string|null $client_name
 * @property string|null $client_idnumber
 * @property string|null $client_address
 * @property string|null $client_email
 * @property int|null $total_price
 * @property int $company_id
 * @property string|null $receipt_number
 * @property \Illuminate\Support\Carbon|null $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_number
 * @property-read mixed $invoice_reference
 * @property-read string $invoice_series
 * @property-read mixed $total_price_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $invoiceDetails
 * @property-read int|null $invoice_details_count
 * @property-read \App\Models\InvoiceType|null $invoiceType
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $products
 * @property-read int|null $products_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ScheduledPayment[] $scheduledPayments
 * @property-read int|null $scheduled_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceDetail[] $taxes
 * @property-read int|null $taxes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereInvoiceTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereReceiptNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUserId($value)
 */
	class IdeHelperInvoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceDetail
 *
 * @mixin IdeHelperInvoiceDetail
 * @property int $id
 * @property int $invoice_id
 * @property string $product_name
 * @property int $price
 * @property string $tax_rate
 * @property int|null $final_price
 * @property string|null $product_code
 * @property int|null $product_id
 * @property string|null $product_type
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $price_with_currency
 * @property-read mixed $total_price
 * @property-read \App\Models\Invoice $invoice
 * @property-read Model|\Eloquent $product
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newQuery()
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereFinalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereProductType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail withTrashed()
 * @method static \Illuminate\Database\Query\Builder|InvoiceDetail withoutTrashed()
 */
	class IdeHelperInvoiceDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceType
 *
 * @mixin IdeHelperInvoiceType
 * @property int $id
 * @property string $name
 * @property array|null $description
 * @property string|null $notes
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceType whereUpdatedAt($value)
 */
	class IdeHelperInvoiceType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeadType
 *
 * @mixin IdeHelperLeadType
 * @property int $id
 * @property array $name
 * @property array|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Student[] $students
 * @property-read int|null $students_count
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeadType whereUpdatedAt($value)
 */
	class IdeHelperLeadType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Leave
 *
 * @mixin IdeHelperLeave
 * @property int $id
 * @property int $teacher_id
 * @property string $date
 * @property int $leave_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\LeaveType $leaveType
 * @property-read \App\Models\Teacher $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave query()
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereLeaveTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Leave whereUpdatedAt($value)
 */
	class IdeHelperLeave extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeaveType
 *
 * @mixin IdeHelperLeaveType
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereName($value)
 */
	class IdeHelperLeaveType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Level
 *
 * @mixin IdeHelperLevel
 * @property int $id
 * @property string $name
 * @property string|null $reference
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection|Skill[] $skill
 * @property-read int|null $skill_count
 * @method static \Illuminate\Database\Eloquent\Builder|Level newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Level newQuery()
 * @method static \Illuminate\Database\Query\Builder|Level onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Level query()
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereReference($value)
 * @method static \Illuminate\Database\Query\Builder|Level withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Level withoutTrashed()
 */
	class IdeHelperLevel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Member
 *
 * @mixin IdeHelperMember
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property int|null $comitee
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereComitee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Member whereUpdatedAt($value)
 */
	class IdeHelperMember extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Partner
 *
 * @mixin IdeHelperPartner
 * @property int $id
 * @property string $name
 * @property string|null $started_on
 * @property string|null $expired_on
 * @property string|null $last_alert_sent_at
 * @property int|null $auto_renewal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Course[] $courses
 * @property-read int|null $courses_count
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner query()
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereAutoRenewal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereExpiredOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereLastAlertSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereStartedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 */
	class IdeHelperPartner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @mixin IdeHelperPayment
 * @property int $id
 * @property int $responsable_id
 * @property int $enrollment_id
 * @property string|null $payment_method
 * @property string|null $date
 * @property string $value
 * @property int|null $status
 * @property string|null $comment
 * @property string|null $receipt_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $invoice_id
 * @property-read string $bic
 * @property-read mixed $date_for_humans
 * @property-read mixed $display_status
 * @property-read string $enrollment_name
 * @property-read string $iban
 * @property-read mixed $month
 * @property-read mixed $value_with_currency
 * @property-read \App\Models\Invoice $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereReceiptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereValue($value)
 */
	class IdeHelperPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Paymentmethod
 *
 * @mixin IdeHelperPaymentmethod
 * @property int $id
 * @property string $name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod query()
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Paymentmethod whereUpdatedAt($value)
 */
	class IdeHelperPaymentmethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Period
 *
 * @mixin IdeHelperPeriod
 * @property int $id
 * @property string $name
 * @property string $start
 * @property string $end
 * @property int $year_id
 * @property int|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $external_courses
 * @property-read int|null $external_courses_count
 * @property-read mixed $acquisition_rate
 * @property-read mixed $courses_with_pending_attendance
 * @property-read mixed $external_enrollments_count
 * @property-read mixed $external_sold_hours_count
 * @property-read mixed $external_students_count
 * @property-read mixed $external_taught_hours_count
 * @property-read mixed $internal_enrollments_count
 * @property-read mixed $next_period
 * @property-read mixed $paid_enrollments_count
 * @property-read mixed $partnerships_count
 * @property-read mixed $pending_enrollments_count
 * @property-read mixed $period_sold_hours_count
 * @property-read mixed $period_taught_hours_count
 * @property-read mixed $previous_period
 * @property-read mixed $students_count
 * @property-read mixed $takings
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $internal_courses
 * @property-read int|null $internal_courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \App\Models\Year $year
 * @method static Builder|Period newModelQuery()
 * @method static Builder|Period newQuery()
 * @method static Builder|Period query()
 * @method static Builder|Period whereEnd($value)
 * @method static Builder|Period whereId($value)
 * @method static Builder|Period whereName($value)
 * @method static Builder|Period whereOrder($value)
 * @method static Builder|Period whereStart($value)
 * @method static Builder|Period whereYearId($value)
 */
	class IdeHelperPeriod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PhoneNumber
 *
 * @mixin IdeHelperPhoneNumber
 * @property int $id
 * @property int $phoneable_id
 * @property string $phoneable_type
 * @property string $phone_number
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Model|\Eloquent $phoneable
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableType($value)
 */
	class IdeHelperPhoneNumber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Profession
 *
 * @mixin IdeHelperProfession
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Profession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profession whereUpdatedAt($value)
 */
	class IdeHelperProfession extends \Eloquent {}
}

namespace App\Models{
/**
 * A RemoteEvent represents hours that do not have a specific date/time, but that should be taken into account in the teacher's total for the month or the period
 *
 * @mixin IdeHelperRemoteEvent
 * @property int $id
 * @property int|null $teacher_id
 * @property string $name
 * @property int $worked_hours
 * @property int|null $period_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $course_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Course|null $course
 * @property-read \App\Models\Period|null $period
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent wherePeriodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RemoteEvent whereWorkedHours($value)
 */
	class IdeHelperRemoteEvent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Result
 *
 * @mixin IdeHelperResult
 * @property int $id
 * @property int $enrollment_id
 * @property int $result_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Enrollment $enrollment
 * @property-read mixed $course_name
 * @property-read mixed $course_period
 * @property-read mixed $result_type
 * @property-read mixed $student_name
 * @property-read \App\Models\ResultType $result_name
 * @method static \Illuminate\Database\Eloquent\Builder|Result newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Result newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Result query()
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereResultTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Result whereUpdatedAt($value)
 */
	class IdeHelperResult extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ResultType
 *
 * @mixin IdeHelperResultType
 * @property int $id
 * @property array $name
 * @property array|null $description
 * @property string|null $icon
 * @property string|null $class
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_description
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ResultType whereUpdatedAt($value)
 */
	class IdeHelperResultType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Rhythm
 *
 * @mixin IdeHelperRhythm
 * @property int $id
 * @property string $name
 * @property int|null $default_volume
 * @property string|null $product_code
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $lms_id
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm newQuery()
 * @method static \Illuminate\Database\Query\Builder|Rhythm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereDefaultVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereProductCode($value)
 * @method static \Illuminate\Database\Query\Builder|Rhythm withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Rhythm withoutTrashed()
 */
	class IdeHelperRhythm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Room
 *
 * @mixin IdeHelperRoom
 * @property int $id
 * @property string $name
 * @property int $campus_id
 * @property int|null $capacity
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Query\Builder|Room onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCampusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereName($value)
 * @method static \Illuminate\Database\Query\Builder|Room withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Room withoutTrashed()
 */
	class IdeHelperRoom extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SchedulePreset
 *
 * @mixin IdeHelperSchedulePreset
 * @property int $id
 * @property string $name
 * @property string $presets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset wherePresets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereUpdatedAt($value)
 */
	class IdeHelperSchedulePreset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ScheduledPayment
 *
 * @mixin IdeHelperScheduledPayment
 * @property int $id
 * @property int $enrollment_id
 * @property int $value
 * @property string $date
 * @property int|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Enrollment $enrollment
 * @property-read mixed $computed_status
 * @property-read mixed $date_for_humans
 * @property-read mixed $status_type_name
 * @property-read mixed $value_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Invoice[] $invoices
 * @property-read int|null $invoices_count
 * @property-read \App\Models\EnrollmentStatusType|null $statusType
 * @method static Builder|ScheduledPayment newModelQuery()
 * @method static Builder|ScheduledPayment newQuery()
 * @method static Builder|ScheduledPayment query()
 * @method static Builder|ScheduledPayment status($status)
 * @method static Builder|ScheduledPayment whereCreatedAt($value)
 * @method static Builder|ScheduledPayment whereDate($value)
 * @method static Builder|ScheduledPayment whereEnrollmentId($value)
 * @method static Builder|ScheduledPayment whereId($value)
 * @method static Builder|ScheduledPayment whereStatus($value)
 * @method static Builder|ScheduledPayment whereUpdatedAt($value)
 * @method static Builder|ScheduledPayment whereValue($value)
 */
	class IdeHelperScheduledPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Scholarship
 *
 * @mixin IdeHelperScholarship
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newQuery()
 * @method static \Illuminate\Database\Query\Builder|Scholarship onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Scholarship withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Scholarship withoutTrashed()
 */
	class IdeHelperScholarship extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\Skill
 *
 * @mixin IdeHelperSkill
 * @property int $id
 * @property string $name
 * @property int $default_weight
 * @property int $level_id
 * @property int $skill_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $order
 * @property-read string $complete_name
 * @property-read Level $level
 * @property-read \Illuminate\Database\Eloquent\Collection|EvaluationType[] $presets
 * @property-read int|null $presets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Skills\SkillEvaluation[] $skill_evaluations
 * @property-read int|null $skill_evaluations_count
 * @property-read \App\Models\Skills\SkillType $skill_type
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill query()
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereDefaultWeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereLevelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereSkillTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Skill whereUpdatedAt($value)
 */
	class IdeHelperSkill extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillEvaluation
 *
 * @mixin IdeHelperSkillEvaluation
 * @property int $id
 * @property int|null $enrollment_id
 * @property int $skill_scale_id
 * @property int $skill_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Enrollment|null $enrollment
 * @property-read \App\Models\Skills\Skill $skill
 * @property-read \App\Models\Skills\SkillScale $skill_scale
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereSkillId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereSkillScaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillEvaluation whereUpdatedAt($value)
 */
	class IdeHelperSkillEvaluation extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillScale
 *
 * @mixin IdeHelperSkillScale
 * @property int $id
 * @property array $shortname
 * @property array $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string $classes
 * @property-read mixed $scale_name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereClasses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillScale whereValue($value)
 */
	class IdeHelperSkillScale extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillType
 *
 * @mixin IdeHelperSkillType
 * @property int $id
 * @property string $shortname
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType query()
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereShortname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SkillType whereUpdatedAt($value)
 */
	class IdeHelperSkillType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Student
 *
 * @mixin IdeHelperStudent
 * @property int $id
 * @property string|null $idnumber
 * @property string|null $address
 * @property string|null $zip_code
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property int|null $genre_id
 * @property string|null $birthdate
 * @property string|null $terms_accepted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $lead_type_id
 * @property string|null $how_did_you_know_us
 * @property int|null $force_update
 * @property int|null $profession_id
 * @property int|null $institution_id
 * @property string|null $account_holder
 * @property string|null $iban
 * @property string|null $bic
 * @property string|null $price_category
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Attendance[] $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Book[] $books
 * @property-read int|null $books_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Contact[] $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $enrollments
 * @property-read int|null $enrollments_count
 * @property string $email
 * @property string $firstname
 * @property-read mixed $is_enrolled
 * @property string $lastname
 * @property-read mixed $lead_status
 * @property-read mixed $lead_status_name
 * @property-read string $name
 * @property-read mixed $student_age
 * @property-read mixed $student_birthdate
 * @property-read \App\Models\Institution|null $institution
 * @property-read \App\Models\LeadType|null $leadType
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection|Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PhoneNumber[] $phone
 * @property-read int|null $phone_count
 * @property-read \App\Models\Profession|null $profession
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enrollment[] $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \App\Models\Title $title
 * @property-read \App\Models\User $user
 * @method static Builder|Student computedLeadType($leadTypeId)
 * @method static Builder|Student newInPeriod($period)
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 * @method static Builder|Student whereAccountHolder($value)
 * @method static Builder|Student whereAddress($value)
 * @method static Builder|Student whereBic($value)
 * @method static Builder|Student whereBirthdate($value)
 * @method static Builder|Student whereCity($value)
 * @method static Builder|Student whereCountry($value)
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereForceUpdate($value)
 * @method static Builder|Student whereGenreId($value)
 * @method static Builder|Student whereHowDidYouKnowUs($value)
 * @method static Builder|Student whereIban($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereIdnumber($value)
 * @method static Builder|Student whereInstitutionId($value)
 * @method static Builder|Student whereLeadTypeId($value)
 * @method static Builder|Student wherePriceCategory($value)
 * @method static Builder|Student whereProfessionId($value)
 * @method static Builder|Student whereState($value)
 * @method static Builder|Student whereTermsAcceptedAt($value)
 * @method static Builder|Student whereUpdatedAt($value)
 * @method static Builder|Student whereZipCode($value)
 */
	class IdeHelperStudent extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\Tax
 *
 * @mixin IdeHelperTax
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tax whereValue($value)
 */
	class IdeHelperTax extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Teacher
 *
 * @mixin IdeHelperTeacher
 * @property int $id
 * @property string|null $hired_at
 * @property string $max_week_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events
 * @property-read int|null $events_count
 * @property string|null $email
 * @property string|null $firstname
 * @property string|null $lastname
 * @property-read string|null $name
 * @property-read mixed $upcoming_leaves
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Leave[] $leaves
 * @property-read int|null $leaves_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\RemoteEvent[] $remote_events
 * @property-read int|null $remote_events_count
 * @property-read \App\Models\User $user
 * @method static Builder|Teacher newModelQuery()
 * @method static Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Query\Builder|Teacher onlyTrashed()
 * @method static Builder|Teacher query()
 * @method static Builder|Teacher whereCreatedAt($value)
 * @method static Builder|Teacher whereDeletedAt($value)
 * @method static Builder|Teacher whereHiredAt($value)
 * @method static Builder|Teacher whereId($value)
 * @method static Builder|Teacher whereMaxWeekHours($value)
 * @method static Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Teacher withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Teacher withoutTrashed()
 */
	class IdeHelperTeacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Title
 *
 * @mixin IdeHelperTitle
 * @property int $id
 * @property string $title
 * @method static \Illuminate\Database\Eloquent\Builder|Title newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Title newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Title query()
 * @method static \Illuminate\Database\Eloquent\Builder|Title whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Title whereTitle($value)
 */
	class IdeHelperTitle extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @mixin IdeHelperUser
 * @property int $id
 * @property string|null $username
 * @property string $firstname
 * @property string $lastname
 * @property string|null $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $locale
 * @property string|null $preferred_course_view
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read mixed $address
 * @property-read mixed $birthdate
 * @property-read mixed $force_update
 * @property-read mixed $idnumber
 * @property-read mixed $name
 * @property-read mixed $student_id
 * @property-read mixed $teacher_id
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student|null $student
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePreferredCourseView($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 */
	class IdeHelperUser extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Year
 *
 * @mixin IdeHelperYear
 * @property int $id
 * @property string $name
 * @property-read mixed $partnerships
 * @property-read mixed $year_distinct_students_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Period[] $periods
 * @property-read int|null $periods_count
 * @method static \Illuminate\Database\Eloquent\Builder|Year newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year query()
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereName($value)
 */
	class IdeHelperYear extends \Eloquent {}
}
