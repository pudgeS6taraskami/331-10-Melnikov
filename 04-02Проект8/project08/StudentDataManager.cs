using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;

namespace StudentManagement
{
    public class StudentDataManager
    {
        private string _filePath;
        private List<Student> _students;

        public StudentDataManager(string filePath = "students.json")
        {
            _filePath = filePath;
            _students = LoadStudents();
        }

        public List<Student> Students => _students;

        private List<Student> LoadStudents()
        {
            if (!File.Exists(_filePath))
                return new List<Student>();

            var json = File.ReadAllText(_filePath);
            return JsonSerializer.Deserialize<List<Student>>(json) ?? new List<Student>();
        }

        public void SaveStudents()
        {
            var json = JsonSerializer.Serialize(_students, new JsonSerializerOptions { WriteIndented = true });
            File.WriteAllText(_filePath, json);
        }

        public void AddStudent(Student student)
        {
            _students.Add(student);
        }

        public void RemoveStudent(Student student)
        {
            _students.Remove(student);
        }

        public void UpdateStudent(Student oldStudent, Student newStudent)
        {
            var index = _students.IndexOf(oldStudent);
            if (index != -1)
            {
                _students[index] = newStudent;
            }
        }

        public List<Student> FilterStudents(string course = null, string group = null, string lastName = null)
        {
            var query = _students.AsQueryable();

            if (!string.IsNullOrWhiteSpace(course) && int.TryParse(course, out int courseNum))
            {
                query = query.Where(s => s.Course == courseNum);
            }

            if (!string.IsNullOrWhiteSpace(group))
            {
                query = query.Where(s => s.Group.Contains(group, StringComparison.OrdinalIgnoreCase));
            }

            if (!string.IsNullOrWhiteSpace(lastName))
            {
                query = query.Where(s => s.LastName.Contains(lastName, StringComparison.OrdinalIgnoreCase));
            }

            return query.ToList();
        }

        public void ImportFromCsv(string filePath)
        {
            var lines = File.ReadAllLines(filePath).Skip(1); // Skip header
            foreach (var line in lines)
            {
                var values = line.Split(',');
                if (values.Length >= 7)
                {
                    var student = new Student
                    {
                        LastName = values[0],
                        FirstName = values[1],
                        MiddleName = values[2],
                        Course = int.Parse(values[3]),
                        Group = values[4],
                        DateOfBirth = DateTime.Parse(values[5]),
                        Email = values[6]
                    };
                    if (student.IsValid())
                    {
                        AddStudent(student);
                    }
                }
            }
        }

        public void ExportToCsv(string filePath)
        {
            var lines = new List<string>
            {
                "LastName,FirstName,MiddleName,Course,Group,DateOfBirth,Email"
            };

            lines.AddRange(_students.Select(s =>
                $"{s.LastName},{s.FirstName},{s.MiddleName},{s.Course},{s.Group},{s.DateOfBirth:dd.MM.yyyy},{s.Email}"));

            File.WriteAllLines(filePath, lines);
        }
    }
} 