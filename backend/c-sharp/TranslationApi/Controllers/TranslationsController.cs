using System.Security.Claims;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using TranslationApi.Data;

namespace TranslationApi.Controllers;

[Authorize(Roles = "translator")]
[ApiController]
[Route("api/translations")]
public class TranslationsController : ControllerBase
{
    private readonly AppDbContext _context;

    public TranslationsController(AppDbContext context)
    {
        _context = context;
    }

    // GET: api/translations
    [HttpGet]
    public async Task<IActionResult> Get() => Ok(await _context.Translations.ToListAsync());


    // GET: api/translations/{sid}/{langId}
    [HttpGet("{sid}/{langId}")]
    public async Task<IActionResult> Get(string sid, string langId)
    {
        var translation = await _context.Translations
            .FindAsync(sid, langId);

        if (translation == null)
            return NotFound();

        return Ok(translation);
    }

    // POST: api/translations
    [HttpPost]
    public async Task<IActionResult> Create(Translation translation)
    {
        _context.Translations.Add(translation);
        await _context.SaveChangesAsync();

        return CreatedAtAction(
            nameof(Get),
            new { sid = translation.SID, langId = translation.LangId },
            translation
        );
    }

    // PUT: api/translations/{sid}/{langId}
    [HttpPut("{sid}/{langId}")]
    public async Task<IActionResult> Update(
        string sid,
        string langId,
        Translation updated)
    {
        if (sid != updated.SID || langId != updated.LangId)
            return BadRequest();

        _context.Entry(updated).State = EntityState.Modified;
        await _context.SaveChangesAsync();

        return NoContent();
    }

    // DELETE: api/translations/{sid}/{langId}
    [HttpDelete("{sid}/{langId}")]
    public async Task<IActionResult> Delete(string sid, string langId)
    {
        var translation = await _context.Translations
            .FindAsync(sid, langId);

        if (translation == null)
            return NotFound();

        _context.Translations.Remove(translation);
        await _context.SaveChangesAsync();

        return NoContent();
    }

    [HttpGet("claims")]
    public IActionResult Claims()
    {
        return Ok(User.Claims.Select(c => new
        {
            Type = c.Type,
            Value = c.Value
        }));
    }
}
