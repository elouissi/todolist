FROM node:18-alpine

# Set working directory
WORKDIR /app

# Copy package files
COPY package*.json ./

# Clear npm cache and install dependencies
RUN npm cache clean --force && npm install --force

# Copy source code
COPY . .

# Expose port 3000
EXPOSE 3000

# Start the development server with hot reload
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"] 