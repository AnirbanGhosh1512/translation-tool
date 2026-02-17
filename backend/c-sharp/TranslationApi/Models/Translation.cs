using System.ComponentModel.DataAnnotations;

/// <summary>
/// Represents a single translation for a specific SID and language.
/// </summary>
public class Translation
{
    /// <summary>
    /// The unique identifier for a translation group.
    /// Example: "001", "home_title", etc.
    /// This is required and cannot be empty.
    /// </summary>
    [Required]
    public string SID { get; set; } = string.Empty;

    /// <summary>
    /// The language code of the translation.
    /// Example: "en" for English, "de" for German, "fr" for French.
    /// This is required and cannot be empty.
    /// </summary>
    [Required]
    public string LangId { get; set; } = string.Empty;

    /// <summary>
    /// The translated text corresponding to the SID and language.
    /// This can be empty if no translation has been added yet.
    /// </summary>
    //[Required]  // Uncomment if you want to enforce non-empty text
    public string Text { get; set; } = string.Empty;
}

