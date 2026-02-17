using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Design;
using TranslationApi.Data;

/// <summary>
/// Factory class to create instances of <see cref="AppDbContext"/> at design time.
/// </summary>
/// <remarks>
/// This is required by Entity Framework Core tools (like migrations) to create the DbContext
/// without needing to run the application. It reads configuration from appsettings.json
/// and configures the database connection.
/// </remarks>
public class AppDbContextFactory : IDesignTimeDbContextFactory<AppDbContext>
{
    /// <summary>
    /// Creates a new instance of <see cref="AppDbContext"/> using design-time configuration.
    /// </summary>
    /// <param name="args">Optional arguments provided by EF Core tools (not used here).</param>
    /// <returns>A fully configured <see cref="AppDbContext"/> instance.</returns>
    public AppDbContext CreateDbContext(string[] args)
    {
        // Build configuration from appsettings.json
        var configuration = new ConfigurationBuilder()
            .SetBasePath(Directory.GetCurrentDirectory())
            .AddJsonFile("appsettings.json")
            .Build();
                
        // Create a new DbContextOptionsBuilder for AppDbContext
        var optionsBuilder = new DbContextOptionsBuilder<AppDbContext>();

        // Configure the DbContext to use Postgres with the connection string from appsettings.json
        optionsBuilder.UseNpgsql(
            configuration.GetConnectionString("DefaultConnection")
        );

        // Return a new instance of AppDbContext with the configured options
        return new AppDbContext(optionsBuilder.Options);
    }
}
