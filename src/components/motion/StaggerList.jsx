import { motion } from 'framer-motion'
import useReducedMotion from '../../hooks/useReducedMotion'

const container = {
  hidden: {},
  visible: {
    transition: { staggerChildren: 0.08 },
  },
}

const item = {
  hidden: { opacity: 0, y: 24 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.5, ease: 'easeOut' },
  },
}

const reducedContainer = {
  hidden: {},
  visible: { transition: { staggerChildren: 0 } },
}

const reducedItem = {
  hidden: { opacity: 1, y: 0 },
  visible: { opacity: 1, y: 0 },
}

export function StaggerContainer({ children, className, ...props }) {
  const prefersReduced = useReducedMotion()

  return (
    <motion.div
      variants={prefersReduced ? reducedContainer : container}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, margin: '-40px' }}
      className={className}
      {...props}
    >
      {children}
    </motion.div>
  )
}

export function StaggerItem({ children, className, ...props }) {
  const prefersReduced = useReducedMotion()

  return (
    <motion.div variants={prefersReduced ? reducedItem : item} className={className} {...props}>
      {children}
    </motion.div>
  )
}
