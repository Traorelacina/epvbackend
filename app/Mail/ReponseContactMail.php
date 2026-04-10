<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReponseContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Contact $contact,
        public readonly string $reponse
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->contact->sujet . ' — EPV MAREL',
        );
    }

    public function build(): self
    {
        return $this
            ->to($this->contact->email, $this->contact->nom)
            ->subject('Re: ' . $this->contact->sujet . ' — EPV MAREL')
            ->html($this->renderHtml());
    }

    private function renderHtml(): string
    {
        $c           = $this->contact;
        $reponseHtml = nl2br(htmlspecialchars($this->reponse));
        $messageOriginal = nl2br(htmlspecialchars($c->message));

        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head><meta charset='UTF-8'><title>Réponse de l'EPV MAREL</title></head>
        <body style='font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);'>
                <div style='background: #1B4F8A; color: white; padding: 24px 32px;'>
                    <h1 style='margin: 0; font-size: 20px;'>École Privée Laïque EPV MAREL</h1>
                    <p style='margin: 8px 0 0; opacity: 0.85; font-size: 14px;'>Réponse à votre message</p>
                </div>
                <div style='padding: 32px;'>
                    <p style='color: #333; font-size: 15px;'>Bonjour <strong>{$c->nom}</strong>,</p>
                    <p style='color: #333; font-size: 15px;'>Merci pour votre message. Voici notre réponse concernant votre sujet : <strong>{$c->sujet}</strong></p>
                    <div style='background: #f0f7ff; border-left: 4px solid #1B4F8A; padding: 20px; border-radius: 4px; margin: 24px 0;'>
                        <p style='margin: 0; line-height: 1.7; color: #333; font-size: 15px;'>{$reponseHtml}</p>
                    </div>
                    <p style='color: #555; font-size: 14px; margin-top: 24px;'>
                        Nous restons à votre disposition pour toute autre question.<br>
                        Cordialement,<br><br>
                        <strong>L'Administration — EPV MAREL</strong>
                    </p>
                    <hr style='border: none; border-top: 1px solid #eee; margin: 24px 0;'>
                    <p style='color: #999; font-size: 12px; font-style: italic;'>Votre message original :</p>
                    <div style='background: #f9f9f9; padding: 16px; border-radius: 4px; font-size: 13px; color: #666;'>
                        <p style='margin: 0; line-height: 1.6;'>{$messageOriginal}</p>
                    </div>
                </div>
                <div style='background: #1B4F8A; color: white; padding: 20px 32px; font-size: 13px;'>
                    <p style='margin: 0;'><strong>EPV MAREL</strong> — Cité Vision 2000, Angré 8è Tranche, Cocody</p>
                    <p style='margin: 6px 0 0; opacity: 0.8;'>
                        📞 +225 27 22 50 35 81 &nbsp;|&nbsp; +225 07 08 39 91 30<br>
                        📮 01 BP 1552 Abidjan 01 — Côte d'Ivoire
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }
}