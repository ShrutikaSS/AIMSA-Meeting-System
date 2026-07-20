import { motion } from 'framer-motion'
import useReducedMotion from '../../hooks/useReducedMotion'

const variants = {
  hidden: { opacity: 0, y: 40 },
  visible: (i = 0) => ({
    opacity: 1,
    y: 0,
    transition: { duration: 0.6, delay: i * 0.1, ease: 'easeOut' },
  }),
}

const reducedVariants = {
  hidden: { opacity: 1, y: 0 },
  visible: { opacity: 1, y: 0 },
}

export default function FadeIn({ children, index = 0, className, as: Tag = 'div', ...props }) {
  const prefersReduced = useReducedMotion()

  return (
    <motion.div
      custom={index}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, margin: '-40px' }}
      variants={prefersReduced ? reducedVariants : variants}
      className={className}
      {...props}
    >
      {children}
    </motion.div>
  )
}
