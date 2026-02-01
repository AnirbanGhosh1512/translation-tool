using Microsoft.EntityFrameworkCore;

namespace TranslationApi.Data;

public class AppDbContext : DbContext
{
    public AppDbContext(DbContextOptions<AppDbContext> options)
        : base(options) {}

    public DbSet<Translation> Translations => Set<Translation>();

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<Translation>()
            .HasKey(t => new { t.SID, t.LangId });
    }
}
