using System.ComponentModel.DataAnnotations;

public class Translation
{
    [Required]
    public string SID { get; set; } = string.Empty;

    [Required]
    public string LangId { get; set; } = string.Empty;

    [Required]
    public string Text { get; set; } = string.Empty;
}
