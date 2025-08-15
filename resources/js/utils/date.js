/**
 * Format date to readable string
 * @param {string|Date} date
 * @param {string} locale
 * @returns {string}
 */
export function formatDate(date, locale = "en") {
  if (!date) return ""

  const dateObj = new Date(date)

  if (isNaN(dateObj.getTime())) {
    return ""
  }

  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }

  return dateObj.toLocaleDateString(locale === "ar" ? "ar-SA" : "en-US", options)
}

/**
 * Format date to short string (date only)
 * @param {string|Date} date
 * @param {string} locale
 * @returns {string}
 */
export function formatDateShort(date, locale = "en") {
  if (!date) return ""

  const dateObj = new Date(date)

  if (isNaN(dateObj.getTime())) {
    return ""
  }

  const options = {
    year: "numeric",
    month: "short",
    day: "numeric",
  }

  return dateObj.toLocaleDateString(locale === "ar" ? "ar-SA" : "en-US", options)
}

/**
 * Get relative time (e.g., "2 hours ago")
 * @param {string|Date} date
 * @param {string} locale
 * @returns {string}
 */
export function getRelativeTime(date, locale = "en") {
  if (!date) return ""

  const dateObj = new Date(date)
  const now = new Date()
  const diffInSeconds = Math.floor((now - dateObj) / 1000)

  if (diffInSeconds < 60) {
    return locale === "ar" ? "الآن" : "just now"
  }

  const diffInMinutes = Math.floor(diffInSeconds / 60)
  if (diffInMinutes < 60) {
    return locale === "ar"
      ? `منذ ${diffInMinutes} دقيقة${diffInMinutes > 1 ? "" : ""}`
      : `${diffInMinutes} minute${diffInMinutes > 1 ? "s" : ""} ago`
  }

  const diffInHours = Math.floor(diffInMinutes / 60)
  if (diffInHours < 24) {
    return locale === "ar"
      ? `منذ ${diffInHours} ساعة${diffInHours > 1 ? "" : ""}`
      : `${diffInHours} hour${diffInHours > 1 ? "s" : ""} ago`
  }

  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 30) {
    return locale === "ar"
      ? `منذ ${diffInDays} يوم${diffInDays > 1 ? "" : ""}`
      : `${diffInDays} day${diffInDays > 1 ? "s" : ""} ago`
  }

  return formatDateShort(date, locale)
}

/**
 * Check if date is today
 * @param {string|Date} date
 * @returns {boolean}
 */
export function isToday(date) {
  if (!date) return false

  const dateObj = new Date(date)
  const today = new Date()

  return dateObj.toDateString() === today.toDateString()
}

/**
 * Check if date is this week
 * @param {string|Date} date
 * @returns {boolean}
 */
export function isThisWeek(date) {
  if (!date) return false

  const dateObj = new Date(date)
  const today = new Date()
  const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000)

  return dateObj >= weekAgo && dateObj <= today
}

/**
 * Format date for input fields (YYYY-MM-DD)
 * @param {string|Date} date
 * @returns {string}
 */
export function formatDateForInput(date) {
  if (!date) return ""

  const dateObj = new Date(date)

  if (isNaN(dateObj.getTime())) {
    return ""
  }

  return dateObj.toISOString().split("T")[0]
}
