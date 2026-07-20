import { motion } from 'framer-motion'
import useReducedMotion from '../../hooks/useReducedMotion'

export default function HoverTilt({ children, className, ...props }) {
  const prefersReduced = useReducedMotion()

  return (
    <motion.div
      whileHover={prefersReduced ? { scale: 1.01 } : { scale: 1.02, rotateX: 3, rotateY: 3 }}
      transition={{ type: 'spring', stiffness: 300, damping: 15 }}
      className={className}
      style={{ perspective: prefersReduced ? 'none' : 800 }}
      {...props}
    >
      {children}
    </motion.div>
  )
}
