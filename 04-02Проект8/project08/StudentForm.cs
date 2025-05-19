using System;
using System.Drawing;
using System.Windows.Forms;

namespace StudentManagement
{
    public partial class StudentForm : Form
    {
        private Student _student;
        private ErrorProvider _errorProvider;

        public Student Student => _student;

        public StudentForm(Student student = null)
        {
            InitializeComponent();
            _student = student ?? new Student();
            _errorProvider = new ErrorProvider();
            InitializeControls();
            if (student != null)
            {
                LoadStudent();
            }
        }

        private void InitializeControls()
        {
            this.Text = _student == null ? "Добавить студента" : "Редактировать студента";
            this.Size = new Size(400, 500);
            this.FormBorderStyle = FormBorderStyle.FixedDialog;
            this.MaximizeBox = false;
            this.MinimizeBox = false;
            this.StartPosition = FormStartPosition.CenterParent;

            var panel = new TableLayoutPanel
            {
                Dock = DockStyle.Fill,
                Padding = new Padding(10),
                RowCount = 9,
                ColumnCount = 2
            };

            // Last Name
            panel.Controls.Add(new Label { Text = "Фамилия:" }, 0, 0);
            var lastNameBox = new TextBox { Name = "lastNameBox", Dock = DockStyle.Fill };
            lastNameBox.Validating += ValidateRequired;
            panel.Controls.Add(lastNameBox, 1, 0);

            // First Name
            panel.Controls.Add(new Label { Text = "Имя:" }, 0, 1);
            var firstNameBox = new TextBox { Name = "firstNameBox", Dock = DockStyle.Fill };
            firstNameBox.Validating += ValidateRequired;
            panel.Controls.Add(firstNameBox, 1, 1);

            // Middle Name
            panel.Controls.Add(new Label { Text = "Отчество:" }, 0, 2);
            var middleNameBox = new TextBox { Name = "middleNameBox", Dock = DockStyle.Fill };
            middleNameBox.Validating += ValidateRequired;
            panel.Controls.Add(middleNameBox, 1, 2);

            // Course
            panel.Controls.Add(new Label { Text = "Курс:" }, 0, 3);
            var courseBox = new TextBox { Name = "courseBox", Dock = DockStyle.Fill };
            courseBox.Validating += ValidateCourse;
            panel.Controls.Add(courseBox, 1, 3);

            // Group
            panel.Controls.Add(new Label { Text = "Группа:" }, 0, 4);
            var groupBox = new TextBox { Name = "groupBox", Dock = DockStyle.Fill };
            groupBox.Validating += ValidateRequired;
            panel.Controls.Add(groupBox, 1, 4);

            // Date of Birth
            panel.Controls.Add(new Label { Text = "Дата рождения:" }, 0, 5);
            var dateBox = new DateTimePicker
            {
                Name = "dateBox",
                Dock = DockStyle.Fill,
                Format = DateTimePickerFormat.Short,
                MinDate = new DateTime(1992, 1, 1),
                MaxDate = DateTime.Now
            };
            panel.Controls.Add(dateBox, 1, 5);

            // Email
            panel.Controls.Add(new Label { Text = "Email:" }, 0, 6);
            var emailBox = new TextBox { Name = "emailBox", Dock = DockStyle.Fill };
            emailBox.Validating += ValidateEmail;
            panel.Controls.Add(emailBox, 1, 6);

            // Buttons
            var buttonPanel = new FlowLayoutPanel
            {
                Dock = DockStyle.Fill,
                FlowDirection = FlowDirection.RightToLeft
            };

            var saveButton = new Button
            {
                Text = "Сохранить",
                DialogResult = DialogResult.OK
            };
            saveButton.Click += SaveButton_Click;

            var cancelButton = new Button
            {
                Text = "Отмена",
                DialogResult = DialogResult.Cancel
            };

            buttonPanel.Controls.Add(cancelButton);
            buttonPanel.Controls.Add(saveButton);
            panel.Controls.Add(buttonPanel, 1, 7);

            this.Controls.Add(panel);
            this.AutoValidate = AutoValidate.EnableAllowFocusChange;
        }

        private void LoadStudent()
        {
            var lastNameBox = (TextBox)Controls.Find("lastNameBox", true)[0];
            var firstNameBox = (TextBox)Controls.Find("firstNameBox", true)[0];
            var middleNameBox = (TextBox)Controls.Find("middleNameBox", true)[0];
            var courseBox = (TextBox)Controls.Find("courseBox", true)[0];
            var groupBox = (TextBox)Controls.Find("groupBox", true)[0];
            var dateBox = (DateTimePicker)Controls.Find("dateBox", true)[0];
            var emailBox = (TextBox)Controls.Find("emailBox", true)[0];

            lastNameBox.Text = _student.LastName;
            firstNameBox.Text = _student.FirstName;
            middleNameBox.Text = _student.MiddleName;
            courseBox.Text = _student.Course.ToString();
            groupBox.Text = _student.Group;
            dateBox.Value = _student.DateOfBirth;
            emailBox.Text = _student.Email;
        }

        private void SaveButton_Click(object sender, EventArgs e)
        {
            if (ValidateChildren())
            {
                var lastNameBox = (TextBox)Controls.Find("lastNameBox", true)[0];
                var firstNameBox = (TextBox)Controls.Find("firstNameBox", true)[0];
                var middleNameBox = (TextBox)Controls.Find("middleNameBox", true)[0];
                var courseBox = (TextBox)Controls.Find("courseBox", true)[0];
                var groupBox = (TextBox)Controls.Find("groupBox", true)[0];
                var dateBox = (DateTimePicker)Controls.Find("dateBox", true)[0];
                var emailBox = (TextBox)Controls.Find("emailBox", true)[0];

                _student.LastName = lastNameBox.Text;
                _student.FirstName = firstNameBox.Text;
                _student.MiddleName = middleNameBox.Text;
                _student.Course = int.Parse(courseBox.Text);
                _student.Group = groupBox.Text;
                _student.DateOfBirth = dateBox.Value;
                _student.Email = emailBox.Text;

                DialogResult = DialogResult.OK;
            }
            else
            {
                DialogResult = DialogResult.None;
            }
        }

        private void ValidateRequired(object sender, System.ComponentModel.CancelEventArgs e)
        {
            var textBox = (TextBox)sender;
            if (string.IsNullOrWhiteSpace(textBox.Text))
            {
                _errorProvider.SetError(textBox, "Это поле обязательно для заполнения");
                e.Cancel = true;
            }
            else
            {
                _errorProvider.SetError(textBox, "");
            }
        }

        private void ValidateCourse(object sender, System.ComponentModel.CancelEventArgs e)
        {
            var textBox = (TextBox)sender;
            if (!int.TryParse(textBox.Text, out int course) || course < 1)
            {
                _errorProvider.SetError(textBox, "Введите корректный номер курса");
                e.Cancel = true;
            }
            else
            {
                _errorProvider.SetError(textBox, "");
            }
        }

        private void ValidateEmail(object sender, System.ComponentModel.CancelEventArgs e)
        {
            var textBox = (TextBox)sender;
            var student = new Student { Email = textBox.Text };
            if (!student.IsValidEmail())
            {
                _errorProvider.SetError(textBox, "Введите корректный email (yandex.ru, gmail.com или icloud.com)");
                e.Cancel = true;
            }
            else
            {
                _errorProvider.SetError(textBox, "");
            }
        }
    }
} 