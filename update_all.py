#!/usr/bin/env python3
"""
Script para atualizar registros existentes no MySQL
Força a atualização de todos os registros encontrados no SQL Server
"""

from datetime import datetime
import sys
import traceback
from consolare import Consolare
from consolare_ms import ConsolareMS
from consolare_mysql import ConsolareMysql


def log_data(message, log_type="INFO", file=__file__, line=0):
    """Log data with timestamp"""
    now = datetime.now()
    timestamp = now.strftime('%Y:%m:%d %H:%M:%S') + f"{now.microsecond // 1000:03d}"
    print(f"[{log_type}][{timestamp}][{file}][{line}]: {message}")


def main(start_date=None, end_date=None):
    log_data("ROTINA DE ATUALIZAÇÃO INICIADA")
    order_id = ''
    total_updated = 0
    total_errors = 0
    errors_list = []  # Lista para armazenar os erros
    
    try:
        # Buscar guias do SQL Server
        log_data("Conectando ao SQL Server...")
        consolare_ms = ConsolareMS()
        consolare_ms.connection()
        results = consolare_ms.get_new(start_date, end_date)
        consolare_ms.close()
        log_data(f"SQL Server conectado e {len(results)} registros encontrados")
        
        if not results:
            log_data("NENHUM REGISTRO ENCONTRADO")
            log_data("FIM DA ROTINA DE ATUALIZAÇÃO")
            return
        
        # Processar todos os registros
        num_order_arr = []
        consolare_data = []
        
        for result in results:
            consolare = Consolare(result)
            consolare_data.append(consolare)
            num_order_arr.append(consolare.dadosos_NumOrdem)
        
        log_data(f"ORDENS ENCONTRADAS - [{', '.join(map(str, num_order_arr))}]")
        
        # Conectar ao MySQL
        log_data("Conectando ao MySQL...")
        consolare_mysql = ConsolareMysql()
        consolare_mysql.connect()
        log_data("MySQL conectado")
        
        # Verificar quais já existem
        where = num_order_arr
        not_included = consolare_mysql.get_not_included(where, consolare_data)
        already_exists = len(consolare_data) - len(not_included)
        
        log_data(f"REGISTROS JÁ EXISTENTES: {already_exists}")
        log_data(f"REGISTROS NOVOS: {len(not_included)}")
        
        # Atualizar TODOS os registros (existentes e novos)
        log_data(f"\nINICIANDO ATUALIZAÇÃO DE {len(consolare_data)} REGISTROS...")
        
        for idx, consolare in enumerate(consolare_data, 1):
            order_id = consolare.dadosos_NumOrdem
            try:
                consolare_mysql.insert_guia(consolare)
                total_updated += 1
                status = "NOVO" if consolare in not_included else "ATUALIZADO"
                log_data(f"[{idx}/{len(consolare_data)}] {status} - Ordem: {order_id} - {consolare.falecido_Nome} (Versão: {consolare.versao_1})")
            except Exception as e:
                total_errors += 1
                tb = traceback.extract_tb(e.__traceback__)
                file = tb[-1].filename if tb else __file__
                line = tb[-1].lineno if tb else 0
                error_msg = str(e)
                errors_list.append({
                    'ordem': order_id,
                    'falecido': consolare.falecido_Nome,
                    'erro': error_msg,
                    'file': file,
                    'line': line
                })
                log_data(f"[{idx}/{len(consolare_data)}] ERRO na ordem {order_id}: {error_msg}", "ERROR", file, line)
        
        # Resumo
        log_data("\n" + "=" * 60)
        log_data("RESUMO DA ATUALIZAÇÃO")
        # Mostrar detalhes dos erros
        if errors_list:
            log_data("\n" + "=" * 60)
            log_data("DETALHES DOS ERROS")
            log_data("=" * 60)
            for idx, error in enumerate(errors_list, 1):
                log_data(f"\nERRO {idx}/{len(errors_list)}:")
                log_data(f"  Ordem: {error['ordem']}")
                log_data(f"  Falecido: {error['falecido']}")
                log_data(f"  Erro: {error['erro']}")
                log_data(f"  Arquivo: {error['file']}, Linha: {error['line']}")
            log_data("=" * 60)
        
        log_data("=" * 60)
        log_data(f"Total de registros processados: {len(consolare_data)}")
        log_data(f"Registros atualizados com sucesso: {total_updated}")
        log_data(f"Registros com erro: {total_errors}")
        log_data(f"Novos registros inseridos: {len(not_included)}")
        log_data(f"Registros atualizados (já existiam): {total_updated - len(not_included)}")
        log_data("=" * 60)
        
        log_data("FIM DA ROTINA DE ATUALIZAÇÃO")
    
    except Exception as e:
        # Handle any errors
        tb = traceback.extract_tb(e.__traceback__)
        file = tb[-1].filename if tb else __file__
        line = tb[-1].lineno if tb else 0
        log_data(f"[{order_id}] {str(e)}", "ERROR", file, line)
        log_data("FIM DA ROTINA DE ATUALIZAÇÃO (COM ERRO)")


if __name__ == "__main__":
    start_date = None
    end_date = None
    
    # Argumentos de linha de comando: python update_all.py "2024-01-01" "2024-01-31"
    if len(sys.argv) >= 3:
        start_date = sys.argv[1]
        end_date = sys.argv[2]
        log_data(f"Atualizando dados de {start_date} até {end_date}")
    
    main(start_date, end_date)
