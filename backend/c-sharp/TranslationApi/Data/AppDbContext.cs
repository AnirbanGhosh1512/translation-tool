using Microsoft.EntityFrameworkCore;

namespace TranslationApi.Data;

/// <summary>
/// Represents the Entity Framework Core database context for the Translation API.
/// Manages the database connection and the Translations table.
/// </summary>
public class AppDbContext : DbContext
{
    /// <summary>
    /// Initializes a new instance of <see cref="AppDbContext"/> with the given options.
    /// </summary>
    /// <param name="options">The DbContext options, including connection string and other configuration.</param>
    public AppDbContext(DbContextOptions<AppDbContext> options)
        : base(options) {}

    /// <summary>
    /// Represents the Translations table in the database.
    /// Provides CRUD operations on <see cref="Translation"/> entities.
    /// </summary>
    public DbSet<Translation> Translations => Set<Translation>();

    /// <summary>
    /// Configures the model (tables, keys, and columns) when EF Core creates the schema.
    /// </summary>
    /// <param name="modelBuilder">The <see cref="ModelBuilder"/> used to configure entity mappings.</param>
    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // Configure Translation entity
        modelBuilder.Entity<Translation>()
            .HasKey(t => new { t.SID, t.LangId }); // Composite primary key: SID + LangId

        modelBuilder.Entity<Translation>(entity =>
        {
            entity.ToTable("translations");   // ⭐ IMPORTANT: maps the Translation entity to "translations" table

            // Explicitly define the composite key again (optional but clearer)
            entity.HasKey(t => new { t.SID, t.LangId });

            // Map entity properties to database column names
            entity.Property(t => t.SID).HasColumnName("sid");
            entity.Property(t => t.LangId).HasColumnName("langid");
            entity.Property(t => t.Text).HasColumnName("text");
        });
    }
}
