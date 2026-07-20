import { motion } from 'framer-motion'
import useReducedMotion from '../../hooks/useReducedMotion'

export default function PageTransition({ children }) {
  const prefersReduced = useReducedMotion()

  return (
    <motion.div
      initial={prefersReduced ? {} : { opacity: 0, y: 20 }}
      animate={prefersReduced ? {} : { opacity: 1, y: 0 }}
      exit={prefersReduced ? {} : { opacity: 0, y: -20 }}
      transition={{ duration: 0.35, ease: 'easeInOut' }}
    >
      {children}
    </motion.div>
  )
}
