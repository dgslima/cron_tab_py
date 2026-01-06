import mysql.connector
import random
import string
from address import Address


class ConsolareMysql:
    def __init__(self):
        self.dns = "35.199.115.108"
        self.username = "root"
        self.password = "abc123**"
        self.database = "fvblocadora"
        self.conn = None

    def connect(self):
        """Establish connection to MySQL database"""
        self.conn = mysql.connector.connect(
            host=self.dns,
            user=self.username,
            password=self.password,
            database=self.database
        )

    def get_not_included(self, where, consolare_data):
        """Get records not yet included in database"""
        if not where:
            return consolare_data
        
        placeholders = ', '.join(['%s'] * len(where))
        query = f"""
            SELECT txt_numero_nota, versao 
            FROM guias_recolhimento gr 
            WHERE gr.txt_numero_nota IN ({placeholders}) 
            AND gr.hora_gravacao = (
                SELECT MAX(gr_inner.hora_gravacao) 
                FROM guias_recolhimento gr_inner 
                WHERE gr_inner.txt_numero_nota = gr.txt_numero_nota
            )
        """
        
        cursor = self.conn.cursor(dictionary=True, buffered=True)
        cursor.execute(query, where)
        results = cursor.fetchall()
        cursor.close()
        
        if results:
            filtered_data = []
            for data in consolare_data:
                exists = False
                for result in results:
                    if (data.dadosos_NumOrdem == result['txt_numero_nota'] and 
                        data.versao_1 == result['versao']):
                        exists = True
                        break
                if not exists:
                    filtered_data.append(data)
            return filtered_data
        
        return consolare_data

    def check_address(self, address, tipo):
        """Check if address exists in database, if not geocode and insert"""
        if address.nome is None or not address.cep:
            return {'lat': None, 'lng': None}
        
        formatted_cep = f"{str(address.cep)[:5]}-{str(address.cep)[5:]}"
        numero = str(address.numero).replace(' ', '')
        bairro = address.bairro or ""
        
        # Check if address exists
        query = "SELECT DISTINCT latitude, longitude FROM cadastro_enderecos WHERE cep = %s AND numero = %s"
        cursor = self.conn.cursor(dictionary=True, buffered=True)
        cursor.execute(query, (formatted_cep, numero))
        result = cursor.fetchone()
        cursor.close()
        
        if result and result.get("latitude") and result.get("longitude"):
            return {"lat": result["latitude"], "lng": result["longitude"]}
        
        # Geocode and insert
        positions = address.geocoding()
        user = "silvio@fvblocadora.com.br"
        
        query = """
            INSERT INTO cadastro_enderecos(
                tipo_local, usuario, descricao, endereco, cep, complemento,
                bairro, numero, latitude, longitude, data_hora
            )
            VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, NOW())
        """
        
        cursor = self.conn.cursor(buffered=True)
        cursor.execute(query, (
            tipo, user, address.nome, address.logradouro, formatted_cep,
            address.complemento, bairro, numero, str(positions['lat']), str(positions['lng'])
        ))
        self.conn.commit()
        cursor.close()
        
        return positions

    def check_urna(self, urna):
        """Check if urna exists, if not insert and return ID"""
        if not urna:
            return None
            
        query = "SELECT id FROM urnas WHERE descricao = %s"
        cursor = self.conn.cursor(buffered=True)
        cursor.execute(query, (urna.name,))
        result = cursor.fetchone()
        
        if result:
            cursor.close()
            return result[0]
        
        query = "INSERT INTO urnas(codigo, descricao) VALUES(%s, %s)"
        cursor.execute(query, (urna.id, urna.name))
        self.conn.commit()
        urna_id = cursor.lastrowid
        cursor.close()
        return urna_id

    def generate_family_url(self):
        """Generate random family URL code"""
        characters = string.ascii_uppercase
        
        while True:
            random_string = ''.join(random.choices(characters, k=12))
            query = 'SELECT code FROM guias_recolhimento WHERE code = %s'
            cursor = self.conn.cursor(buffered=True)
            cursor.execute(query, (random_string,))
            result = cursor.fetchone()
            cursor.close()
            
            if not result:
                break
        
        return random_string

    def _format_date_string(self, date_value):
        """Convert date value to string YYYY-MM-DD format"""
        if date_value is None:
            return None
        if hasattr(date_value, 'strftime'):
            return date_value.strftime('%Y-%m-%d')
        return str(date_value)[:10]
    
    def _format_datetime_string(self, date_value, time_value):
        """Convert date and time values to string YYYY-MM-DD HH:MM:SS format"""
        if date_value is None:
            return None
        date_str = self._format_date_string(date_value)
        time_str = time_value or '00:00'
        if isinstance(time_str, str) and len(time_str) >= 4:
            time_str = f"{time_str[:2]}:{time_str[2:4]}"
        return f"{date_str} {time_str}:00"

    def insert_guia(self, consolare):
        """Insert guia into database"""
        # Format datetime
        falecimento_datetime = self._format_datetime_string(
            consolare.falecido_DataFalecimento,
            consolare.falecido_HoraFalecimento
        )
        
        # Format agency name
        agencia = '%' + consolare.cabecalho_NomeFuneraria.replace("AGENCIA", "").replace("-", "").strip()
        
        # Format phone number
        numbers_fone = ''.join(filter(str.isdigit, consolare.contratante_Fone1 or ''))
        tel = f"({numbers_fone[:2]}){numbers_fone[2:7]}-{numbers_fone[7:11]}" if len(numbers_fone) >= 10 else None
        
        # Format dates and times
        vel_date = self._format_date_string(consolare.remocao_VelorioData)
        sep_date = self._format_date_string(consolare.sepultamento_Data)
        vel_hr = f"{str(consolare.remocao_VelorioHora)[:2]}:{str(consolare.remocao_VelorioHora)[2:4]}" if consolare.remocao_VelorioHora else None
        sep_hr = f"{str(consolare.sepultamento_Hora)[:2]}:{str(consolare.sepultamento_Hora)[2:4]}" if consolare.sepultamento_Hora else None
        
        # Generate family URL code
        code = self.generate_family_url()
        family_url = f"https://consolare.ossbrasil.com/view/auth.php?code={code}"
        
        # Check Urna
        urna_obj = consolare.find_item_with_urna()
        urna_id = self.check_urna(urna_obj)
        
        # Check Addresses
        hospital_address = Address(
            consolare.remocao_RemocaoNome, consolare.remocao_RemocaoCEP,
            consolare.remocao_RemocaoLogradouro, consolare.remocao_RemocaoNumero,
            consolare.remocao_RemocaoBairro, consolare.remocao_RemocaoComplemento,
            consolare.remocao_RemocaoCidade, consolare.remocao_RemocaoUF
        )
        hospital_lat_lng = self.check_address(hospital_address, "3")
        
        clinica_address = Address(
            consolare.remocao_LaboratorioNome, consolare.remocao_LaboratorioCEP,
            consolare.remocao_LaboratorioLogradouro, consolare.remocao_LaboratorioNumero,
            consolare.remocao_LaboratorioBairro, consolare.remocao_LaboratorioComplemento,
            consolare.remocao_LaboratorioCidade, consolare.remocao_LaboratorioUF
        )
        clinica_lat_lng = self.check_address(clinica_address, "4")
        
        funeral_address = Address(
            consolare.remocao_VelorioNome, consolare.remocao_VelorioCEP,
            consolare.remocao_VelorioLogradouro, consolare.remocao_VelorioNumero,
            consolare.remocao_VelorioBairro, consolare.remocao_VelorioComplemento,
            consolare.remocao_VelorioCidade, consolare.remocao_VelorioUF
        )
        funeral_lat_lng = self.check_address(funeral_address, "5")
        
        cemiterio_address = Address(
            consolare.sepultamento_Nome, consolare.sepultamento_Cep,
            consolare.sepultamento_Logradouro, consolare.sepultamento_Numero,
            consolare.sepultamento_Bairro, consolare.sepultamento_Complemento,
            consolare.sepultamento_Cidade, consolare.sepultamento_UF
        )
        cemiterio_lat_lng = self.check_address(cemiterio_address, "6")
        
        # Get agency ID
        cursor_agencia = self.conn.cursor(buffered=True)
        cursor_agencia.execute("SELECT id FROM agencias WHERE nome LIKE %s COLLATE utf8mb4_unicode_ci", (agencia,))
        agencia_result = cursor_agencia.fetchone()
        agencia_id = agencia_result[0] if agencia_result else None
        cursor_agencia.close()
        
        # Prepare strings
        hospital_string = hospital_address.to_string()
        clinica_string = clinica_address.to_string()
        funeral_string = funeral_address.to_string()
        cemiterio_string = cemiterio_address.to_string()
        
        # SQL query (36 INSERT params + 34 UPDATE params = 70)
        query = """
            INSERT INTO guias_recolhimento (
                flag, usuario_cadastro, hora_gravacao, id_contratacao, txt_numero_nota,
                nome_falecido, data_falecimento, code, url_familia, id_agencia, telefone, id_urna,
                desc_remocao, end_remocao_sel, rot_remocao_lat, rot_remocao_long,
                desc_clinica, end_clinica_sel, rot_clinica_lat, rot_clinica_long,
                desc_velorio, end_velorio_sel, rot_velo_lat, rot_velo_long,
                desc_cemiterio, end_cemiterio_sel, rot_cemi_lat, rot_cemi_long,
                data_saida_velorio, hora_saida_velorio, data_sepultamento_cremacao, hr_sepultamento_cremacao,
                tipo_contratacao, tipo_contratacao_spregula, duracao_velorio, data_hora_contratacao, tarifa, tipo_urna,
                dt_contratacao, obs, versao
            )
            VALUES (
                '1', 'SisFuner', NOW(),
                %s, %s, %s, %s, %s, %s, %s, %s, %s,
                %s, %s, %s, %s,
                %s, %s, %s, %s,
                %s, %s, %s, %s,
                %s, %s, %s, %s,
                %s, %s, %s, %s,
                %s, %s, %s, %s, %s, %s,
                %s, %s, %s
            )
            ON DUPLICATE KEY UPDATE
                data_retirada=CURDATE(),
                nome_falecido=%s, data_falecimento=%s, code=%s, url_familia=%s, id_agencia=%s,
                telefone=%s, id_urna=%s,
                desc_remocao=%s, end_remocao_sel=%s, rot_remocao_lat=%s, rot_remocao_long=%s,
                desc_clinica=%s, end_clinica_sel=%s, rot_clinica_lat=%s, rot_clinica_long=%s,
                desc_velorio=%s, end_velorio_sel=%s, rot_velo_lat=%s, rot_velo_long=%s,
                desc_cemiterio=%s, end_cemiterio_sel=%s, rot_cemi_lat=%s, rot_cemi_long=%s,
                data_saida_velorio=%s, hora_saida_velorio=%s, data_sepultamento_cremacao=%s, hr_sepultamento_cremacao=%s,
                tipo_contratacao=%s, tipo_contratacao_spregula=%s, duracao_velorio=%s, data_hora_contratacao=%s, tarifa=%s, tipo_urna=%s,
                dt_contratacao=%s, obs=%s, versao=%s
        """

        params = (
            # INSERT (36 params)
            consolare.falecido_RegistroPROAIM, consolare.dadosos_NumOrdem, consolare.falecido_Nome,
            falecimento_datetime, code, family_url, agencia_id, tel, urna_id,
            hospital_address.nome, hospital_string,
            str(hospital_lat_lng['lat']) if hospital_lat_lng['lat'] else None,
            str(hospital_lat_lng['lng']) if hospital_lat_lng['lng'] else None,
            clinica_address.nome, clinica_string,
            str(clinica_lat_lng['lat']) if clinica_lat_lng['lat'] else None,
            str(clinica_lat_lng['lng']) if clinica_lat_lng['lng'] else None,
            funeral_address.nome, funeral_string,
            str(funeral_lat_lng['lat']) if funeral_lat_lng['lat'] else None,
            str(funeral_lat_lng['lng']) if funeral_lat_lng['lng'] else None,
            cemiterio_address.nome, cemiterio_string,
            str(cemiterio_lat_lng['lat']) if cemiterio_lat_lng['lat'] else None,
            str(cemiterio_lat_lng['lng']) if cemiterio_lat_lng['lng'] else None,
            vel_date, vel_hr, sep_date, sep_hr,
            consolare.dadosos_TipoDeContratacao, consolare.dadosos_TipoDeContratacao_SPRegula,
            consolare.remocao_VelorioDuracao, consolare.dadosos_DataEmissao,
            consolare.itens_ValorTotal, (urna_obj.name if urna_obj else None),
            consolare.dadosos_DataEmissao, consolare.itens_Observacoes, consolare.versao_1,
            # UPDATE params
            consolare.falecido_Nome, falecimento_datetime, code, family_url, agencia_id, tel, urna_id,
            hospital_address.nome, hospital_string,
            str(hospital_lat_lng['lat']) if hospital_lat_lng['lat'] else None,
            str(hospital_lat_lng['lng']) if hospital_lat_lng['lng'] else None,
            clinica_address.nome, clinica_string,
            str(clinica_lat_lng['lat']) if clinica_lat_lng['lat'] else None,
            str(clinica_lat_lng['lng']) if clinica_lat_lng['lng'] else None,
            funeral_address.nome, funeral_string,
            str(funeral_lat_lng['lat']) if funeral_lat_lng['lat'] else None,
            str(funeral_lat_lng['lng']) if funeral_lat_lng['lng'] else None,
            cemiterio_address.nome, cemiterio_string,
            str(cemiterio_lat_lng['lat']) if cemiterio_lat_lng['lat'] else None,
            str(cemiterio_lat_lng['lng']) if cemiterio_lat_lng['lng'] else None,
            vel_date, vel_hr, sep_date, sep_hr,
            consolare.dadosos_TipoDeContratacao, consolare.dadosos_TipoDeContratacao_SPRegula,
            consolare.remocao_VelorioDuracao, consolare.dadosos_DataEmissao,
            consolare.itens_ValorTotal, (urna_obj.name if urna_obj else None),
            consolare.dadosos_DataEmissao, consolare.itens_Observacoes, consolare.versao_1
        )

        # Debug: validate placeholder/parameter count alignment during runtime
        try:
            q_before, q_after = query.split('ON DUPLICATE KEY UPDATE', 1)
            insert_placeholders = q_before.count('%s')
            update_placeholders = q_after.count('%s')
            total_placeholders = insert_placeholders + update_placeholders
            if total_placeholders != len(params):
                raise ValueError(f"Placeholder/params mismatch: placeholders={total_placeholders}, params={len(params)}")
        except Exception:
            pass

        cursor = self.conn.cursor(buffered=True)
        cursor.execute(query, params)
        self.conn.commit()
        cursor.close()
