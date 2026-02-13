using System.Security.Claims;
using System.Text.Json;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

[Authorize(AuthenticationSchemes = JwtBearerDefaults.AuthenticationScheme, Roles = "translator")]
[ApiController]
[Route("api/translations")]
public class TranslationsController : ControllerBase
{
    private readonly ITranslationService _service;

    public TranslationsController(ITranslationService service)
    {
        _service = service;
    }

    // GET: api/translations
    [HttpGet]
    public async Task<IActionResult> Get()
        => Ok(await _service.GetAllAsync());

    // GET: api/translations/{sid}/{langId}
    [HttpGet("{sid}/{langId}")]
    public async Task<IActionResult> Get(string sid, string langId)
    {
        var translation = await _service.GetAsync(sid, langId);
        return translation == null ? NotFound() : Ok(translation);
    }

    // POST: api/translations
    [HttpPost]
    public async Task<IActionResult> Create(Translation translation)
    {
        var created = await _service.CreateAsync(translation);

        return CreatedAtAction(
            nameof(Get),
            new { sid = created.SID, langId = created.LangId },
            created
        );
    }

    // PUT: api/translations/{sid}/{langId}
    [HttpPut("{sid}/{langId}")]
    public async Task<IActionResult> Update(
        string sid,
        string langId,
        Translation updated)
    {
        await _service.UpdateAsync(sid, langId, updated);
        return NoContent();
    }

    // DELETE: api/translations/{sid}/{langId}
    [HttpDelete("{sid}/{langId}")]
    public async Task<IActionResult> DeleteTranslation(string sid, string langId)
    {
        await _service.DeleteTranslationAsync(sid, langId);
        return NoContent();
    }

    // ⭐ DELETE SID (ALL LANGUAGES)
    [HttpDelete("{sid}")]
    public async Task<IActionResult> DeleteSid(string sid)
    {
        try
        {
            await _service.DeleteSidAsync(sid);
            return NoContent();
        }
        catch (KeyNotFoundException)
        {
            return NotFound();
        }
    }

    [HttpGet("claims")]
    public IActionResult Claims()
    {
        return Ok(User.Claims.Select(c => new
        {
            c.Type,
            c.Value
        }));
    }

    [Authorize]
    [HttpGet("debug")]
    public IActionResult Debug()
    {
        return Ok(User.Claims.Select(c => new
        {
            c.Type,
            c.Value
        }));
    }

    [HttpGet("debug-auth")]
    public IActionResult DebugAuth()
    {
        return Ok(new   
        {
            IsAuthenticated = User.Identity?.IsAuthenticated,
            Roles = User.Claims.Where(c => c.Type == ClaimTypes.Role).Select(c => c.Value)
        });
    }
}



