using Microsoft.AspNetCore.Mvc;

namespace TranslationApi.Controllers; // 👈 THIS MUST MATCH

[ApiController]
[Route("api/test")]
public class TestController : ControllerBase
{
    [HttpGet]
    public IActionResult Get()
    {
        return Ok("API is working");
    }

}
