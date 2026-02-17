using Xunit;
using Moq;
using FluentAssertions;
using Microsoft.AspNetCore.Mvc;
using System.Collections.Generic;
using System.Threading.Tasks;

public class TranslationsControllerTests
{
    private readonly Mock<ITranslationService> _serviceMock;
    private readonly TranslationsController _controller;

    public TranslationsControllerTests()
    {
        _serviceMock = new Mock<ITranslationService>();
        _controller = new TranslationsController(_serviceMock.Object);
    }

    [Fact]
    public async Task Get_Should_Return_All_Translations()
    {
        var data = new List<Translation>
        {
            new Translation { SID = "HelloWorld", LangId = "en", Text = "Hello" }
        };

        _serviceMock.Setup(s => s.GetAllAsync()).ReturnsAsync(data);

        var result = await _controller.Get();

        var okResult = result as OkObjectResult;
        okResult.Should().NotBeNull();

        var value = okResult!.Value as List<Translation>;
        value!.Count.Should().Be(1);
    }

    [Fact]
    public async Task Get_By_Id_Should_Return_NotFound_When_Missing()
    {
        _serviceMock.Setup(s => s.GetAsync("HelloWorld", "en"))
            .ReturnsAsync((Translation?)null);

        var result = await _controller.Get("HelloWorld", "en");

        result.Should().BeOfType<NotFoundResult>();
    }

    [Fact]
    public async Task Create_Should_Return_Created()
    {
        var translation = new Translation
        {
            SID = "HalloWolt",
            LangId = "de",
            Text = "Hallo"
        };

        _serviceMock.Setup(s => s.CreateOrUpdateAsync(It.IsAny<Translation>()))
            .ReturnsAsync(translation);

        var result = await _controller.Create(translation);

        result.Should().BeOfType<CreatedAtActionResult>();
    }

    [Fact]
    public async Task Update_Should_Return_NoContent()
    {
        var translation = new Translation
        {
            SID = "HelloWorld",
            LangId = "en",
            Text = "Hi"
        };

        var result = await _controller.Update("HelloWorld", "en", translation);

        result.Should().BeOfType<NoContentResult>();
    }

    [Fact]
    public async Task DeleteTranslation_Should_Return_NoContent()
    {
        var result = await _controller.DeleteTranslation("HelloWorld4", "en");

        result.Should().BeOfType<NoContentResult>();
    }

    [Fact]
    public async Task DeleteSid_Should_Return_NoContent_When_Exists()
    {
        _serviceMock.Setup(s => s.DeleteSidAsync("HelloWorld5"))
            .Returns(Task.CompletedTask);

        var result = await _controller.DeleteSid("HelloWorld5");

        result.Should().BeOfType<NoContentResult>();
    }

    [Fact]
    public async Task DeleteSid_Should_Return_NotFound_When_Missing()
    {
        _serviceMock.Setup(s => s.DeleteSidAsync("HelloWorld6"))
            .Throws<KeyNotFoundException>();

        var result = await _controller.DeleteSid("HelloWorld6");

        result.Should().BeOfType<NotFoundResult>();
    }
}
