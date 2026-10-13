import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

interface Comision {
  vendedor: string;
  totalVendido: number;
  porcentaje: number;
  comision: number;
}

@Component({
  selector: 'app-root',
  imports: [CommonModule, FormsModule],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  fechaInicio = '';
  fechaFin = '';
  resultados: Comision[] = [];
  consultado = false;
  cargando = false;
  error = '';

  constructor(private http: HttpClient) {}

  calcular(): void {
    this.error = '';

    if (!this.fechaInicio || !this.fechaFin) {
      this.error = 'Selecciona la fecha de inicio y la fecha final';
      return;
    }

    this.cargando = true;

    this.http
      .get<Comision[]>('/api/comisiones', {
        params: { fechaInicio: this.fechaInicio, fechaFin: this.fechaFin }
      })
      .subscribe({
        next: (datos) => {
          this.resultados = datos;
          this.consultado = true;
          this.cargando = false;
        },
        error: (e) => {
          this.error = e.error?.error ?? 'No se pudo conectar con el servidor';
          this.resultados = [];
          this.consultado = false;
          this.cargando = false;
        }
      });
  }

  get totalComisiones(): number {
    return this.resultados.reduce((suma, fila) => suma + fila.comision, 0);
  }
}