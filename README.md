# Professional AI Headshot Generator

A Laravel-based application that uses AI to generate professional headshots from user selfies.

![Professional AI Headshot Generator](/assets/feature.webp)

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Set your FAL.AI API key: `FAL_API_KEY=your_api_key_here` in `.env`
6. Run migrations with `php artisan migrate`

**Note:** This project requires:
- Livewire Flux PRO components (paid license)
- FAL.AI API key for headshot generation

**AI Models:** Uses `fal-ai/flux-lora-portrait-trainer` and `fal-ai/flux-lora` from FAL.AI

**Authentication:** This project uses [WorkOS](https://workos.com/) for authentication. For configuration details, refer to the [Laravel documentation](https://laravel.com/docs/12.x/starter-kits#workos).

## Features

- **Selfie Upload**: Users upload selfies which are used as training data
- **Model Training**: Uses `fal-ai/flux-lora-portrait-trainer` to create personalized AI models
- **Personal Profile**: Collects personal details (age, ethnicity, height, weight, body type, eye color, gender, glasses) to improve generation quality
- **Style Creation**: Users can create styles combining backdrops and outfits
- **Photo Generation**: Uses `fal-ai/flux-lora` to generate professional headshots based on trained model and selected styles
- **Batch Download**: Generated photos can be downloaded as a ZIP archive
- **WorkOS Integration**: Secure authentication and session management
