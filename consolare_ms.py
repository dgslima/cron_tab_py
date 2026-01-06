import pyodbc


class ConsolareMS:
    def __init__(self):
        self.conn = None
        self.server = "consolaredb.consolare.com.br"
        self.database = "Consolare"
        self.username = "Integracao_Rastreamento"
        self.password = "f3R9K1pQ7B"

    def connection(self):
        """Establish connection to SQL Server database"""
        connection_string = (
            f"DRIVER={{ODBC Driver 17 for SQL Server}};"
            f"SERVER={self.server};"
            f"DATABASE={self.database};"
            f"UID={self.username};"
            f"PWD={self.password};"
            f"TrustServerCertificate=yes;"
        )
        self.conn = pyodbc.connect(connection_string)

    def close(self):
        """Close database connection"""
        if self.conn:
            self.conn.close()
            self.conn = None

    def get_new(self, start_date=None, end_date=None):
        """Get new records from database (last 90 days or specific date range)"""
        if start_date and end_date:
            # Query para período específico
            query = f"""
                SELECT * FROM VW_OBITOS_OS 
                WHERE _DADOSOS_DataUpdate >= '{start_date}' 
                AND _DADOSOS_DataUpdate <= '{end_date}'
                AND OSFechada='S'
            """
        else:
            # Query padrão para últimos 90 dias
            query = """
                SELECT * FROM VW_OBITOS_OS 
                WHERE _DADOSOS_DataUpdate >= DATEADD(DAY, -90, GETDATE()) 
                AND OSFechada='S'
            """
        cursor = self.conn.cursor()
        cursor.execute(query)
        
        # Get column names
        columns = [column[0] for column in cursor.description]
        
        # Fetch all results and convert to list of dictionaries
        results = []
        for row in cursor.fetchall():
            results.append(dict(zip(columns, row)))
        
        return results
