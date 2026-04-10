<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NouveauContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Contact $contact
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[EPV MAREL] Nouveau message : ' . $this->contact->sujet,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nouveau-contact',
        );
    }

    public function build(): self
    {
        return $this->subject('[EPV MAREL] Nouveau message : ' . $this->contact->sujet)
            ->html($this->renderHtml());
    }

    private function renderHtml(): string
    {
        $c = $this->contact;
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head><meta charset='UTF-8'><title>Nouveau message de contact</title></head>
        <body style='font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>
                <div style='background: #1B4F8A; color: white; padding: 24px 32px;'>
                    <h1 style='margin: 0; font-size: 20px;'>📩 Nouveau message de contact</h1>
                    <p style='margin: 8px 0 0; opacity: 0.85; font-size: 14px;'>Groupe Scolaire EPV MAREL</p>
                </div>
                <div style='padding: 32px;'>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 10px 0; color: #666; font-size: 13px; width: 30%;'>De :</td>
                            <td style='padding: 10px 0; font-weight: bold;'>{$c->nom}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px 0; color: #666; font-size: 13px;'>Email :</td>
                            <td style='padding: 10px 0;'><a href='mailto:{$c->email}' style='color: #1B4F8A;'>{$c->email}</a></td>
                        </tr>
                        <tr>
                            <td style='padding: 10px 0; color: #666; font-size: 13px;'>Téléphone :</td>
                            <td style='padding: 10px 0;'>{$c->telephone}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px 0; color: #666; font-size: 13px;'>Sujet :</td>
                            <td style='padding: 10px 0; font-weight: bold;'>{$c->sujet}</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px 0; color: #666; font-size: 13px; vertical-align: top;'>Message :</td>
                            <td style='padding: 10px 0;'></td>
                        </tr>
                    </table>
                    <div style='background: #f8f9fa; border-left: 4px solid #1B4F8A; padding: 16px; border-radius: 4px; margin-top: 8px;'>
                        <p style='margin: 0; line-height: 1.6;'>" . nl2br(htmlspecialchars($c->message)) . "</p>
                    </div>
                    <div style='margin-top: 24px; text-align: center;'>
                        <a href='" . config('app.url') . "/admin/contacts' 
                           style='background: #1B4F8A; color: white; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-size: 14px;'>
                            Voir dans le back-office
                        </a>
                    </div>
                </div>
                <div style='background: #f5f5f5; padding: 16px 32px; text-align: center; font-size: 12px; color: #999;'>
                    <p style='margin: 0;'>EPV MAREL — Cité Vision 2000, Angré 8è Tranche, Cocody — Abidjan, Côte d'Ivoire</p>
                    <p style='margin: 4px 0 0;'>Reçu le " . now()->format('d/m/Y à H:i') . "</p>
                </div>
            </div>
        </body>
        </html>";
    }
}