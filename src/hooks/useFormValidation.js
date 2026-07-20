import { useState } from 'react'

export default function useFormValidation(initialValues, rules) {
  const [values, setValues] = useState(initialValues)
  const [errors, setErrors] = useState({})
  const [touched, setTouched] = useState({})

  const validate = (fieldValues = values) => {
    const errs = {}
    Object.keys(rules).forEach((key) => {
      const val = fieldValues[key]
      const fieldRules = rules[key]
      if (fieldRules.required && (!val || (typeof val === 'string' && !val.trim()))) {
        errs[key] = fieldRules.message || `${key} is required`
      }
      if (fieldRules.pattern && val && !fieldRules.pattern.test(val)) {
        errs[key] = fieldRules.patternMessage || `Invalid ${key}`
      }
      if (fieldRules.minLength && val && val.length < fieldRules.minLength) {
        errs[key] = fieldRules.minLengthMessage || `Minimum ${fieldRules.minLength} characters`
      }
    })
    return errs
  }

  const handleChange = (key, value) => {
    const updated = { ...values, [key]: value }
    setValues(updated)
    setTouched((prev) => ({ ...prev, [key]: true }))
    if (touched[key]) {
      const fieldErr = validate(updated)
      setErrors((prev) => ({
        ...prev,
        [key]: fieldErr[key] || null,
      }))
    }
  }

  const handleBlur = (key) => {
    setTouched((prev) => ({ ...prev, [key]: true }))
    const fieldErr = validate(values)
    setErrors((prev) => ({
      ...prev,
      [key]: fieldErr[key] || null,
    }))
  }

  const runValidation = () => {
    const errs = validate(values)
    setErrors(errs)
    setTouched(Object.keys(values).reduce((acc, k) => ({ ...acc, [k]: true }), {}))
    return Object.keys(errs).length === 0
  }

  const reset = (newValues) => {
    setValues(newValues || initialValues)
    setErrors({})
    setTouched({})
  }

  return { values, errors, touched, handleChange, handleBlur, runValidation, setValues, reset }
}
