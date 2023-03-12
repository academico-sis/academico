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
 * @property int $id
 * @property int $student_id
 * @property int $event_id
 * @property int $attendance_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\AttendanceType $attendanceType
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
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\AttendanceType
 *
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
	class AttendanceType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Book
 *
 * @property int $id
 * @property string $name
 * @property int|null $price
 * @property string|null $product_code
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string $price_with_currency
 * @property-read string $type
 * @method static \Illuminate\Database\Eloquent\Builder|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Book whereProductCode($value)
 */
	class Book extends \Eloquent implements \App\Models\Interfaces\InvoiceableModel {}
}

namespace App\Models{
/**
 * NOTE: In the current configuration, the campus with the ID of 1 represent the school itself
 * the campus model with the ID of 2 represents all external courses
 *
 * @property int $id
 * @property array $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus query()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Campus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Campus withoutTrashed()
 */
	class Campus extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Comment
 *
 * @property int $id
 * @property int $commentable_id
 * @property string $commentable_type
 * @property string $body
 * @property int|null $action
 * @property int|null $author_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\User|null $author
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $commentable
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
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Config
 *
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
	class Config extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Contact
 *
 * @property int $id
 * @property int $student_id
 * @property string $firstname
 * @property string $lastname
 * @property string|null $idnumber
 * @property string|null $address
 * @property string|null $email
 * @property int|null $relationship_id
 * @property int|null $profession_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $locale
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PhoneNumber> $phone
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
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereRelationshipId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 */
	class Contact extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ContactRelationship
 *
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
	class ContactRelationship extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $price_with_currency
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereValue($value)
 */
	class Coupon extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Course
 *
 * @property int $id
 * @property int $campus_id
 * @property int|null $rhythm_id
 * @property int|null $level_id
 * @property int|null $volume
 * @property string $name
 * @property int|null $price
 * @property string|null $price_b
 * @property string|null $price_c
 * @property int|null $hourly_price
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $room_id
 * @property int|null $teacher_id
 * @property int|null $parent_course_id
 * @property int|null $exempt_attendance
 * @property int $period_id
 * @property int|null $spots
 * @property int|null $head_count
 * @property int|null $new_students
 * @property string|null $color
 * @property int|null $evaluation_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $partner_id
 * @property string|null $remote_volume
 * @property int|null $sync_to_lms
 * @property int|null $lms_id
 * @property int $marked
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Course> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \App\Models\EvaluationType|null $evaluationType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read bool $accepts_new_students
 * @property-read mixed $course_enrollments_count
 * @property-read mixed $course_period_name
 * @property-read mixed $course_teacher_name
 * @property-read mixed $course_times
 * @property-read mixed $description
 * @property-read mixed $formatted_end_date
 * @property-read mixed $formatted_start_date
 * @property-read Course|null $parent
 * @property-read string $price_with_currency
 * @property-read mixed $shortname
 * @property-read mixed $sortable_id
 * @property-read bool $takes_attendance
 * @property-read mixed $total_volume
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \App\Models\Level|null $level
 * @property-read \App\Models\Partner|null $partner
 * @property-read \App\Models\Period $period
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \App\Models\Rhythm|null $rhythm
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseTime> $times
 * @property-read int|null $times_count
 * @method static \Illuminate\Database\Eloquent\Builder|Course external()
 * @method static \Illuminate\Database\Eloquent\Builder|Course hideChildren()
 * @method static \Illuminate\Database\Eloquent\Builder|Course internal()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course partner(\App\Models\Partner $partner)
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
	class Course extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CourseTime
 *
 * @property int $id
 * @property int $course_id
 * @property int $day
 * @property string $start
 * @property string $end
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Course $course
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
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
	class CourseTime extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Discount
 *
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
	class Discount extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Enrollment
 *
 * @property int $id
 * @property int $student_id
 * @property int $responsible_id
 * @property int $course_id
 * @property int $status_id
 * @property int|null $total_price
 * @property int|null $parent_id
 * @property int|null $invoice_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Enrollment> $childrenEnrollments
 * @property-read int|null $children_enrollments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\EnrollmentStatusType|null $enrollmentStatus
 * @property-read mixed $absence_count
 * @property-read mixed $attendance_ratio
 * @property-read mixed $balance
 * @property-read mixed $children
 * @property-read mixed $children_count
 * @property-read mixed $date
 * @property-read mixed $has_book_for_course
 * @property-read mixed $name
 * @property-read string $price_with_currency
 * @property-read mixed $product_code
 * @property-read mixed $result_name
 * @property-read mixed $status
 * @property-read mixed $student_age
 * @property-read mixed $student_birthdate
 * @property-read mixed $student_email
 * @property-read mixed $student_formatted_gender
 * @property-read mixed $student_name
 * @property-read mixed $total_paid_price
 * @property-read string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grade> $grades
 * @property-read int|null $grades_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $invoiceDetails
 * @property-read int|null $invoice_details_count
 * @property-read \App\Models\Result|null $result
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ScheduledPayment> $scheduledPayments
 * @property-read int|null $scheduled_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Scholarship> $scholarships
 * @property-read int|null $scholarships_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skills\SkillEvaluation> $skillEvaluations
 * @property-read int|null $skill_evaluations_count
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment course(int $courseId)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment noresult()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment parent()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment pending()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment period(int $periodId)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment real()
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereResponsibleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enrollment withoutChildren()
 */
	class Enrollment extends \Eloquent implements \App\Models\Interfaces\InvoiceableModel {}
}

namespace App\Models{
/**
 * App\Models\EnrollmentStatusType
 *
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStatusType whereName($value)
 */
	class EnrollmentStatusType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\EvaluationType
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GradeType> $gradeTypes
 * @property-read int|null $grade_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skills\Skill> $skills
 * @property-read int|null $skills_count
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EvaluationType whereName($value)
 */
	class EvaluationType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Event
 *
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \App\Models\Course|null $course
 * @property-read mixed $color
 * @property-read mixed $end_time
 * @property-read mixed $event_length
 * @property-read mixed $formatted_date
 * @property-read mixed $length
 * @property-read mixed $short_date
 * @property-read mixed $start_time
 * @property-read mixed $volume
 * @property-read \App\Models\Room|null $room
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event unassigned()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCourseTimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereExemptAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereTeacherId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Fee
 *
 * @property int $id
 * @property string $name
 * @property int|null $price
 * @property string|null $product_code
 * @property int $default
 * @property-read string $price_with_currency
 * @property-read string $type
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee query()
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Fee whereProductCode($value)
 */
	class Fee extends \Eloquent implements \App\Models\Interfaces\InvoiceableModel {}
}

namespace App\Models{
/**
 * App\Models\Grade
 *
 * @property int $id
 * @property int $grade_type_id
 * @property int|null $enrollment_id
 * @property string $grade
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Enrollment|null $enrollment
 * @property-read mixed $grade_type_category
 * @property-read \App\Models\GradeType $gradeType
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereGradeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grade whereUpdatedAt($value)
 */
	class Grade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradeType
 *
 * @property int $id
 * @property int $grade_type_category_id
 * @property string $name
 * @property int $total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\GradeTypeCategory|null $category
 * @property-read mixed $complete_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationType> $presets
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
	class GradeType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\GradeTypeCategory
 *
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
	class GradeTypeCategory extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Institution
 *
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
	class Institution extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property int|null $invoice_number
 * @property int|null $invoice_type_id
 * @property string|null $client_name
 * @property string|null $client_idnumber
 * @property string|null $client_address
 * @property string|null $client_email
 * @property string|null $client_phone
 * @property string|null $total_price
 * @property int $company_id
 * @property string|null $receipt_number
 * @property \Illuminate\Support\Carbon|null $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read mixed $balance
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_number
 * @property-read mixed $invoice_reference
 * @property-read string $invoice_series
 * @property-read mixed $total_price_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $invoiceDetails
 * @property-read int|null $invoice_details_count
 * @property-read \App\Models\InvoiceType|null $invoiceType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $scheduledPayments
 * @property-read int|null $scheduled_payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $taxes
 * @property-read int|null $taxes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientPhone($value)
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
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceDetail
 *
 * @property int $id
 * @property int $invoice_id
 * @property string $product_name
 * @property string|null $product_code
 * @property int|null $product_id
 * @property string|null $product_type
 * @property string $quantity
 * @property string $price
 * @property string $tax_rate
 * @property int|null $final_price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $comment
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string $price_with_currency
 * @property-read mixed $total_price
 * @property-read \App\Models\Invoice $invoice
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $product
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail whereComment($value)
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
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceDetail withoutTrashed()
 */
	class InvoiceDetail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\InvoiceType
 *
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
	class InvoiceType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeadType
 *
 * @property int $id
 * @property array $name
 * @property array|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $translated_name
 * @property-read array $translations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Student> $students
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
	class LeadType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Leave
 *
 * @property int $id
 * @property int $teacher_id
 * @property string $date
 * @property int $leave_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
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
	class Leave extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\LeaveType
 *
 * @property int $id
 * @property array $name
 * @property-read array $translations
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType query()
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LeaveType whereName($value)
 */
	class LeaveType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Level
 *
 * @property int $id
 * @property string $name
 * @property string|null $reference
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skills\Skill> $skill
 * @property-read int|null $skill_count
 * @method static \Illuminate\Database\Eloquent\Builder|Level newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Level newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Level onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Level query()
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Level withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Level withoutTrashed()
 */
	class Level extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Member
 *
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
	class Member extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Partner
 *
 * @property int $id
 * @property string $name
 * @property string|null $started_on
 * @property string|null $expired_on
 * @property int|null $send_report_on
 * @property string|null $last_alert_sent_at
 * @property int|null $auto_renewal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
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
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereSendReportOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereStartedOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Partner whereUpdatedAt($value)
 */
	class Partner extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property int|null $responsable_id
 * @property int $invoice_id
 * @property string $payment_method
 * @property string|null $date
 * @property int|null $value
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string $bic
 * @property-read mixed $date_for_humans
 * @property-read string $enrollment_name
 * @property-read string $iban
 * @property-read mixed $month
 * @property-read mixed $value_with_currency
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\Paymentmethod|null $paymentmethod
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereResponsableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereValue($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Paymentmethod
 *
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
	class Paymentmethod extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Period
 *
 * @property int $id
 * @property string $name
 * @property string $start
 * @property string $end
 * @property int $year_id
 * @property int|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read mixed $acquisition_rate
 * @property-read mixed $courses_with_pending_attendance
 * @property-read mixed $takings
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $real_enrollments
 * @property-read int|null $real_enrollments_count
 * @property-read \App\Models\Year $year
 * @method static \Illuminate\Database\Eloquent\Builder|Period newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Period newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Period query()
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Period whereYearId($value)
 */
	class Period extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PhoneNumber
 *
 * @property int $id
 * @property int $phoneable_id
 * @property string $phoneable_type
 * @property string $phone_number
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $phoneable
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber query()
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PhoneNumber wherePhoneableType($value)
 */
	class PhoneNumber extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Profession
 *
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
	class Profession extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Result
 *
 * @property int $id
 * @property int $enrollment_id
 * @property int $result_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
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
	class Result extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ResultType
 *
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
	class ResultType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Rhythm
 *
 * @property int $id
 * @property string $name
 * @property int|null $default_volume
 * @property string|null $product_code
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $lms_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereDefaultVolume($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereLmsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm whereProductCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Rhythm withoutTrashed()
 */
	class Rhythm extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Room
 *
 * @property int $id
 * @property string $name
 * @property int $campus_id
 * @property int|null $capacity
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Campus $campus
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Room onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCampusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Room withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Room withoutTrashed()
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\SchedulePreset
 *
 * @deprecated 
 * @property int $id
 * @property string $name
 * @property string $presets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset wherePresets($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchedulePreset whereUpdatedAt($value)
 */
	class SchedulePreset extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ScheduledPayment
 *
 * @property int $id
 * @property int $enrollment_id
 * @property int $value
 * @property string $date
 * @property int|null $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Enrollment $enrollment
 * @property-read mixed $computed_status
 * @property-read mixed $date_for_humans
 * @property-read mixed $status_type_name
 * @property-read mixed $value_with_currency
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InvoiceDetail> $invoiceDetails
 * @property-read int|null $invoice_details_count
 * @property-read \App\Models\EnrollmentStatusType|null $statusType
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment status($status)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereEnrollmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduledPayment whereValue($value)
 */
	class ScheduledPayment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Scholarship
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Scholarship withoutTrashed()
 */
	class Scholarship extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\Skill
 *
 * @property int $id
 * @property string $name
 * @property int $default_weight
 * @property int $level_id
 * @property int $skill_type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $order
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read string $complete_name
 * @property-read \App\Models\Level $level
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\EvaluationType> $presets
 * @property-read int|null $presets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Skills\SkillEvaluation> $skillEvaluations
 * @property-read int|null $skill_evaluations_count
 * @property-read \App\Models\Skills\SkillType $skillType
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
	class Skill extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillEvaluation
 *
 * @property int|null $enrollment_id
 * @property int $skill_scale_id
 * @property int $skill_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Enrollment|null $enrollment
 * @property-read \App\Models\Skills\Skill|null $skill
 * @property-read \App\Models\Skills\SkillScale|null $skill_scale
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
	class SkillEvaluation extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillScale
 *
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
	class SkillScale extends \Eloquent {}
}

namespace App\Models\Skills{
/**
 * App\Models\Skills\SkillType
 *
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
	class SkillType extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Student
 *
 * @property int $id
 * @property string|null $idnumber
 * @property string|null $address
 * @property string|null $zip_code
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property int|null $gender_id
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Attendance> $attendance
 * @property-read int|null $attendance_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read string|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read string $firstname
 * @property-read string $formatted_gender
 * @property string|null $image
 * @property-read mixed $is_enrolled
 * @property-read mixed $student_age
 * @property-read mixed $student_birthdate
 * @property-read \App\Models\Institution|null $institution
 * @property-read string $lastname
 * @property-read \App\Models\LeadType|null $leadType
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PhoneNumber> $phone
 * @property-read int|null $phone_count
 * @property-read \App\Models\Profession|null $profession
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Student enrolled()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newInPeriod(int $periodId)
 * @method static \Illuminate\Database\Eloquent\Builder|Student newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Student query()
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereAccountHolder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereBic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereForceUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereGenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereHowDidYouKnowUs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereIdnumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereLeadTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student wherePriceCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereProfessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereTermsAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Student whereZipCode($value)
 */
	class Student extends \Eloquent implements \Spatie\MediaLibrary\HasMedia {}
}

namespace App\Models{
/**
 * App\Models\Tax
 *
 * @property int $id
 * @property string $name
 * @property string $value
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
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
	class Tax extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Teacher
 *
 * @property int $id
 * @property string|null $hired_at
 * @property string|null $max_week_hours
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Course> $courses
 * @property-read int|null $courses_count
 * @property-read string|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Event> $events
 * @property-read int|null $events_count
 * @property-read string $firstname
 * @property-read mixed $upcoming_leaves
 * @property-read string $lastname
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Leave> $leaves
 * @property-read int|null $leaves_count
 * @property-read string $name
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher query()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereHiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereMaxWeekHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Teacher withoutTrashed()
 */
	class Teacher extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string $username
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read mixed $force_update
 * @property-read string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \App\Models\Student|null $student
 * @property-read \App\Models\Teacher|null $teacher
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
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
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Year
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Period> $periods
 * @property-read int|null $periods_count
 * @method static \Illuminate\Database\Eloquent\Builder|Year newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Year query()
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Year whereName($value)
 */
	class Year extends \Eloquent {}
}

