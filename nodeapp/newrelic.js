'use strict'

/**
 * New Relic agent configuration.
 *
 * See lib/config.defaults.js in the agent distribution for a more complete
 * description of configuration variables and their potential values.
 */
exports.config = {
  /**
   * Array of application names.
   */
  app_name: ['My Application'],
  /**
   * Your New Relic license key.
   */
  license_key: 'd66a0549e4d3d13e0fcbf1469dc4b1b0ac9de438',
  logging: {
    /**
     * Level at which to log. 'trace' is most useful to New Relic when diagnosing
     * issues with the agent, 'info' and higher will impose the least overhead on
     * production applications.
     */
    level: 'info',
    transaction_tracer: {
      record_sql: 'raw'
    },
    slow_sql: true
  }
}
