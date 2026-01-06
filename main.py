from datetime import datetime
import traceback
from consolare import Consolare
from consolare_ms import ConsolareMS
from consolare_mysql import ConsolareMysql


def log_data(message, log_type="INFO", file=__file__, line=0):
    """Log data with timestamp"""
    now = datetime.now()
    timestamp = now.strftime('%Y:%m:%d %H:%M:%S') + f"{now.microsecond // 1000:03d}"
    print(f"[{log_type}][{timestamp}][{file}][{line}]: {message}")


def main():
    log_data("ROUTINE STARTED")
    order_id = ''
    
    try:
        # Select novas guias
        consolare_ms = ConsolareMS()
        consolare_ms.connection()
        results = consolare_ms.get_new()
        consolare_ms.close()
        
        if results:
            num_order_arr = []
            consolare_data = []
            where = []
            
            for result in results:
                consolare = Consolare(result)
                consolare_data.append(consolare)
                num_order_arr.append(consolare.dadosos_NumOrdem)
                where.append(consolare.dadosos_NumOrdem)
            
            log_data(f"ORDERS FETCHED - [{', '.join(map(str, num_order_arr))}]")
            
            consolare_mysql = ConsolareMysql()
            consolare_mysql.connect()
            consolare_data = consolare_mysql.get_not_included(where, consolare_data)
            
            if len(consolare_data) > 0:
                new_consolare = [c.dadosos_NumOrdem for c in consolare_data]
                log_data(f"NEW ORDERS - [{', '.join(map(str, new_consolare))}]")
                
                for consolare in consolare_data:
                    order_id = consolare.dadosos_NumOrdem
                    consolare_mysql.insert_guia(consolare)
                    log_data(f"SUCCESSFUL ORDER INSERT - [{order_id}]")
            else:
                log_data("NO NEW RESULTS")
        else:
            log_data("NO RESULTS")
        
        log_data("END ROUTINE")
    
    except Exception as e:
        # Handle any errors
        tb = traceback.extract_tb(e.__traceback__)
        file = tb[-1].filename if tb else __file__
        line = tb[-1].lineno if tb else 0
        log_data(f"[{order_id}] {str(e)}", "ERROR", file, line)
        log_data("END ROUTINE")


if __name__ == "__main__":
    main()
