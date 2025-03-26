import re

class StudentLoanCalculator:
    def __init__(self):
        # Universidades y carreras pre-definidas por nosotros
        self.university_majors = {
            #University of Michigan
            "University of Michigan (Ann Arbor)": [
                "Computer Science (College of Engineering)", "Computer Science (College of Literature, Science, and the Arts)",
                "Data Science (College of Engineering)", "Data Science (College of Literature, Science, and the Arts)",
                "Industrial & Operations Engineering", "Electrical Engineering", "Aerospace Engineering", "Biomedical Engineering", 
                "Chemical Engineering", "Robotics", "Materials Science & Engineering", "Mechanical Engineering", "Civil Engineering", 
                "Climate & Meteorology", "Computer Engineering", "Engineering Physics", "Environmental Engineering",
                "Naval Architecture & Marine Engineering", "Nuclear Engineering & Radiological Sciences", "Space Sciences & Engineering", 
                "Business Administration (Ross School of Business)", "Biology (Pre-Med)", "Biology, Health and Society (Pre-Med)", 
                "Biomolecular Science (Pre-Med)", "Biopsychology, Cognition, and Neuroscience (Pre-Med)", 
                "Cellular and Molecular Biomedical Science (Pre-Med)", "Molecular, Celullar, and Developmental Biology (Pre-Med)", 
                "Neuroscience (Pre-Med)", "Psychology (Pre-Med)"
            ],
            #University 2
            "University 2": [
                "Major1", "Major2", "Major3", "Major4", "Major5"
            ],
            #University 3
            "University 3": [
                "Major1", "Major2", "Major3", "Major4", "Major5"
            ],
            #University 4
            "University 4": [
                "Major1", "Major2", "Major3", "Major4", "Major5"
            ],
            #University 5
            "University 5": [
                "Major1", "Major2", "Major3", "Major4", "Major5"
            ],
            #University 6
            "University 6": [
                "Major1", "Major2", "Major3", "Major4", "Major5"
            ],
            #Otra (falta definir qué pasaría en este caso)
            "Other": ["Other"]
        }
        
        self.base_rate = 5.0  #Tasa de interés base (%)
        self.study_years = 4  #4 años de carrera

    def clean_currency_input(self, value):
        """Remove commas and dollar signs from input"""
        return float(re.sub(r'[^\d.]', '', str(value)))

    def get_user_input(self):
        """Collect all required user input"""
        print("=== Student Loan Calculator ===")
        
        #Selección de Universidad (con opciones definidas)
        print("\nSelect your university:")
        universities = list(self.university_majors.keys())
        for i, uni in enumerate(universities, 1):
            print(f"{i}. {uni}")
        
        uni_choice = int(input("Option (1-4): ")) - 1
        university = universities[uni_choice]
        
        #Elegir carrera (opciones pre-definidas)
        print(f"\nAvailable majors at {university}:")
        majors = self.university_majors[university]
        for i, major in enumerate(majors, 1):
            print(f"{i}. {major}")
        
        major_choice = int(input(f"Select your major (1-{len(majors)}): ")) - 1
        major = majors[major_choice]
        
        #Información financiera
        financial_aid = self.clean_currency_input(
            input("\nAnnual financial aid received (scholarships, etc.): $")
        )
        tuition = self.clean_currency_input(
            input("Annual tuition cost: $")
        )
        
        #Información académica
        print("\nGPA type:")
        gpa_type = input("1. High School GPA\n2. University GPA\nOption (1-2): ")
        gpa = float(input("GPA (0.0-4.0): "))
        
        #Work experience (me gustaría definir el algoritmo para esta sección)
        work_exp = input("\nRelevant work experience? (y/n): ").lower() == 'y'
        
        #Preguntarle al usuario cuándo va a querer empezar con el pago del tuition
        print("\nWhen will you make loan payments?")
        repayment_timing = input("1. Pay monthly while studying\n2. Pay after graduation\nOption (1-2): ")
        
        # Imprimiendo reporte para usuario
        return {
            'university': university,
            'major': major,
            'financial_aid': financial_aid,
            'tuition': tuition,
            'gpa_type': 'High School' if gpa_type == '1' else 'University',
            'gpa': gpa,
            'work_experience': work_exp,
            'repayment_timing': repayment_timing
        }

    def calculate_interest_rate(self, user_data):
        """Calculate estimated interest rate based on profile"""
        rate = self.base_rate
        
        #Algoritmo por universidades (faltaría definir)
        if "Michigan" in user_data['university']:
            rate -= 0.5  # Discount for UMich
        elif user_data['university'] == "Community College":
            rate += 1.0
        
        #Algoritmo por carrera (faltaría definir)
        if any(x in user_data['major'] for x in ["Engineering", "Computer Science", "Business"]):
            rate -= 1.0
        elif "Studies" in user_data['major']:
            rate += 0.5
        
        #Algoritmo por GPA (faltaría definir)
        if user_data['gpa'] >= 3.5:
            rate -= 1.0
        elif user_data['gpa'] < 2.5:
            rate += 1.5
        
        #Algoritmo por work experience (faltaría definir)
        if user_data['work_experience']:
            rate -= 0.5
        
        #Algoritmo por plan de pagos (faltaría definir)
        if user_data['repayment_timing'] == '1':
            rate -= 0.75
        
        return max(3.0, min(12.0, rate))  #Rango de interés que se le puede ofrecer al usuario (3%-12%)

    def calculate_total_loan(self, user_data):
        """Calculate total loan needed for 4 years"""
        annual_loan = max(0, user_data['tuition'] - user_data['financial_aid'])
        return annual_loan * self.study_years

    def calculate_repayment(self, total_loan, annual_rate, years=10):
        """Calculate monthly payment"""
        monthly_rate = annual_rate / 12 / 100
        num_payments = years * 12
        if monthly_rate == 0:
            return total_loan / num_payments
        payment = total_loan * monthly_rate * (1 + monthly_rate)**num_payments / ((1 + monthly_rate)**num_payments - 1)
        return payment

    def run(self):
        user_data = self.get_user_input()
        total_loan = self.calculate_total_loan(user_data)
        
        if total_loan <= 0:
            print("\n=== Results ===")
            print("No loan needed! Your financial aid covers your tuition.")
            return
            
        interest_rate = self.calculate_interest_rate(user_data)
        
        print("\n=== 4-Year Loan Estimate ===")
        print(f"University: {user_data['university']}")
        print(f"Major: {user_data['major']}")
        print(f"Total Loan Need (4 years): ${total_loan:,.2f}")
        print(f"Estimated Interest Rate: {interest_rate:.2f}%")
        
        if user_data['repayment_timing'] == '1':
            #Pagar mientras estudia
            monthly_payment = self.calculate_repayment(total_loan, interest_rate, self.study_years)
            print("\n=== Payment Plan While Studying ===")
            print(f"Monthly Payment During School: ${monthly_payment:,.2f}")
            print(f"Total Paid During School: ${monthly_payment * self.study_years * 12:,.2f}")
        else:
            #Pagar luego de graduarse
            monthly_payment = self.calculate_repayment(total_loan, interest_rate)
            total_interest = (monthly_payment * 10 * 12) - total_loan
            print("\n=== Payment Plan After Graduation ===")
            print(f"Monthly Payment After Graduation: ${monthly_payment:,.2f}")
            print(f"Total Interest Paid: ${total_interest:,.2f}")
            print(f"Total Paid Over 10 Years: ${monthly_payment * 10 * 12:,.2f}")

if __name__ == "__main__":
    calculator = StudentLoanCalculator()
    calculator.run()