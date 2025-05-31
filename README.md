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

### Selfie Upload & Model Training
![User interface for uploading selfies to create AI headshots](/assets/create.webp)
- Upload 15 selfies for training data
- Uses `fal-ai/flux-lora-portrait-trainer` to create personalized AI models

### Personal Profile
![Form for entering personal details to improve AI headshot generation](/assets/profile.webp)
- Collects detailed personal information:
  - Age, ethnicity, height, weight
  - Body type, eye color, gender

### Style Creation
![Interface for selecting backdrops and outfits to create headshot styles](/assets/styles.webp)
- Combine professional backdrops and outfits

### Photo Generation & Download
- Uses `fal-ai/flux-lora` for high-quality headshots
- Generates 10 variations per style
- Batch download as ZIP archive
