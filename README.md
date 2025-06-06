# 🌍 AI-Powered Travel Planner ✈️

AI-Powered Travel Planner is an intelligent travel planning application that leverages artificial intelligence to create personalized travel itineraries tailored to your preferences, budget, and schedule.

## ✨ Features

- **🤖 AI-Generated Itineraries:** Create complete travel plans with just a few inputs about your destination, interests, and schedule
- **🎯 Personalized Recommendations:** Get suggestions for attractions, restaurants, and activities based on your preferences
- **📅 Interactive Timeline:** Visualize your trip day-by-day with an intuitive calendar interface
- **💰 Budget Management:** Track estimated costs and keep your travel expenses organized
- **🔔 Real-Time Updates:** Receive notifications about weather changes, attraction closures, or travel advisories
- **📤 Export & Share:** Download your itinerary as PDF or share it with travel companions

## 🛠️ Technology Stack

- **Backend:** PHP, Laravel
- **Frontend:** Blade templating engine, Tailwind CSS
- **AI:** OpenAI API integration
- **Database:** MySQL
- **Authentication:** Laravel Sanctum

## 📋 Installation & Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL
- OpenAI API key

## 🚀 Setup Instructions

1. **Clone the repository**
   ```
   git clone https://github.com/yourusername/travelmindai.git
   cd travelmindai
   ```

2. **Install PHP dependencies**
   ```
   composer install
   ```

3. **Install Node.js dependencies**
   ```
   npm install
   ```

4. **Create environment file**
   ```
   cp .env.example .env
   ```

5. **Configure your database and OpenAI API key in `.env`**
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=travelmindai
   DB_USERNAME=root
   DB_PASSWORD=

   OPENAI_API_KEY=your_openai_api_key
   ```

6. **Generate application key**
   ```
   php artisan key:generate
   ```

7. **Run database migrations**
   ```
   php artisan migrate
   ```

8. **Compile assets**
   ```
   npm run dev
   ```

9. **Start the development server**
   ```
   php artisan serve
   ```

## 🌟 Usage

1. Register or log in to your account
2. Create a new trip by specifying your destination, dates, and preferences
3. Let the AI generate your personalized itinerary
4. Review and customize your itinerary as needed
5. Export or share your finalized travel plan

## 🤝 Contributing

We welcome contributions from the community! Feel free to submit pull requests or open issues to help improve this project.

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- OpenAI for their powerful GPT models
- Laravel team for the excellent web framework
- All contributors who have helped make this project better
