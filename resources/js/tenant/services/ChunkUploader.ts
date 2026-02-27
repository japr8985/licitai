import api from '@tenant/api/axios';

export interface UploadOptions {
    endpoint?: string;
    chunkSize?: number; // Por defecto 2MB
    onProgress?: (percentage: number) => void;
}

export interface UploadResult {
    upload_uuid: string;
    total_chunks: number;
    file_name: string;
    file_size: number;
    file_type: string;
}

export class ChunkUploader {
    private file: File;
    private options: UploadOptions;
    private abortController: AbortController;
    private uploadUuid: string;

    constructor(file: File, options: UploadOptions = {}) {

        if (!file) {
            throw new Error("CHUNK_UPLOADER_ERROR: Constructor received null/undefined file");
        }
        this.file = file;
        this.options = {
            endpoint: '/upload/chunk', // Endpoint genérico por defecto
            chunkSize: 2 * 1024 * 1024, // 2MB
            ...options
        };
        this.abortController = new AbortController();
        this.uploadUuid = self.crypto.randomUUID();
    }

    /**
     * Inicia el proceso de carga fragmentada.
     */
    public async start(): Promise<UploadResult> {
        const totalChunks = Math.ceil(this.file.size / (this.options.chunkSize as number));

        for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
            // Verificar si se canceló la carga
            if (this.abortController.signal.aborted) {
                throw new Error('UPLOAD_ABORTED_BY_USER');
            }

            // Cortar el pastel (Chunking)
            const start = chunkIndex * (this.options.chunkSize as number);
            const end = Math.min(start + (this.options.chunkSize as number), this.file.size);
            const chunk = this.file.slice(start, end);

            // Preparar el Payload
            const formData = new FormData();
            formData.append('file', chunk);
            formData.append('upload_uuid', this.uploadUuid);
            formData.append('chunk_index', chunkIndex.toString());
            formData.append('total_chunks', totalChunks.toString()); // Útil para validación en backend

            // Enviar (Con soporte para cancelación)
            try {
                console.log(`Intentando subir chunk ${chunkIndex + 1}/${totalChunks}...`);

                const response = await api.post(this.options.endpoint, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });

                if (!response) {
                    throw new Error(`Servidor no devolvió respuesta en chunk ${chunkIndex}`);
                }

                console.log(`Servidor confirmó chunk ${chunkIndex}:`, response.data);

            } catch (e: any) {
                const errorToThrow = e || new Error("Error silencioso en la transferencia de bits.");
                console.error("EXCEPCIÓN EN EL BUCLE DE CHUNKS:", errorToThrow);
                throw errorToThrow;
            }
        }

        // Retornamos los metadatos necesarios para que el componente "finalice" el proceso
        return {
            upload_uuid: this.uploadUuid,
            total_chunks: totalChunks,
            file_name: this.file.name,
            file_size: this.file.size,
            file_type: this.file.type
        };
    }

    /**
     * Cancela la subida en curso.
     */
    public abort() {
        this.abortController.abort();
    }
}