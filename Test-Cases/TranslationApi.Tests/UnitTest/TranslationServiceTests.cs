namespace TranslationApi.Tests;

using Microsoft.EntityFrameworkCore;
using TranslationApi.Data;
using Xunit;
using FluentAssertions;

public class TranslationServiceTests
{
    private AppDbContext GetDbContext()
    {
        var options = new DbContextOptionsBuilder<AppDbContext>()
            .UseInMemoryDatabase(Guid.NewGuid().ToString())
            .Options;

        return new AppDbContext(options);
    }

    [Fact]
    public async Task CreateOrUpdate_Should_Insert_When_NotExists()
    {
        var context = GetDbContext();
        var service = new TranslationService(context);

        var translation = new Translation
        {
            SID = "HelloWorld",
            LangId = "en",
            Text = "Hello"
        };

        var result = await service.CreateOrUpdateAsync(translation);

        result.Should().NotBeNull();
        result.Text.Should().Be("Hello");

        context.Translations.Count().Should().Be(1);
    }

    [Fact]
    public async Task CreateOrUpdate_Should_Update_When_Exists()
    {
        var context = GetDbContext();
        var service = new TranslationService(context);

        context.Translations.Add(new Translation
        {
            SID = "HelloWorld",
            LangId = "en",
            Text = "Hello"
        });

        await context.SaveChangesAsync();

        var updated = new Translation
        {
            SID = "HelloWorld",
            LangId = "en",
            Text = "Hi"
        };

        var result = await service.CreateOrUpdateAsync(updated);

        result.Text.Should().Be("Hi");
        context.Translations.Count().Should().Be(1);
    }

    [Fact]
    public async Task GetAsync_Should_Return_Translation()
    {
        var context = GetDbContext();
        var service = new TranslationService(context);

        context.Translations.Add(new Translation
        {
            SID = "HalloWorld",
            LangId = "de",
            Text = "Hallo"
        });

        await context.SaveChangesAsync();

        var result = await service.GetAsync("HalloWorld", "de");

        result.Should().NotBeNull();
        result!.Text.Should().Be("Hallo");
    }

    [Fact]
    public async Task DeleteSid_Should_Remove_All_Languages()
    {
        var context = GetDbContext();
        var service = new TranslationService(context);

        context.Translations.AddRange(
            new Translation { SID = "HalloWorld3", LangId = "en", Text = "Hello" },
            new Translation { SID = "HalloWorld3", LangId = "de", Text = "Hallo" }
        );

        await context.SaveChangesAsync();

        await service.DeleteSidAsync("HalloWorld3");

        context.Translations.Count().Should().Be(0);
    }
}
