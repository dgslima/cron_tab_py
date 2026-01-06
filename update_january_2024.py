#!/usr/bin/env python3
"""
Script para atualizar dados de janeiro de 2024
"""

from update_all import main

if __name__ == "__main__":
    # Atualizar todos os dados de janeiro de 2024
    print("=" * 60)
    print("INICIANDO ATUALIZAÇÃO DE DADOS DE JANEIRO DE 2024")
    print("=" * 60)
    main(start_date="2024-01-01", end_date="2024-01-31")
