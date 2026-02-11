using Microsoft.EntityFrameworkCore;
using TranslationApi.Data;

public class TranslationService : ITranslationService
{
    private readonly AppDbContext _context;

    public TranslationService(AppDbContext context)
    {
        _context = context;
    }

    public async Task<List<Translation>> GetAllAsync()
    {
        return await _context.Translations
            .OrderBy(t => t.SID)
            .ThenBy(t => t.LangId)
            .ToListAsync();
    }

    public async Task<Translation?> GetAsync(string sid, string langId)
    {
        return await _context.Translations.FindAsync(sid, langId);
    }

    public async Task<Translation> CreateAsync(Translation translation)
    {
        _context.Translations.Add(translation);
        await _context.SaveChangesAsync();
        return translation;
    }

    public async Task UpdateAsync(string sid, string langId, Translation updated)
    {
        if (sid != updated.SID || langId != updated.LangId)
            throw new ArgumentException("SID or LangId mismatch");

        _context.Entry(updated).State = EntityState.Modified;
        await _context.SaveChangesAsync();
    }

    public async Task DeleteTranslationAsync(string sid, string langId)
    {
        var translation = await _context.Translations.FindAsync(sid, langId);

        if (translation == null)
            throw new KeyNotFoundException("Translation not found");

        _context.Translations.Remove(translation);
        await _context.SaveChangesAsync();
    }

    // ⭐ IMPORTANT: Delete SID including ALL translations
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
}
