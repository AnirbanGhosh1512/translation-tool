using Microsoft.EntityFrameworkCore;
using TranslationApi.Data;

/// <summary>
/// Service class that implements <see cref="ITranslationService"/> to manage translations.
/// Provides CRUD operations and upsert functionality for translations stored in the database.
/// </summary>
public class TranslationService : ITranslationService
{
    private readonly AppDbContext _context;

    /// <summary>
    /// Initializes a new instance of <see cref="TranslationService"/> with the given database context.
    /// </summary>
    /// <param name="context">The Entity Framework database context.</param>
    public TranslationService(AppDbContext context)
    {
        _context = context;
    }

    /// <summary>
    /// Retrieves all translations, ordered by SID and language ID.
    /// </summary>
    /// <returns>A list of all translations.</returns>
    public async Task<List<Translation>> GetAllAsync()
    {
        return await _context.Translations
            .OrderBy(t => t.SID)
            .ThenBy(t => t.LangId)
            .ToListAsync();
    }

    /// <summary>
    /// Retrieves a single translation by SID and language ID.
    /// </summary>
    /// <param name="sid">The unique identifier of the translation.</param>
    /// <param name="langId">The language code (e.g., "en", "de").</param>
    /// <returns>The translation if found, otherwise null.</returns>
    public async Task<Translation?> GetAsync(string sid, string langId)
    {
        return await _context.Translations.FindAsync(sid, langId);
    }

    /// <summary>
    /// Creates a new translation in the database.
    /// </summary>
    /// <param name="translation">The translation object to create.</param>
    /// <returns>The created translation.</returns>
    public async Task<Translation> CreateAsync(Translation translation)
    {
        _context.Translations.Add(translation);
        await _context.SaveChangesAsync();
        return translation;
    }

    /// <summary>
    /// Updates an existing translation identified by SID and language ID.
    /// </summary>
    /// <param name="sid">The SID of the translation to update.</param>
    /// <param name="langId">The language code of the translation to update.</param>
    /// <param name="updated">The translation object containing updated values.</param>
    /// <exception cref="ArgumentException">Thrown if SID or LangId of the updated object does not match parameters.</exception>
    public async Task UpdateAsync(string sid, string langId, Translation updated)
    {
        if (sid != updated.SID || langId != updated.LangId)
            throw new ArgumentException("SID or LangId mismatch");

        _context.Entry(updated).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    /// <summary>
    /// Deletes a specific translation identified by SID and language ID.
    /// </summary>
    /// <param name="sid">The SID of the translation to delete.</param>
    /// <param name="langId">The language code of the translation to delete.</param>
    /// <exception cref="KeyNotFoundException">Thrown if the translation does not exist.</exception>
    public async Task DeleteTranslationAsync(string sid, string langId)
    {
        var translation = await _context.Translations.FindAsync(sid, langId);

        if (translation == null)
            throw new KeyNotFoundException("Translation not found");

        _context.Translations.Remove(translation);
        await _context.SaveChangesAsync();
    }

    /// <summary>
    /// Deletes all translations for a given SID.
    /// ⭐ Important: This removes ALL translations associated with the SID.
    /// </summary>
    /// <param name="sid">The SID whose translations should be deleted.</param>
    /// <exception cref="KeyNotFoundException">Thrown if no translations exist for the SID.</exception>
    public async Task DeleteSidAsync(string sid)
    {
        var rows = await _context.Translations
            .Where(t => t.SID == sid)
            .ToListAsync();

        if (!rows.Any())
            throw new KeyNotFoundException("SID not found");

        _context.Translations.RemoveRange(rows);
        await _context.SaveChangesAsync();
    }

    /// <summary>
    /// Creates a new translation or updates an existing one if a translation
    /// with the same SID and language ID already exists.
    /// </summary>
    /// <param name="translation">The translation to create or update.</param>
    /// <returns>The created or updated translation.</returns>
    public async Task<Translation> CreateOrUpdateAsync(Translation translation)
    {
        // Check if a translation with the same SID + LangId exists
        var existing = await _context.Translations
            .FirstOrDefaultAsync(t => t.SID == translation.SID && t.LangId == translation.LangId);

        if (existing != null)
        {
            // Update existing record
            existing.Text = translation.Text;
            _context.Translations.Update(existing);
            await _context.SaveChangesAsync();
            return existing;
        }
        else
        {
            // Insert new record
            _context.Translations.Add(translation);
            await _context.SaveChangesAsync();
            return translation;
        }
    }
}
