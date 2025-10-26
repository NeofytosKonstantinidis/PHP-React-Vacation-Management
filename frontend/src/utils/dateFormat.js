/**
 * Format date to DD/MM/YYYY format
 * @param {string|Date} date - Date string or Date object
 * @returns {string} Formatted date string
 */
export const formatDate = (date) => {
  if (!date) return 'N/A'
  
  const d = new Date(date)
  if (isNaN(d.getTime())) return 'Invalid Date'
  
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  
  return `${day}/${month}/${year}`
}

/**
 * Format datetime to DD/MM/YYYY HH:MM format
 * @param {string|Date} date - Date string or Date object
 * @returns {string} Formatted datetime string
 */
export const formatDateTime = (date) => {
  if (!date) return 'N/A'
  
  const d = new Date(date)
  if (isNaN(d.getTime())) return 'Invalid Date'
  
  const day = String(d.getDate()).padStart(2, '0')
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const year = d.getFullYear()
  const hours = String(d.getHours()).padStart(2, '0')
  const minutes = String(d.getMinutes()).padStart(2, '0')
  
  return `${day}/${month}/${year} ${hours}:${minutes}`
}
