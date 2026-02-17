public interface ITranslationService
{
    Task<List<Translation>> GetAllAsync();
    Task<Translation?> GetAsync(string sid, string langId);
    Task<Translation> CreateAsync(Translation translation);
    Task<Translation> CreateOrUpdateAsync(Translation translation);
    Task UpdateAsync(string sid, string langId, Translation updated);
    Task DeleteTranslationAsync(string sid, string langId);
    Task DeleteSidAsync(string sid);
}
