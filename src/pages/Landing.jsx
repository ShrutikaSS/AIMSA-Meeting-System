import { useEffect, useRef, useCallback, useState } from 'react'
import { Link } from 'react-router-dom'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import useReducedMotion from '../hooks/useReducedMotion'
import GradientButton from '../components/ui/GradientButton'
import GlassCard from '../components/ui/GlassCard'
import SectionHeading from '../components/ui/SectionHeading'
import { CollegeLogo, AimlLogo } from '../components/Logo'
import api from '../lib/axios'

gsap.registerPlugin(ScrollTrigger)

const committee = [
  { name: 'Dr. A. P. Patil', role: 'Faculty Coordinator', dept: 'AIML Dept.' },
  { name: 'Rohit Sharma', role: 'President', dept: 'BE AIML' },
  { name: 'Priya Deshmukh', role: 'Vice President', dept: 'BE AIML' },
  { name: 'Aniket Joshi', role: 'Tech Lead', dept: 'TE AIML' },
  { name: 'Neha Kulkarni', role: 'Event Head', dept: 'SE AIML' },
  { name: 'Siddharth Rao', role: 'PR Head', dept: 'TE AIML' },
]

const events = [
  { title: 'AI Hackathon 2026', date: 'Aug 15–16, 2026', tag: 'Upcoming' },
  { title: 'ML Workshop Series', date: 'Sep 5, 2026', tag: 'Upcoming' },
  { title: 'Tech Talk: LLMs in Production', date: 'Oct 2, 2026', tag: 'Upcoming' },
]

const achievements = [
  { title: '1st Place – Smart India Hackathon', team: 'Team NeuroNexus', year: '2026' },
  { title: 'Best Paper Award – ICML 2026', author: 'Priya Deshmukh', year: '2026' },
  { title: 'Top 5 – AI for Social Good', team: 'Team CogniCare', year: '2026' },
  { title: 'Patent Filed – Edge AI System', inventor: 'Dr. A. P. Patil', year: '2026' },
  { title: 'Google Summer of Code', student: 'Rohit Sharma', year: '2026' },
]

const gallery = [
  { label: 'AI Hackathon 2025' },
  { label: 'Freshers Welcome' },
  { label: 'Tech Talk Series' },
  { label: 'Industry Visit' },
]

function ParallaxLayer({ children, speed = 0.2, className = '' }) {
  const ref = useRef(null)
  const reduced = useReducedMotion()
  useEffect(() => {
    if (reduced || !ref.current) return
    const ctx = gsap.context(() => {
      gsap.to(ref.current, { y: () => window.innerHeight * speed * 0.3, ease: 'none', scrollTrigger: { trigger: ref.current.parentElement, start: 'top bottom', end: 'bottom top', scrub: true } })
    }, ref)
    return () => ctx.revert()
  }, [speed, reduced])
  return <div ref={ref} className={className} aria-hidden>{children}</div>
}

function TiltCard({ children, className = '' }) {
  const cardRef = useRef(null)
  const reduced = useReducedMotion()
  useEffect(() => {
    if (reduced || !cardRef.current) return
    const ctx = gsap.context(() => {
      gsap.from(cardRef.current, {
        opacity: 0, rotateX: 15, rotateY: -15, y: 60, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: cardRef.current, start: 'top 90%', toggleActions: 'play none none reverse' },
      })
    }, cardRef)
    return () => ctx.revert()
  }, [reduced])
  return <div ref={cardRef} className={`card-3d ${className}`}>{children}</div>
}

function Section({ id, children, className = '' }) {
  return <section id={id} className={`max-w-7xl mx-auto px-6 py-20 md:py-28 ${className}`}>{children}</section>
}

export default function Landing() {
  const reduced = useReducedMotion()
  const heroRef = useRef(null)
  const carouselRef = useRef(null)
  const achievementCarouselRef = useRef(null)
  const [liveAnnouncements, setLiveAnnouncements] = useState([])

  const setupCarousel = useCallback((containerRef) => {
    if (reduced || !containerRef.current) return
    const cards = containerRef.current.querySelectorAll('.carousel-card')
    const total = cards.length
    if (total === 0) return
    const ctx = gsap.context(() => {
      const wrap = containerRef.current
      const cardW = cards[0].offsetWidth || 280
      const gap = 24
      const step = cardW + gap
      const maxScroll = -(total * step - (wrap.parentElement.offsetWidth || 800) + step)

      gsap.set(cards, { transformOrigin: 'center center' })
      cards.forEach((card, i) => {
        const factor = i / (total - 1 || 1)
        gsap.set(card, { scale: 0.85, rotateY: 25, z: -80, opacity: 0.6, filter: 'brightness(0.7)' })
        ScrollTrigger.create({
          trigger: wrap,
          start: 'top 85%',
          end: 'bottom 15%',
          onUpdate: (self) => {
            const progress = self.progress
            const center = progress * (total - 1)
            const dist = Math.abs(i - center)
            const near = dist < 1.5
            const scale = near ? gsap.utils.clamp(0.85, 1.05, 1.05 - dist * 0.13) : 0.85
            const rotateY = near ? gsap.utils.clamp(-10, 10, (i - center) * -8) : (i < center ? 25 : -25)
            const z = near ? gsap.utils.clamp(-80, 0, -80 + dist * 55) : -80
            const opacity = near ? gsap.utils.clamp(0.6, 1, 1.05 - dist * 0.3) : 0.6
            gsap.to(card, { scale, rotateY, z, opacity, duration: 0.5, ease: 'power2.out', overwrite: 'auto' })
          },
        })
      })
      if (reduced) return
      gsap.to(wrap, {
        x: () => maxScroll,
        ease: 'none',
        scrollTrigger: { trigger: wrap.parentElement, start: 'top 20%', end: 'bottom 20%', scrub: 1.2, invalidateOnRefresh: true },
      })
    }, containerRef)
    return () => ctx.revert()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [reduced])

  useEffect(() => {
    if (reduced) return
    const ctx = gsap.context(() => {
      gsap.from('.about-card', {
        opacity: 0, rotateX: 10, y: 60, stagger: 0.2, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '#about', start: 'top 80%', toggleActions: 'play none none reverse' },
      })
      gsap.from('.vision-card', {
        opacity: 0, rotateY: -20, x: -80, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '.vision-card', start: 'top 85%', toggleActions: 'play none none reverse' },
      })
      gsap.from('.mission-card', {
        opacity: 0, rotateY: 20, x: 80, duration: 1, ease: 'power3.out',
        scrollTrigger: { trigger: '.mission-card', start: 'top 85%', toggleActions: 'play none none reverse' },
      })
      gsap.utils.toArray('.event-card').forEach((el, i) => {
        gsap.from(el, { opacity: 0, rotateX: 15, y: 50, duration: 0.8, delay: i * 0.15, ease: 'back.out(1.4)', scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none reverse' } })
      })
      gsap.utils.toArray('.gallery-item').forEach((el, i) => {
        gsap.from(el, { opacity: 0, scale: 0.8, rotate: i % 2 === 0 ? -3 : 3, duration: 0.7, delay: i * 0.12, ease: 'power3.out', scrollTrigger: { trigger: el, start: 'top 90%', toggleActions: 'play none none reverse' } })
      })
      gsap.utils.toArray('.announcement-item').forEach((el, i) => {
        gsap.from(el, { opacity: 0, x: -40, duration: 0.6, delay: i * 0.12, ease: 'power2.out', scrollTrigger: { trigger: el, start: 'top 92%', toggleActions: 'play none none reverse' } })
      })
    })
    return () => ctx.revert()
  }, [reduced])

  useEffect(() => {
    const c1 = setupCarousel(carouselRef)
    const c2 = setupCarousel(achievementCarouselRef)
    return () => { if (c1) c1(); if (c2) c2() }
  }, [setupCarousel])

  useEffect(() => {
    api.get('/announcements/public').then(({ data }) => {
      setLiveAnnouncements(data.announcements || [])
    }).catch(() => {})
  }, [])

  return (
    <div className="overflow-hidden">
      {/* ───── HERO ───── */}
      <section ref={heroRef} className="relative min-h-screen flex items-center justify-center overflow-hidden">
        <ParallaxLayer speed={-0.15} className="absolute inset-0 z-0 opacity-20">
          <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-accent rounded-full blur-[120px]" />
          <div className="absolute bottom-1/4 right-1/4 w-80 h-80 bg-amber-500 rounded-full blur-[100px]" />
        </ParallaxLayer>
        <div className="relative z-10 text-center px-6 max-w-4xl">
          <div className="flex items-center justify-center gap-3 sm:gap-4 mb-6">
            <CollegeLogo className="w-12 h-12 sm:w-16 sm:h-16" />
            <AimlLogo className="w-12 h-12 sm:w-16 sm:h-16" />
          </div>
          <p className="text-xs sm:text-sm font-semibold tracking-[0.2em] uppercase text-amber-400 mb-3">
            Zeal College of Engineering &amp; Research
          </p>
          <h1 className="text-4xl sm:text-6xl md:text-7xl font-extrabold leading-tight mb-4">
            <span className="text-gradient">AIMSA</span>
            <br />
            <span className="text-dark-title">AIML Student Association</span>
          </h1>
          <p className="text-sm sm:text-base md:text-lg text-dark-muted max-w-2xl mx-auto mb-8">
            Empowering students through innovation, collaboration, and excellence in Artificial Intelligence &amp; Machine Learning.
          </p>
          <Link to="/login">
            <GradientButton className="text-sm sm:text-base px-8 py-3">Login to Portal</GradientButton>
          </Link>
        </div>
        <div className="absolute bottom-8 left-1/2 -translate-x-1/2 z-10">
          <svg className="w-6 h-6 text-dark-subtle animate-bounce" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M12 5v14m0 0-4-4m4 4 4-4"/></svg>
        </div>
      </section>

      {/* ───── ABOUT ───── */}
      <Section id="about">
        <div className="grid md:grid-cols-2 gap-10 items-center">
          <div className="about-card space-y-4">
            <SectionHeading>About AIMSA</SectionHeading>
            <p className="text-dark-muted text-sm sm:text-base leading-relaxed">
              The AIML Student Association (AIMSA) is the official student body of the Department of Artificial
              Intelligence &amp; Machine Learning at Zeal College of Engineering &amp; Research. Founded to foster a
              culture of innovation, research, and collaboration, AIMSA organizes hackathons, workshops, tech talks,
              and community outreach programs that bridge academic learning with real-world impact.
            </p>
          </div>
          <div className="about-card glass p-6 glow-border text-sm text-dark-muted space-y-3">
            <p><strong className="text-dark-title">Established:</strong> 2023</p>
            <p><strong className="text-dark-title">Members:</strong> 200+</p>
            <p><strong className="text-dark-title">Events Conducted:</strong> 25+</p>
            <p><strong className="text-dark-title">Industry Partners:</strong> 8</p>
          </div>
        </div>
      </Section>

      {/* ───── VISION & MISSION ───── */}
      <Section id="vision-mission" className="bg-warm-alt/30">
        <div className="grid md:grid-cols-2 gap-8">
          <TiltCard className="vision-card">
            <GlassCard glow className="h-full">
              <h3 className="text-xl font-bold text-gradient mb-3">Vision</h3>
              <p className="text-dark-muted text-sm leading-relaxed">
                To be a leading student association that nurtures AI and ML talent, driving impactful
                innovation and ethical technology solutions for society.
              </p>
            </GlassCard>
          </TiltCard>
          <TiltCard className="mission-card">
            <GlassCard glow className="h-full">
              <h3 className="text-xl font-bold text-gradient mb-3">Mission</h3>
              <ul className="text-dark-muted text-sm space-y-2 list-disc list-inside">
                <li>Organize hands-on workshops and hackathons</li>
                <li>Foster industry-academia collaboration</li>
                <li>Encourage research publications and patents</li>
                <li>Build a community of lifelong learners</li>
              </ul>
            </GlassCard>
          </TiltCard>
        </div>
      </Section>

      {/* ───── EXECUTIVE COMMITTEE ───── */}
      <Section id="committee">
        <SectionHeading className="text-center mb-2">Executive Committee</SectionHeading>
        <p className="text-dark-muted text-sm text-center mb-10">Meet the team leading AIMSA this year</p>
        <div className="relative overflow-hidden" style={{ perspective: '1000px' }}>
          <div ref={carouselRef} className="flex gap-6 px-4 will-change-transform scrollbar-hide overflow-x-auto md:overflow-x-visible pb-4">
            {committee.map((m, i) => (
              <div key={i} className="carousel-card flex-shrink-0 w-64" style={{ transformStyle: 'preserve-3d' }}>
                <GlassCard className="text-center h-full">
                  <div className="w-14 h-14 mx-auto mb-3 rounded-full bg-gradient-accent flex items-center justify-center text-white font-bold text-lg">
                    {m.name.split(' ').map(s => s[0]).join('').slice(0, 2)}
                  </div>
                  <h4 className="text-dark-title font-semibold text-sm">{m.name}</h4>
                  <p className="text-amber-400 text-xs mt-0.5">{m.role}</p>
                  <p className="text-dark-subtle text-xs mt-1">{m.dept}</p>
                </GlassCard>
              </div>
            ))}
          </div>
        </div>
      </Section>

      {/* ───── UPCOMING EVENTS ───── */}
      <Section id="events" className="bg-warm-alt/30">
        <SectionHeading className="text-center mb-2">Upcoming Events</SectionHeading>
        <p className="text-dark-muted text-sm text-center mb-10">Mark your calendar</p>
        <div className="grid md:grid-cols-3 gap-6">
          {events.map((e, i) => (
            <div key={i} className="event-card">
              <GlassCard glow className="h-full flex flex-col">
                <span className="text-[10px] font-semibold text-blue-accent uppercase tracking-wider mb-2">{e.tag}</span>
                <h4 className="text-dark-title font-semibold text-sm mb-2">{e.title}</h4>
                <p className="text-dark-subtle text-xs mt-auto">{e.date}</p>
              </GlassCard>
            </div>
          ))}
        </div>
      </Section>

      {/* ───── RECENT ACHIEVEMENTS ───── */}
      <Section id="achievements">
        <SectionHeading className="text-center mb-2">Recent Achievements</SectionHeading>
        <p className="text-dark-muted text-sm text-center mb-10">Our members keep reaching new heights</p>
        <div className="relative overflow-hidden" style={{ perspective: '1000px' }}>
          <div ref={achievementCarouselRef} className="flex gap-6 px-4 will-change-transform scrollbar-hide overflow-x-auto md:overflow-x-visible pb-4">
            {achievements.map((a, i) => (
              <div key={i} className="carousel-card flex-shrink-0 w-72" style={{ transformStyle: 'preserve-3d' }}>
                <GlassCard glow className="h-full">
                  <div className="w-10 h-10 mb-3 rounded-full bg-amber-500/20 flex items-center justify-center">
                    <svg className="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" strokeWidth="2" viewBox="0 0 24 24"><path d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.36-8A7 7 0 1 0 8 15.93V19a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-3.07A6.97 6.97 0 0 0 19.36 9Z"/></svg>
                  </div>
                  <h4 className="text-dark-title font-semibold text-sm mb-1">{a.title}</h4>
                  <p className="text-dark-subtle text-xs">{a.team || a.author || a.student || a.inventor}</p>
                  <p className="text-dark-subtle text-xs mt-1">{a.year}</p>
                </GlassCard>
              </div>
            ))}
          </div>
        </div>
      </Section>

      {/* ───── PHOTO GALLERY ───── */}
      <Section id="gallery" className="bg-warm-alt/30">
        <SectionHeading className="text-center mb-2">Photo Gallery</SectionHeading>
        <p className="text-dark-muted text-sm text-center mb-10">Moments from our journey</p>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {gallery.map((g, i) => (
            <div key={i} className="gallery-item glass p-6 flex items-center justify-center h-32 sm:h-40 glow-border hover:bg-glass-hover transition-colors cursor-pointer">
              <span className="text-xs text-dark-subtle text-center">{g.label}</span>
            </div>
          ))}
        </div>
      </Section>

      {/* ───── ANNOUNCEMENTS ───── */}
      <Section id="announcements">
        <SectionHeading className="text-center mb-2">Announcements</SectionHeading>
        <p className="text-dark-muted text-sm text-center mb-10">Latest updates from AIMSA</p>
        <div className="max-w-3xl mx-auto space-y-4">
          {liveAnnouncements.length === 0 && <p className="text-dark-subtle text-sm text-center">No recent announcements.</p>}
          {liveAnnouncements.map((a, i) => (
            <div key={a._id || i} className="announcement-item glass px-5 py-4 flex items-start gap-3">
              <span className="w-2 h-2 mt-1.5 rounded-full bg-blue-accent shrink-0" />
              <div>
                <p className="text-sm font-semibold text-dark-title">{a.title}</p>
                {a.body && <p className="text-xs text-dark-muted mt-0.5">{a.body}</p>}
                <p className="text-[10px] text-dark-subtle mt-1">{new Date(a.createdAt).toLocaleDateString()}</p>
              </div>
            </div>
          ))}
        </div>
        <div className="text-center mt-6">
          <Link to="/announcements" className="text-xs text-blue-accent hover:underline no-underline">View all notices →</Link>
        </div>
      </Section>

      {/* ───── CONTACT / HELP DESK ───── */}
      <Section id="contact" className="bg-warm-alt/30">
        <div className="max-w-4xl mx-auto text-center">
          <SectionHeading className="text-center mb-4">Help Desk &amp; Contact</SectionHeading>
          <p className="text-dark-muted text-sm mb-8 max-w-2xl mx-auto">
            Have questions or need assistance? Reach out to the AIMSA team.
          </p>
          <div className="grid sm:grid-cols-3 gap-6 text-left">
            <GlassCard>
              <h4 className="text-dark-title font-semibold text-sm mb-1">Email Support</h4>
              <p className="text-dark-muted text-xs">support@aimsa.zeal.edu.in</p>
            </GlassCard>
            <GlassCard>
              <h4 className="text-dark-title font-semibold text-sm mb-1">Faculty Coordinator</h4>
              <p className="text-dark-muted text-xs">Dr. A. P. Patil</p>
              <p className="text-dark-subtle text-xs">ap.patil@zeal.edu.in</p>
            </GlassCard>
            <GlassCard>
              <h4 className="text-dark-title font-semibold text-sm mb-1">Student Coordinators</h4>
              <p className="text-dark-muted text-xs">Rohit Sharma</p>
              <p className="text-dark-subtle text-xs">rohit.sharma@aimsa.zeal.edu.in</p>
            </GlassCard>
          </div>
        </div>
      </Section>
    </div>
  )
}
