import { Routes, Route } from 'react-router-dom'
import MainLayout from './layouts/MainLayout'
import Landing from './pages/Landing'
import Login from './pages/Login'
import ForgotPassword from './pages/ForgotPassword'
import ChangePassword from './pages/ChangePassword'
import ProtectedRoute from './components/auth/ProtectedRoute'
import RoleGuard from './components/auth/RoleGuard'
import AdministratorDashboard from './pages/dashboards/AdministratorDashboard'
import FacultyDashboard from './pages/dashboards/FacultyDashboard'
import PresidentDashboard from './pages/dashboards/PresidentDashboard'
import VicePresidentDashboard from './pages/dashboards/VicePresidentDashboard'
import CommitteeDashboard from './pages/dashboards/CommitteeDashboard'
import StudentDashboard from './pages/dashboards/StudentDashboard'
import MeetingsList from './pages/meetings/MeetingsList'
import MeetingDetail from './pages/meetings/MeetingDetail'
import CreateMeeting from './pages/meetings/CreateMeeting'
import EditMeeting from './pages/meetings/EditMeeting'
import EventsList from './pages/events/EventsList'
import EventDetail from './pages/events/EventDetail'
import CreateEvent from './pages/events/CreateEvent'
import EditEvent from './pages/events/EditEvent'
import MyRegistrations from './pages/events/MyRegistrations'
import Confirmation from './pages/events/Confirmation'
import AnnouncementsList from './pages/announcements/AnnouncementsList'
import CreateAnnouncement from './pages/announcements/CreateAnnouncement'
import EditAnnouncement from './pages/announcements/EditAnnouncement'
import CommitteeManagement from './pages/committee/CommitteeManagement'
import ApplyMembership from './pages/membership/ApplyMembership'
import MembershipStatus from './pages/membership/MembershipStatus'
import MemberManagement from './pages/membership/MemberManagement'
import MyAchievements from './pages/achievements/MyAchievements'
import AchievementReview from './pages/achievements/AchievementReview'
import MyCertificates from './pages/certificates/MyCertificates'
import IssueCertificate from './pages/certificates/IssueCertificate'
import NotificationsPage from './pages/notifications/NotificationsPage'
import GalleryPage from './pages/gallery/GalleryPage'
import ReportsPage from './pages/reports/ReportsPage'

export default function App() {
  return (
    <Routes>
      <Route element={<MainLayout />}>
        <Route path="/" element={<Landing />} />
        <Route path="/login" element={<Login />} />
        <Route path="/forgot-password" element={<ForgotPassword />} />

        <Route
          path="/change-password"
          element={
            <ProtectedRoute>
              <ChangePassword />
            </ProtectedRoute>
          }
        />

        {/* Meeting routes */}
        <Route
          path="/meetings"
          element={
            <ProtectedRoute>
              <MeetingsList />
            </ProtectedRoute>
          }
        />
        <Route
          path="/meetings/create"
          element={
            <ProtectedRoute>
              <CreateMeeting />
            </ProtectedRoute>
          }
        />
        <Route
          path="/meetings/:id"
          element={
            <ProtectedRoute>
              <MeetingDetail />
            </ProtectedRoute>
          }
        />
        <Route
          path="/meetings/:id/edit"
          element={
            <ProtectedRoute>
              <EditMeeting />
            </ProtectedRoute>
          }
        />

        {/* Event routes */}
        <Route
          path="/events"
          element={
            <ProtectedRoute>
              <EventsList />
            </ProtectedRoute>
          }
        />
        <Route
          path="/events/create"
          element={
            <ProtectedRoute>
              <CreateEvent />
            </ProtectedRoute>
          }
        />
        <Route
          path="/events/:id"
          element={
            <ProtectedRoute>
              <EventDetail />
            </ProtectedRoute>
          }
        />
        <Route
          path="/events/:id/edit"
          element={
            <ProtectedRoute>
              <EditEvent />
            </ProtectedRoute>
          }
        />

        {/* Registration routes */}
        <Route
          path="/registrations/my"
          element={
            <ProtectedRoute>
              <MyRegistrations />
            </ProtectedRoute>
          }
        />
        <Route
          path="/registrations/:id/confirmation"
          element={
            <ProtectedRoute>
              <Confirmation />
            </ProtectedRoute>
          }
        />

        {/* Announcement routes */}
        <Route
          path="/announcements"
          element={
            <ProtectedRoute>
              <AnnouncementsList />
            </ProtectedRoute>
          }
        />
        <Route
          path="/announcements/create"
          element={
            <ProtectedRoute>
              <CreateAnnouncement />
            </ProtectedRoute>
          }
        />
        <Route
          path="/announcements/:id/edit"
          element={
            <ProtectedRoute>
              <EditAnnouncement />
            </ProtectedRoute>
          }
        />

        {/* Achievement routes */}
        <Route
          path="/achievements"
          element={
            <ProtectedRoute>
              <MyAchievements />
            </ProtectedRoute>
          }
        />
        <Route
          path="/achievements/review"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President']}>
                <AchievementReview />
              </RoleGuard>
            </ProtectedRoute>
          }
        />

        {/* Certificate routes */}
        <Route
          path="/certificates"
          element={
            <ProtectedRoute>
              <MyCertificates />
            </ProtectedRoute>
          }
        />
        <Route
          path="/certificates/issue"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator', 'Association President', 'Vice President', 'Faculty Coordinator']}>
                <IssueCertificate />
              </RoleGuard>
            </ProtectedRoute>
          }
        />

        {/* Notification routes */}
        <Route
          path="/notifications"
          element={
            <ProtectedRoute>
              <NotificationsPage />
            </ProtectedRoute>
          }
        />

        {/* Gallery routes */}
        <Route
          path="/gallery"
          element={
            <ProtectedRoute>
              <GalleryPage />
            </ProtectedRoute>
          }
        />

        {/* Reports routes */}
        <Route
          path="/reports"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator', 'Faculty Coordinator', 'Association President', 'Vice President']}>
                <ReportsPage />
              </RoleGuard>
            </ProtectedRoute>
          }
        />

        {/* Committee routes */}
        <Route
          path="/committee"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator']}>
                <CommitteeManagement />
              </RoleGuard>
            </ProtectedRoute>
          }
        />

        {/* Membership routes */}
        <Route
          path="/membership/apply"
          element={
            <ProtectedRoute>
              <ApplyMembership />
            </ProtectedRoute>
          }
        />
        <Route
          path="/membership/status"
          element={
            <ProtectedRoute>
              <MembershipStatus />
            </ProtectedRoute>
          }
        />
        <Route
          path="/membership/manage"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator', 'Faculty Coordinator']}>
                <MemberManagement />
              </RoleGuard>
            </ProtectedRoute>
          }
        />

        <Route
          path="/dashboard/administrator"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Administrator']}>
                <AdministratorDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
        <Route
          path="/dashboard/faculty"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Faculty Coordinator']}>
                <FacultyDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
        <Route
          path="/dashboard/president"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['President/VP']}>
                <PresidentDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
        <Route
          path="/dashboard/vice-president"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['President/VP']}>
                <VicePresidentDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
        <Route
          path="/dashboard/committee"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Committee Member']}>
                <CommitteeDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
        <Route
          path="/dashboard/student"
          element={
            <ProtectedRoute>
              <RoleGuard allowedRoles={['Student Member']}>
                <StudentDashboard />
              </RoleGuard>
            </ProtectedRoute>
          }
        />
      </Route>
    </Routes>
  )
}
