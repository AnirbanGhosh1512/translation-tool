/// <summary>
/// Service interface for managing translations.
/// </summary>
public interface ITranslationService
{
    /// <summary>
    /// Retrieves all translations from the database.
    /// </summary>
    /// <returns>A list of all <see cref="Translation"/> objects.</returns>
    Task<List<Translation>> GetAllAsync();

    /// <summary>
    /// Retrieves a single translation by its SID and language ID.
    /// </summary>
    /// <param name="sid">The unique identifier of the translation.</param>
    /// <param name="langId">The language code (e.g., "en", "de").</param>
    /// <returns>The translation if found, otherwise null.</returns>
    Task<Translation?> GetAsync(string sid, string langId);

    /// <summary>
    /// Creates a new translation.
    /// </summary>
    /// <param name="translation">The translation object to create.</param>
    /// <returns>The created <see cref="Translation"/> object.</returns>
    Task<Translation> CreateAsync(Translation translation);

    /// <summary>
    /// Creates a new translation or updates the existing one if it already exists
    /// for the given SID and language ID.
    /// </summary>
    /// <param name="translation">The translation object to create or update.</param>
    /// <returns>The created or updated <see cref="Translation"/> object.</returns>
    Task<Translation> CreateOrUpdateAsync(Translation translation);

    /// <summary>
    /// Updates an existing translation identified by SID and language ID.
    /// </summary>
    /// <param name="sid">The unique identifier of the translation.</param>
    /// <param name="langId">The language code.</param>
    /// <param name="updated">The updated translation object.</param>
    Task UpdateAsync(string sid, string langId, Translation updated);

    /// <summary>
    /// Deletes a translation for a specific SID and language ID.
    /// </summary>
    /// <param name="sid">The unique identifier of the translation.</param>
    /// <param name="langId">The language code.</param>
    Task DeleteTranslationAsync(string sid, string langId);

    /// <summary>
    /// Deletes all translations associated with a specific SID.
    /// </summary>
    /// <param name="sid">The unique identifier of the translation group.</param>
    Task DeleteSidAsync(string sid);
}
