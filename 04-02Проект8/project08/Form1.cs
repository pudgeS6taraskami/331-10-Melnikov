using System;
using System.Drawing;
using System.Windows.Forms;

namespace StudentManagement
{
    public partial class Form1 : Form
    {
        private StudentDataManager _dataManager;
        private bool _hasUnsavedChanges;

        public Form1()
        {
            InitializeComponent();
            _dataManager = new StudentDataManager();
            InitializeControls();
            LoadStudents();
        }

        private void InitializeControls()
        {
            // Main layout
            this.Text = "Управление студентами";
            this.Size = new Size(1000, 600);

            // Student list
            var studentList = new DataGridView
            {
                Name = "studentList",
                Dock = DockStyle.Fill,
                AllowUserToAddRows = false,
                SelectionMode = DataGridViewSelectionMode.FullRowSelect,
                MultiSelect = true,
                AutoSizeColumnsMode = DataGridViewAutoSizeColumnsMode.Fill
            };

            // Buttons panel
            var buttonsPanel = new FlowLayoutPanel
            {
                Dock = DockStyle.Top,
                Height = 40,
                Padding = new Padding(5)
            };

            var addButton = new Button
            {
                Text = "Добавить",
                Width = 100
            };
            addButton.Click += AddButton_Click;

            var editButton = new Button
            {
                Text = "Редактировать",
                Width = 100
            };
            editButton.Click += EditButton_Click;

            var deleteButton = new Button
            {
                Text = "Удалить",
                Width = 100
            };
            deleteButton.Click += DeleteButton_Click;

            var importButton = new Button
            {
                Text = "Импорт CSV",
                Width = 100
            };
            importButton.Click += ImportButton_Click;

            var exportButton = new Button
            {
                Text = "Экспорт CSV",
                Width = 100
            };
            exportButton.Click += ExportButton_Click;

            // Filter panel
            var filterPanel = new FlowLayoutPanel
            {
                Dock = DockStyle.Top,
                Height = 40,
                Padding = new Padding(5)
            };

            var courseFilter = new TextBox
            {
                PlaceholderText = "Курс",
                Width = 100
            };
            courseFilter.TextChanged += Filter_TextChanged;

            var groupFilter = new TextBox
            {
                PlaceholderText = "Группа",
                Width = 100
            };
            groupFilter.TextChanged += Filter_TextChanged;

            var lastNameFilter = new TextBox
            {
                PlaceholderText = "Фамилия",
                Width = 100
            };
            lastNameFilter.TextChanged += Filter_TextChanged;

            // Add controls
            buttonsPanel.Controls.AddRange(new Control[] { addButton, editButton, deleteButton, importButton, exportButton });
            filterPanel.Controls.AddRange(new Control[] { courseFilter, groupFilter, lastNameFilter });

            this.Controls.Add(studentList);
            this.Controls.Add(buttonsPanel);
            this.Controls.Add(filterPanel);

            // Set up columns
            studentList.Columns.AddRange(new[]
            {
                new DataGridViewTextBoxColumn { Name = "LastName", HeaderText = "Фамилия" },
                new DataGridViewTextBoxColumn { Name = "FirstName", HeaderText = "Имя" },
                new DataGridViewTextBoxColumn { Name = "MiddleName", HeaderText = "Отчество" },
                new DataGridViewTextBoxColumn { Name = "Course", HeaderText = "Курс" },
                new DataGridViewTextBoxColumn { Name = "Group", HeaderText = "Группа" },
                new DataGridViewTextBoxColumn { Name = "DateOfBirth", HeaderText = "Дата рождения" },
                new DataGridViewTextBoxColumn { Name = "Email", HeaderText = "Email" }
            });
        }

        private void LoadStudents()
        {
            var studentList = (DataGridView)Controls.Find("studentList", true)[0];
            studentList.Rows.Clear();

            foreach (var student in _dataManager.Students)
            {
                studentList.Rows.Add(
                    student.LastName,
                    student.FirstName,
                    student.MiddleName,
                    student.Course,
                    student.Group,
                    student.DateOfBirth.ToString("dd.MM.yyyy"),
                    student.Email
                );
            }
        }

        private void AddButton_Click(object sender, EventArgs e)
        {
            using (var form = new StudentForm())
            {
                if (form.ShowDialog() == DialogResult.OK)
                {
                    _dataManager.AddStudent(form.Student);
                    LoadStudents();
                    _hasUnsavedChanges = true;
                }
            }
        }

        private void EditButton_Click(object sender, EventArgs e)
        {
            var studentList = (DataGridView)Controls.Find("studentList", true)[0];
            if (studentList.SelectedRows.Count == 1)
            {
                var row = studentList.SelectedRows[0];
                var student = new Student
                {
                    LastName = row.Cells["LastName"].Value.ToString(),
                    FirstName = row.Cells["FirstName"].Value.ToString(),
                    MiddleName = row.Cells["MiddleName"].Value.ToString(),
                    Course = int.Parse(row.Cells["Course"].Value.ToString()),
                    Group = row.Cells["Group"].Value.ToString(),
                    DateOfBirth = DateTime.Parse(row.Cells["DateOfBirth"].Value.ToString()),
                    Email = row.Cells["Email"].Value.ToString()
                };

                using (var form = new StudentForm(student))
                {
                    if (form.ShowDialog() == DialogResult.OK)
                    {
                        _dataManager.UpdateStudent(student, form.Student);
                        LoadStudents();
                        _hasUnsavedChanges = true;
                    }
                }
            }
        }

        private void DeleteButton_Click(object sender, EventArgs e)
        {
            var studentList = (DataGridView)Controls.Find("studentList", true)[0];
            if (studentList.SelectedRows.Count > 0)
            {
                if (MessageBox.Show("Удалить выбранных студентов?", "Подтверждение", MessageBoxButtons.YesNo) == DialogResult.Yes)
                {
                    foreach (DataGridViewRow row in studentList.SelectedRows)
                    {
                        var student = new Student
                        {
                            LastName = row.Cells["LastName"].Value.ToString(),
                            FirstName = row.Cells["FirstName"].Value.ToString(),
                            MiddleName = row.Cells["MiddleName"].Value.ToString(),
                            Course = int.Parse(row.Cells["Course"].Value.ToString()),
                            Group = row.Cells["Group"].Value.ToString(),
                            DateOfBirth = DateTime.Parse(row.Cells["DateOfBirth"].Value.ToString()),
                            Email = row.Cells["Email"].Value.ToString()
                        };
                        _dataManager.RemoveStudent(student);
                    }
                    LoadStudents();
                    _hasUnsavedChanges = true;
                }
            }
        }

        private void ImportButton_Click(object sender, EventArgs e)
        {
            using (var dialog = new OpenFileDialog())
            {
                dialog.Filter = "CSV files (*.csv)|*.csv|All files (*.*)|*.*";
                if (dialog.ShowDialog() == DialogResult.OK)
                {
                    try
                    {
                        _dataManager.ImportFromCsv(dialog.FileName);
                        LoadStudents();
                        _hasUnsavedChanges = true;
                    }
                    catch (Exception ex)
                    {
                        MessageBox.Show($"Ошибка при импорте: {ex.Message}", "Ошибка", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }
                }
            }
        }

        private void ExportButton_Click(object sender, EventArgs e)
        {
            using (var dialog = new SaveFileDialog())
            {
                dialog.Filter = "CSV files (*.csv)|*.csv|All files (*.*)|*.*";
                if (dialog.ShowDialog() == DialogResult.OK)
                {
                    try
                    {
                        _dataManager.ExportToCsv(dialog.FileName);
                    }
                    catch (Exception ex)
                    {
                        MessageBox.Show($"Ошибка при экспорте: {ex.Message}", "Ошибка", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    }
                }
            }
        }

        private void Filter_TextChanged(object sender, EventArgs e)
        {
            var filterPanel = (FlowLayoutPanel)Controls.Find("filterPanel", false)[0];
            var courseFilter = (TextBox)filterPanel.Controls[0];
            var groupFilter = (TextBox)filterPanel.Controls[1];
            var lastNameFilter = (TextBox)filterPanel.Controls[2];

            var filteredStudents = _dataManager.FilterStudents(
                courseFilter.Text,
                groupFilter.Text,
                lastNameFilter.Text
            );

            var studentList = (DataGridView)Controls.Find("studentList", true)[0];
            studentList.Rows.Clear();

            foreach (var student in filteredStudents)
            {
                studentList.Rows.Add(
                    student.LastName,
                    student.FirstName,
                    student.MiddleName,
                    student.Course,
                    student.Group,
                    student.DateOfBirth.ToString("dd.MM.yyyy"),
                    student.Email
                );
            }
        }

        protected override void OnFormClosing(FormClosingEventArgs e)
        {
            if (_hasUnsavedChanges)
            {
                var result = MessageBox.Show(
                    "У вас есть несохраненные изменения. Сохранить перед выходом?",
                    "Подтверждение",
                    MessageBoxButtons.YesNoCancel
                );

                switch (result)
                {
                    case DialogResult.Yes:
                        _dataManager.SaveStudents();
                        break;
                    case DialogResult.Cancel:
                        e.Cancel = true;
                        break;
                }
            }

            base.OnFormClosing(e);
        }
    }
} 