FROM python:3.9-slim

WORKDIR /app

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Para SQL Server no Linux
RUN apt-get update && apt-get install -y gnupg curl
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
RUN curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql17

COPY . .

# Para Cloud Scheduler HTTP trigger
EXPOSE 8080
ENV PORT=8080

CMD ["python", "-u", "main.py"]