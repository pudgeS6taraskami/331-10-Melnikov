using System;
using System.ComponentModel.DataAnnotations;
using System.Text.RegularExpressions;

namespace StudentManagement
{
    public class Student
    {
        public string LastName { get; set; }
        public string FirstName { get; set; }
        public string MiddleName { get; set; }
        public int Course { get; set; }
        public string Group { get; set; }
        public DateTime DateOfBirth { get; set; }
        public string Email { get; set; }

        public bool IsValidEmail()
        {
            if (string.IsNullOrWhiteSpace(Email))
                return false;

            var pattern = @"^[^@\s]+@(yandex\.ru|gmail\.com|icloud\.com)$";
            return Regex.IsMatch(Email, pattern, RegexOptions.IgnoreCase);
        }

        public bool IsValidDateOfBirth()
        {
            var minDate = new DateTime(1992, 1, 1);
            return DateOfBirth >= minDate && DateOfBirth <= DateTime.Now;
        }

        public bool IsValid()
        {
            return !string.IsNullOrWhiteSpace(LastName) &&
                   !string.IsNullOrWhiteSpace(FirstName) &&
                   !string.IsNullOrWhiteSpace(MiddleName) &&
                   Course > 0 &&
                   !string.IsNullOrWhiteSpace(Group) &&
                   IsValidEmail() &&
                   IsValidDateOfBirth();
        }
    }
} 