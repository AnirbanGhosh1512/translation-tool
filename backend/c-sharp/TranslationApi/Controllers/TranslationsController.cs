using System.Security.Claims;
using System.Text.Json;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

/// <summary>
/// API controller for managing translations.
/// </summary>
/// <remarks>
/// Requires JWT Bearer authentication and the "translator" role to access most endpoints.
/// Provides CRUD operations and utility endpoints for translation management.
/// </remarks>
[Authorize(AuthenticationSchemes = JwtBearerDefaults.AuthenticationScheme, Roles = "translator")]
[ApiController]
[Route("api/translations")]
public class TranslationsController : ControllerBase
{
    private readonly ITranslationService _service;

    /// <summary>
    /// Initializes a new instance of <see cref="TranslationsController"/>.
    /// </summary>
    /// <param name="service">The translation service for handling CRUD operations.</param>
    public TranslationsController(ITranslationService service)
    {
        _service = service;
    }

    /// <summary>
    /// Retrieves all translations from the database.
    /// GET: api/translations
    /// </summary>
    /// <returns>HTTP 200 OK with the list of translations.</returns>
    [HttpGet]
    public async Task<IActionResult> Get()
        => Ok(await _service.GetAllAsync());

    /// <summary>
    /// Retrieves a single translation by SID and language ID.
    /// GET: api/translations/{sid}/{langId}
    /// </summary>
    /// <param name="sid">The unique translation identifier.</param>
    /// <param name="langId">The language code (e.g., "en", "de").</param>
    /// <returns>HTTP 200 OK with translation if found; 404 Not Found otherwise.</returns>
    [HttpGet("{sid}/{langId}")]
    public async Task<IActionResult> Get(string sid, string langId)
    {
        var translation = await _service.GetAsync(sid, langId);
        return translation == null ? NotFound() : Ok(translation);
    }

    /// <summary>
    /// Creates a new translation or updates it if it already exists.
    /// POST: api/translations
    /// </summary>
    /// <param name="translation">The translation object to create or update.</param>
    /// <returns>HTTP 201 Created with the created or updated translation.</returns>
    [HttpPost]
    public async Task<IActionResult> Create(Translation translation)
    {
        if (string.IsNullOrWhiteSpace(translation.Text))
            translation.Text = $"Please add some text on \"{translation.LangId}\" in text area";

        var created = await _service.CreateOrUpdateAsync(translation);

        return CreatedAtAction(
            nameof(Get),
            new { sid = created.SID, langId = created.LangId },
            created
        );
    }

    /// <summary>
    /// Updates an existing translation identified by SID and language ID.
    /// PUT: api/translations/{sid}/{langId}
    /// </summary>
    /// <param name="sid">The translation SID to update.</param>
    /// <param name="langId">The language code of the translation to update.</param>
    /// <param name="updated">The updated translation object.</param>
    /// <returns>HTTP 204 No Content on success.</returns>
    [HttpPut("{sid}/{langId}")]
    public async Task<IActionResult> Update(
        string sid,
        string langId,
        Translation updated)
    {
        if (string.IsNullOrWhiteSpace(updated.Text))
            updated.Text = $"Please add some text on \"{updated.LangId}\" in text area";

        await _service.UpdateAsync(sid, langId, updated);
        return NoContent();
    }

    /// <summary>
    /// Deletes a specific translation identified by SID and language ID.
    /// DELETE: api/translations/{sid}/{langId}
    /// </summary>
    /// <param name="sid">The SID of the translation to delete.</param>
    /// <param name="langId">The language code of the translation to delete.</param>
    /// <returns>HTTP 204 No Content on success.</returns>
    [HttpDelete("{sid}/{langId}")]
    public async Task<IActionResult> DeleteTranslation(string sid, string langId)
    {
        await _service.DeleteTranslationAsync(sid, langId);
        return NoContent();
    }

    /// <summary>
    /// Deletes all translations for a given SID (all languages).
    /// DELETE: api/translations/{sid}
    /// </summary>
    /// <param name="sid">The SID whose translations should be deleted.</param>
    /// <returns>HTTP 204 No Content on success; 404 Not Found if SID does not exist.</returns>
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

    /// <summary>
    /// Returns all claims for the current authenticated user.
    /// GET: api/translations/claims
    /// </summary>
    /// <returns>HTTP 200 OK with a list of claims (type and value).</returns>
    [HttpGet("claims")]
    public IActionResult Claims()
    {
        return Ok(User.Claims.Select(c => new
        {
            c.Type,
            c.Value
        }));
    }

    /// <summary>
    /// Debug endpoint to view all claims for the current user.
    /// Requires the user to be authorized.
    /// GET: api/translations/debug
    /// </summary>
    /// <returns>HTTP 200 OK with claims.</returns>
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

    /// <summary>
    /// Debug endpoint to view authentication status and roles of the current user.
    /// GET: api/translations/debug-auth
    /// </summary>
    /// <returns>
    /// HTTP 200 OK with:
    /// - IsAuthenticated: whether the user is authenticated
    /// - Roles: list of role claims
    /// </returns>
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
