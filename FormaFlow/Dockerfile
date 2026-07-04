# Using a JDK 17 slim image as specified
FROM eclipse-temurin:17-jdk-jammy

WORKDIR /app

# Copy the Maven wrapper and `pom.xml` first to cache dependencies
COPY mvnw .
COPY .mvn .mvn
COPY pom.xml .

# NEW: Fix Windows line endings and ensure the script is executable
RUN sed -i 's/\r$//' mvnw
RUN chmod +x mvnw

# Download dependencies
RUN ./mvnw dependency:go-offline

# Copy the rest of the source code
COPY src src

# Run the Spring Boot application
CMD ["./mvnw", "spring-boot:run"]