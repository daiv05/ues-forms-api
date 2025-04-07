<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReporteMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $viewName;
    public $subjectText;
    public $tableData;
    /**
     * Create a new message instance.
     */
    public function __construct($viewName, $tableData, $subjectText = '')
    {
        $this->viewName = $viewName;
        $this->subjectText = $subjectText ?? config('app.name');
        $this->tableData = $tableData;
    }

       /**
     * Obtener el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText
        );
    }

    /**
     * Obtener la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        // Usa la vista dinámica que se pasó al constructor
        return new Content(
            view: $this->viewName,  // Usa la propiedad viewName que contiene el nombre de la vista
            with: ['tableData' => $this->tableData]  // Pasa los datos de la tabla a la vista
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
